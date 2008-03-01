<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_Timer
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Timer component
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Timer
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Timer
{
    /**
     * Start time
     *
     * @var integer
     */
    protected $_start;

    /**
     * Total time
     *
     * @var array
     */
    protected $_totalTime = array();

    /**
     * Stop flag
     *
     * @var boolean
     */
    protected $_stopped = false;

    /**
     * Start the timer
     *
     */
    public function start()
    {
        $this->_start = microtime(true);
        $this->_stopped = false;
    }

    /**
     * Stop the timer
     *
     * @return integer Time elapsed for this run
     */
    public function stop()
    {
        if ($this->_stopped == true) {
            /**
             * @see Zym_Timer_Exception
             */
            require_once 'Zym/Timer/Exception.php';
            throw new Zym_Timer_Exception('Timer already stopped');
        }

        $spentTime = microtime(true) - $this->_start;
        $this->_totalTime[] = $spentTime;
        $this->_stopped = true;

        return $spentTime;
    }

    /**
     * Amount of times this timer was started
     *
     * @return integer
     */
    public function getCalls()
    {
        $calls = count($this->_totalTime);
        if ($calls < 1 && $this->_start !== null || $this->_stopped == false) {
            $calls++;
        }

        return $calls;
    }

    /**
     * Time elapsed
     *
     * @return integer
     */
    public function getElapsed()
    {
        $elapsedTime = array_sum($this->_totalTime);

        // No elapsed time or currently running? take/add current running time
        if ($elapsedTime == 0 && $this->_start !== null) {
            $elapsedTime = microtime(true) - $this->_start;
        } else if ($this->_start !== null && $this->_stopped == false) {
            $elapsedTime += microtime(true) - $this->_start;
        }

        return $elapsedTime;
    }

    /**
     * Get average runtime
     *
     * @return integer
     */
    public function getAverage()
    {
        $calls = $this->getCalls();
        if ($calls == 0) {
            // @todo do we throw an exception or return 0?
            /**
             * @see Zym_Timer_Exception
             */
            require_once 'Zym/Timer/Exception.php';
            throw new Zym_Timer_Exception(
                'Cannot get average time because timer has not been started'
            );
        }

        $averageTime = $this->getElapsed() / $calls;
        return $averageTime;
    }

    /**
     * Get runtimes
     *
     * @return array
     */
    public function getRun()
    {
        return $this->_totalTime;
    }

    /**
     * Whether the timer is running
     *
     * @return boolean
     */
    public function isStarted()
    {
        return !$this->_stopped;
    }
}