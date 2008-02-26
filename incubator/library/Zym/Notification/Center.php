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
 * @category   Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Notification
 */
require_once 'Zym/Notification.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Notification_Center
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
	 * A collection of observers that will receive all notifications
	 *
	 * @var array
	 */
	protected $_catchAllObservers = array();


	/**
	 * Singleton instance
	 *
	 * @var Zym_Notification_Center
	 */
	protected static $_instance;

    /**
     * Singleton getInstance()
     *
     * @return Zym_Notification_Center
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
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
     * Register an observer for the specified notification
     *
     * @param object $observer
     * @param string|array $events
     * @param string $callback
     */
    public static function attach($observer, $events = null, $callback = null)
    {
        $notificationCenter = self::getInstance();
        $notificationCenter->attachObserver($observer, $events, $callback);
    }

    /**
     * Register an observer to catch all notifications
     *
     * @param object $observer
     * @param string $callback
     */
    public static function attachCatchAll($observer, $callback = null)
    {
        $notificationCenter = self::getInstance();
        $notificationCenter->attachCatchAllObserver($observer, $callback);
    }

    /**
     * Remove an observer
     *
     * @param object $observer
     * @param string|array $event
     */
    public static function detach($observer, $events = null)
    {
        $notificationCenter = self::getInstance();
        $notificationCenter->detachObserver($observer, $events);
    }

    /**
     * Detach an observer from the catch all notifications
     *
     * @param object $observer
     */
    public static function detachCatchAll($observer)
    {
        $notificationCenter = self::getInstance();
        $notificationCenter->detachCatchAllObserver($observer);
    }

    /**
     * Post a notification
     *
     * @param string $name
     * @param object $sender
     * @param array $data
     */
    public static function post($name, $sender = null, array $data = array())
    {
        $notificationCenter = self::getInstance();
        $notificationCenter->postNotification($name, $sender, $data);
    }

    /**
     * Clear an event.
     * If no event is specified all events will be cleared.
     *
     * @param string $event
     */
    public static function reset($event = null)
    {
        $notificationCenter = self::getInstance();
        $notificationCenter->resetEvent($event);
    }

	/**
	 * Post a notification
	 *
	 * @param string $name
	 * @param object $sender
	 * @param array $data
	 * @return Zym_Notification_Center
	 */
	public function postNotification($name, $sender = null, array $data = array())
	{
	    $events = array_keys($this->_observers);

	    if (strpos($name, $this->_wildcard) !== false) {
	        $cleanName = str_ireplace($this->_wildcard, '', $name);

	        if (!empty($cleanName)) {
    	        foreach ($events as $event) {
    	        	if ($this->_checkWildcardEvents($event) || strpos($event, $cleanName) === 0) {
    	        	    $this->_post($event, $sender, $data);
    	        	}
    	        }
	        }
	    } else {
	        foreach ($events as $event) {
	        	if ($this->_checkWildcardEvents($event)) {
                    $this->_post($event, $sender, $data);
                }
	        }

	        $this->_post($name, $sender, $data);
	    }

	    if (isset($this->_observers[$this->_wildcard]) && !empty($this->_observers[$this->_wildcard])) {
	        $notification = new Zym_Notification($name, $sender, $data);

    	    foreach ($this->_observers[$this->_wildcard] as $observerData) {
    	    	$this->_postNotification($notification, $observerData);
    	    }
	    }

		return $this;
	}

	/**
	 * Check if the notification needs to be posted to other events.
	 *
	 * @return boolean
	 */
	protected function _checkWildcardEvents($event)
	{
	    return strpos($event, $this->_wildcard) !== false &&
               strpos($event, str_ireplace($this->_wildcard, '', $event)) === 0;
	}

	/**
	 * Post the notification
	 *
	 * @param Zym_Notification $notification
	 * @param array $observerData
	 */
	protected function _postNotification(Zym_Notification $notification, $observerData)
	{
	    $observer = $observerData[self::OBSERVER_KEY];
        $callback = $observerData[self::CALLBACK_KEY];

	    if ($observer instanceof Zym_Notification_Interface &&
            $callback == $this->_defaultCallback) {
            $observer->update($notification);
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

            $observer->$callback($notification);
        }
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
    	if ($this->eventIsRegistered($name)) {
            $notification = new Zym_Notification($name, $sender, $data);

            foreach ($this->_observers[$name] as $observerData) {
                $this->_postNotification($notification, $observerData);
            }
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
	 * @return Zym_Notification_Center
	 */
	public function attachObserver($observer, $events = null, $callback = null)
	{
	    if (!$events) {
	        $events = $this->_wildcard;
	    }

	    if (!$callback) {
            $callback = $this->_defaultCallback;
        }

	    $events = (array) $events;
	    $observerHash = spl_object_hash($observer);

	    foreach ($events as $event) {
            if (!array_key_exists($event, $this->_observers)) {
                $this->reset($event);
            }

            if (!array_key_exists($observerHash, $this->_observers[$event])) {
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
	 * @return Zym_Notification_Center
	 */
	public function detachObserver($observer, $events = null)
	{
        if (!$events) {
            $events = array_keys($this->_observers);
        } else {
            $events = (array) $events;
        }

        $observerHash = spl_object_hash($observer);

	    foreach ($events as $event) {
    	    if ($this->eventIsRegistered($event) &&
    	        array_key_exists($observerHash, $this->_observers[$event])) {
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
     * @return Zym_Notification_Center
	 */
	public function resetEvent($event = null)
	{
	    if (empty($event)) {
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
	public function eventIsRegistered($event)
	{
	    return array_key_exists($event, $this->_observers);
	}
}