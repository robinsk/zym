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
 * @see Zym_Notification_Registration
 */
require_once 'Zym/Notification/Registration.php';

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
     * The default callback method name
     *
     * @var string
     */
    protected $_defaultCallback = 'notify';

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
	 * Collection of available events.
	 *
	 * @var array
	 */
	protected $_events = array();

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
        return isset(self::$_instances[$namespace]);
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
        $toNotify = array();

        foreach ($this->_events as $event) {
            if ($event == $name || $event == $this->_wildcard) {
                $toNotify[] = $event;
            } else {
                if (strpos($event, $this->_wildcard) !== false) {
                    $cleanEvent = str_replace($this->_wildcard, '', $event);

                    if (strpos($event, $cleanEvent) === 0) {
                        $toNotify[] = $event;
                    }
                }
            }
        }

        $notification = new Zym_Notification_Message($name, $sender, $data);
        $notified = array();

        foreach ($toNotify as $event) {
            foreach ($this->_observers[$event] as $observerHash => $registration) {
                if (!isset($notified[$observerHash])) {
                    $notified[$observerHash] = $observerHash;

                    $observer = $registration->getObserver();
                    $callback = $registration->getCallback();

                    if ($observer instanceof Zym_Notification_Interface &&
                        $callback == $this->_defaultCallback) {

                        $observer->notify($notification);
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
            }
        }

		return $this;
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

            $this->_events[$event] = $event;

            if (!$this->hasObserver($observerHash, $event)) {
                $registration = new Zym_Notification_Registration($observer, $callback);

                $this->_observers[$event][$observerHash] = $registration;
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
            $events = $this->_events;
        } else {
            $events = (array) $events;
        }

        $observerHash = spl_object_hash($observer);

	    foreach ($events as $event) {
	        if ($this->isRegistered($event) &&
    	        $this->hasObserver($observerHash, $event)) {
    	        unset($this->_observers[$event][$observerHash]);

    	        if (empty($this->_observers[$event])) {
    	            unset($this->_events[$event]);
    	        }
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
	    return isset($this->_observers[$event]);
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
        if (!$this->isRegistered($event)) {
            return false;
        }

        if (is_object($observer)) {
            $observerHash = spl_object_hash($observer);
        } else {
            $observerHash = (string) $observer;
        }

        return isset($this->_observers[$event][$observerHash]);
    }
}