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
 * @package    Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zym_Notification
 */
require_once 'Zym/Notification.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
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
     * Constant for the default callback method name
     *
     */
    const DEFAULT_CALLBACK_METHOD = 'notify';

    /**
     * Constant for the catch-all event
     *
     */
    const WILDCARD = '*';

    /**
     * Exception for when the callback method isn't implemented in the target class
     *
     */
    const METHOD_NOT_IMPLEMENTED_EXCEPTION = 'Method "%s" is not implemented in class "%s"';

	/**
	 * The collection of objects that registered to notifications
	 *
	 * @var array
	 */
	protected $_observers = array();

	/**
	 * Singleton instance
	 *
	 * @var Zym_Notification_Center
	 */
	protected static $_instance;

	/**
	 * Singleton constructor
	 *
	 */
	protected function __construct()
	{
	}

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
	 * Post a notification
	 *
	 * @param string $name
	 * @param object $sender
	 * @param array $data
	 * @return Zym_Notification_Center
	 */
	public function post($name, $sender, array $data = array())
	{
	    if (strpos($name, self::WILDCARD) !== false) {
	        $name = str_ireplace(self::WILDCARD, '', $name);

	        if (!empty($name)) {
    	        $events = array_keys($this->_observers);

    	        foreach ($events as $event) {
    	        	if (strpos($event, $name) === 0) {
    	        	    $this->_post($event, $sender, $data);
    	        	}
    	        }
	        }
	    } else {
	        $this->_post($name, $sender, $data);
	    }

	    $this->_post(self::WILDCARD, $sender, $data);

		return $this;
	}

	/**
	 * Actually post the notification
	 *
	 * @throws Zym_Notification_Exception_MethodNotImplemented
	 * @param string $name
	 * @param object $sender
	 * @param array $data
	 * @return Zym_Notification_Center
	 */
	protected function _post($name, $sender, array $data = array())
	{
        if ($this->eventIsRegistered($name)) {
            $notification = new Zym_Notification($name, $sender, $data);

            foreach ($this->_observers[$name] as $observerData) {
                $observer = $observerData[self::OBSERVER_KEY];
                $callback = $observerData[self::CALLBACK_KEY];

                if (!method_exists($observer, $callback)) {
                    /**
                     * @see Zym_Notification_Exception_MethodNotImplemented
                     */
                    require_once 'Zym/Notification/Exception/MethodNotImplemented.php';

                    $message = sprintf(self::METHOD_NOT_IMPLEMENTED_EXCEPTION, $callback, get_class($observer));

                    throw new Zym_Notification_Exception_MethodNotImplemented($message);
                }

                $observer->$callback($notification);
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
	 * @return Zym_Notification_Center
	 */
	public function attach($observer, $events, $callback = self::DEFAULT_CALLBACK_METHOD)
	{
	    if (!is_array($events)) {
	        $events = (array) $events;
	    }

	    foreach ($events as $event) {
            if (!array_key_exists($event, $this->_observers)) {
                $this->reset($event);
            }

            if (!$this->eventHasObserver($observer, $event)) {
                $this->_observers[$event][] = array(self::OBSERVER_KEY => $observer,
                                                    self::CALLBACK_KEY => $callback);
            }
        }

        return $this;
	}

	/**
     * Attach the observer to the catch-all event
     *
     * @param object $observer
     * @param string $callback
     * @return Zym_Notification_Center
     */
	public function attachCatchAll($observer, $callback = self::DEFAULT_CALLBACK_METHOD)
	{
	    return $this->attach($observer, self::WILDCARD, $callback);
	}

	/**
	 * Remove an observer
	 *
	 * @param object $observer
	 * @param string|array $event
	 * @return Zym_Notification_Center
	 */
	public function detach($observer, $events = null)
	{
        if (empty($events)) {
            $events = array_keys($this->_observers);
        } else {
            $events = (array) $events;
        }

	    foreach ($events as $event) {
    	    if ($this->eventIsRegistered($event)) {
                $observerCount = count($this->_observers[$event]);

                for ($i = 0; $i < $observerCount; $i++) {
                    if ($this->_observers[$event][$i][self::OBSERVER_KEY] === $observer) {
                        unset($this->_observers[$event][$i]);
                        break;
                    }
                }
	       }
        }

	    return $this;
	}

	/**
	 * Detach the observer from the catch-all event
	 *
	 * @param object $observer
	 * @return Zym_Notification_Center
	 */
	public function detachCatchAll($observer)
	{
	    return $this->detach($observer, self::WILDCARD);
	}

	/**
	 * Clear an event.
	 * If no event is specified all events will be cleared.
	 *
	 * @param string $event
     * @return Zym_Notification_Center
	 */
	public function reset($event = null)
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

    /**
     * Check if an observer is already registered to an event.
     *
     * @param object $observer
     * @param string $event
     * @return boolean
     */
    public function eventHasObserver($observer, $event)
    {
        foreach ($this->_observers[$event] as $registeredObserver) {
            if ($registeredObserver[self::OBSERVER_KEY] === $observer) {
                return true;
            }
        }

        return false;
    }
}