<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Calendar
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Calendar
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Calendar_Event
{
    /**
     * The label for this event
     *
     * @var string
     */
    protected $_label = null;

    /**
     * The location for this event
     *
     * @var string
     */
    protected $_location = null;

    /**
     * A note for this event
     *
     * @var string
     */
    protected $_note = null;

    /**
     * The event's start date and time
     *
     * @var Zend_Date
     */
    protected $_startDate = null;

    /**
     * The event's end date and time
     *
     * @var Zend_Date
     */
    protected $_endDate = null;

    /**
     * Repeat mode
     *
     * @var string
     */
    protected $_repeat = self::REPEAT_NONE;

    /**
     * Repeat interval for the custom repeat setting
     *
     * @var int
     */
    protected $_repeatInterval = null;

    /**
     * Repeat constants
     */
    const REPEAT_NONE    = 'repeatNone';
    const REPEAT_DAILY   = 'repeatDaily';
    const REPEAT_WEEKLY  = 'repeatWeekly';
    const REPEAT_MONTHLY = 'repeatMonthly';
    const REPEAT_ANUAL   = 'repeatAnual';
    const REPEAT_CUSTOM  = 'repeatCustom';

    /**
     * Constructor
     *
     * @param string $label
     * @param Zend_Date $startDate
     * @param Zend_Date $endDate
     * @param string $location
     * @param string $note
     */
    public function __construct($label, Zend_Date $startDate = null, Zend_Date $endDate = null,
                                $location = null, $note = null)
    {
        $this->_label = $label;
        $this->_location = $location;
        $this->_note = $note;

        if (!$startDate) {
            $startDate = new Zend_Date();
        }

        if (!$endDate && $startDate) {
            $endDate = clone $startDate;
            $endDate->addHour(1);
        }

        if ($startDate && $endDate) {
            $this->setStartDate($startDate);
            $this->setEndDate($endDate);
        }
    }

    /**
     * Set the start date for this event
     *
     * @param Zend_Date $date
     * @param boolean $wholeDay
     * @return Zym_Calendar_Event
     */
    public function setStartDate(Zend_Date $date, $wholeDay = false)
    {
        if ($wholeDay) {
            $date->setHour(0);
            $date->setMinute(0);
            $date->setSecond(0);
            $date->setMilliSecond(0);

            $endDate = clone $date;
            $endDate->setHour(23);
            $endDate->setMinute(59);
            $endDate->setSecond(59);

            $this->setEndDate($endDate);
        }

        $this->_startDate = $date;

        return $this;
    }

    /**
     * Get the start date
     *
     * @return Zend_Date
     */
    public function getStartDate()
    {
        return $this->_startDate;
    }

    /**
     * Set the end date
     *
     * @param Zend_Date $date
     * @return Zym_Calendar_Event
     */
    public function setEndDate(Zend_Date $date)
    {
        $this->_endDate = $date;

        return $this;
    }

    /**
     * Get the event's end date
     *
     * @return Zend_Date
     */
    public function getEndDate()
    {
        return $this->_endDate;
    }

    /**
     * Check if the event is on the given date
     *
     * @param Zend_Date $date
     * @return Zym_Calendar_Event
     */
    public function isOnDate(Zend_Date $date)
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        return ( ($date->equals($startDate) || $date->isLater($startDate)) &&
                 ($date->equals($endDate) || $date->isEarlier($endDate)) );
    }

    /**
     * Set the repeat mode
     *
     * @param string $repeat
     * @param int $interval
     * @return Zym_Calendar_Event
     */
    public function setRepeat($repeat, $interval = null)
    {
        if ($repeat == self::REPEAT_CUSTOM) {
            $this->_repeatInterval = $interval;
        }

        $this->_repeat = $repeat;

        return $this;
    }

    /**
     * Get the repeat mode
     *
     * @return string
     */
    public function getRepeat()
    {
        return $this->_repeat;
    }

    /**
     * Get the repeat interval
     *
     * @return int|null
     */
    public function getRepeatInterval()
    {
        return $this->_repeatInterval;
    }
}