<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Notification_Message
 */
require_once 'Zym/Notification/Message.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Notification
{
    /**
     * Constant for the observer key
     *
     */
    const OBSERVER_KEY = 'observer';

    /**
     * Constant for the callback key
     *
     */
    const CALLBACK_KEY = 'callback';

    /**
     * The default callback method name
     *
     * @var string
     */
    protected $_defaultCallback = 'update';

    /**
     * Wildcard for the catch-all event
     *
     * @var string
     */
    protected $_wildcard = '*';

	/**
	 * The collection of objects that registered to notifications
	 *
	 * @var array
	 */
	protected $_observers = array();

	/**
	 * Singleton instance
	 *
	 * @var array
	 */
	protected static $_instances = array();

    /**
     * Get a notification instance from the internal registry
     *
     * @param string name
     * @return Zym_Notification
     */
    public static function get($namespace = 'default')
    {
        if (!self::has($namespace)) {
            self::$_instances[$namespace] = new self();
        }

        return self::$_instances[$namespace];
    }

    /**
     * Remove a notification instance from the internal registry
     *
     * @param string $name
     */
    public static function remove($namespace)
    {
        if (self::has($namespace)) {
            unset(self::$_instances[$namespace]);
        }
    }

    /**
     * Check if the namespace is already set
     *
     * @return boolean
     */
    public static function has($namespace)
    {
        return array_key_exists($namespace, self::$_instances);
    }

	/**
	 * Singleton constructor
	 *
	 */
	protected function __construct()
	{
	}

	/**
	 * Get the wildcard
	 *
	 * @return string
	 */
	public function getWildcard()
	{
	    return $this->_wildcard;
	}

	/**
	 * Post a notification
	 *
	 * @param string $name
	 * @param object $sender
	 * @param array $data
	 * @return Zym_Notification
	 */
	public function post($name, $sender = null, array $data = array())
	{
	    $events = array_keys($this->_observers);
	    $hasWildcard = $this->_hasWildcard($name);
        $cleanName = $this->_removeWildcard($name);

        if (!$hasWildcard) {
            $this->_post($name, $sender, $data);
        }

	    foreach ($events as $event) {
	        $cleanEvent = $this->_removeWildcard($event);

	        if (($hasWildcard && strpos($cleanEvent, $cleanName) === 0) ||
	            ($this->_hasWildcard($event) && strpos($event, $cleanEvent) === 0)) {
                $this->_post($event, $sender, $data);
            }
        }

	    if ($this->isRegistered($this->_wildcard) &&
	        !empty($this->_observers[$this->_wildcard])) {
	        $notification = new Zym_Notification_Message($name, $sender, $data);

    	    foreach ($this->_observers[$this->_wildcard] as $observerData) {
    	    	$this->_postNotification($notification, $observerData);
       	    }
	    }

		return $this;
	}

	/**
	 * Check if the string contains a wildcard
	 *
	 * @param string $string
	 * @return boolean
	 */
	protected function _hasWildcard($string)
	{
	    return strpos($string, $this->_wildcard) !== false;
	}

	/**
	 * Remove the wildcard from the string
	 *
	 * @param string $string
	 * @return string
	 */
	protected function _removeWildcard($string)
	{
	    return str_ireplace($this->_wildcard, '', $string);
	}

    /**
     * Actually post the notification
     *
     * @throws Zym_Notification_Exception_MethodNotImplemented
     * @param string $name
     * @param object $sender
     * @param array $data
     */
    protected function _post($name, $sender = null, array $data = array())
    {
        if ($this->isRegistered($name)) {
            $notification = new Zym_Notification_Message($name, $sender, $data);

            foreach ($this->_observers[$name] as $observerData) {
                $this->_postNotification($notification, $observerData);
            }
        }
    }

	/**
	 * Post the notification
	 *
	 * @param Zym_Notification_Message $message
	 * @param array $observerData
	 */
	protected function _postNotification(Zym_Notification_Message $message, $observerData)
	{
	    $observer = $observerData[self::OBSERVER_KEY];
        $callback = $observerData[self::CALLBACK_KEY];

	    if ($observer instanceof Zym_Notification_Interface &&
            $callback == $this->_defaultCallback) {
            $observer->update($message);
        } else {
            if (!method_exists($observer, $callback)) {
                /**
                 * @see Zym_Notification_Exception_MethodNotImplemented
                 */
                require_once 'Zym/Notification/Exception/MethodNotImplemented.php';

                $message = sprintf('Method "%s" is not implemented in class "%s"',
                                   $callback, get_class($observer));

                throw new Zym_Notification_Exception_MethodNotImplemented($message);
            }

            $observer->$callback($message);
        }
	}

	/**
	 * Get an array with observer registration data
	 *
	 * @param object $observer
	 * @param string $callback
	 * @return array
	 */
    protected function _getObserverRegistration($observer, $callback)
    {
        return array(self::OBSERVER_KEY => $observer,
                     self::CALLBACK_KEY => $callback);
    }

	/**
	 * Register an observer for the specified notification
	 *
	 * @param object $observer
	 * @param string|array $events
	 * @param string $callback
	 * @return Zym_Notification
	 */
	public function attach($observer, $events = null, $callback = null)
	{
	    if (!$events) {
	        $events = array($this->_wildcard);
	    }

	    if (!$callback) {
            $callback = $this->_defaultCallback;
        }

	    $events = (array) $events;
	    $observerHash = spl_object_hash($observer);

	    foreach ($events as $event) {
            if (!$this->isRegistered($event)) {
                $this->reset($event);
            }

            if (!$this->hasObserver($observerHash, $event)) {
                $this->_observers[$event][$observerHash] = $this->_getObserverRegistration($observer, $callback);
            }
        }

        return $this;
	}

	/**
	 * Remove an observer
	 *
	 * @param object $observer
	 * @param string|array $event
	 * @return Zym_Notification
	 */
	public function detach($observer, $events = null)
	{
        if (!$events) {
            $events = array_keys($this->_observers);
        } else {
            $events = (array) $events;
        }

        $observerHash = spl_object_hash($observer);

	    foreach ($events as $event) {
    	    if ($this->isRegistered($event) &&
    	        $this->hasObserver($observerHash, $event)) {
    	        unset($this->_observers[$event][$observerHash]);
    	    }
        }

	    return $this;
	}

	/**
	 * Clear an event.
	 * If no event is specified all events will be cleared.
	 *
	 * @param string $event
     * @return Zym_Notification
	 */
	public function reset($event = null)
	{
	    if (!$event) {
	        $this->_observers = array();
        } else {
            $this->_observers[$event] = array();
        }

        return $this;
	}

	/**
	 * Check if an event is registered
	 *
	 * @param string $event
	 * @return boolean
	 */
	public function isRegistered($event)
	{
	    return array_key_exists($event, $this->_observers);
	}

    /**
     * Check if the observer is registered for the specified event
     *
     * @param object|string &$observer either spl_object_hash or the object itself
     * @param string $event
     * @return boolean
     */
    public function hasObserver(&$observer, $event)
    {
        if (is_object($observer)) {
            $observerHash = spl_object_hash($observer);
        } else if (is_string($observer)) {
            $observerHash = $observer;
        } else {
            return false;
        }
        
        return array_key_exists($observerHash, $this->_observers[$event]);
    }
}