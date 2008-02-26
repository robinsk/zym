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
 * @package    Zym_Calendar
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Calendar
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Calendar
{
    /**
     * The current day
     *
     * @var Zend_Date
     */
    protected $_activeDate;

    /**
     * Calendar events
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Constructor
     *
     * @param Zend_Date $activeDate
     */
    public function __construct(Zend_Date $activeDate = null)
    {
        if (!$activeDate) {
            $activeDate = new Zend_Date();
        }

        $this->setActiveDate($activeDate);
    }

    /**
     * Set the current active date
     *
     * @param Zend_Date $activeDate
     * @return Zym_Calendar
     */
    public function setActiveDate(Zend_Date $activeDate)
    {
        $this->_activeDate = $activeDate;

        return $this;
    }

    /**
     * Get the current active date
     *
     * @return Zend_Date
     */
    public function getActiveDate()
    {
        return $this->_activeDate;
    }

    /**
     * Add a calendar event
     *
     * @param Zym_Calendar_Event $event
     * @return Zym_Calendar
     */
    public function addEvent(Zym_Calendar_Event $event)
    {
        $this->_events[] = $event;

        return $this;
    }

    /**
     * Set multiple events at once
     *
     * @param array $events
     * @return Zym_Calendar
     */
    public function setEvents(array $events)
    {
        foreach ($events as $event) {
        	$this->_addEvent($event);
        }

        return $this;
    }

    /**
     * Get all events
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * Get all events for a specific date
     *
     * @param Zend_Date $date
     * @return array
     */
    public function getEventsForDate(Zend_Date $date)
    {
        $events = array();

        foreach ($this->_events as $event) {
        	if ($event->isOnDate($date)) {
        	    $events[] = $event;
        	}
        }

        return $events;
    }

    /**
     * Set view object
     *
     * @param  Zend_View_Interface $view
     * @return Zym_Calendar
     */
    public function setView(Zend_View_Interface $view = null)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Retrieve view object
     *
     * If none registered, attempts to pull from ViewRenderer.
     *
     * @return Zend_View_Interface|null
     */
    public function getView()
    {
        if (null === $this->_view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->_view = $viewRenderer->view;
        }

        return $this->_view;
    }

    /**
     * Render the calendar
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        return $this->getView()->calendar($this);
    }
}