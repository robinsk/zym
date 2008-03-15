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
 * @see Zym_Timer
 */
require_once 'Zym/Timer.php';

/**
 * Timer manager component
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Timer
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Timer_Manager implements Countable 
{
    /**
     * Timer instances
     *
     * @var array
     */
    protected $_timers = array();

    /**
     * Count of timers
     *
     * @var integer
     */
    protected $_count = 0;
    
    /**
     * Create a timer instance
     *
     * @param string $name
     * @param string $group
     * @return Zym_Timer
     */
    public function getTimer($name, $group = null)
    {
        if (!isset($this->_timers[$group][$name])) {
            $timer = new Zym_Timer();
            $this->addTimer($name, $timer, $group);
        }
        
        return $this->_timers[$group][$name];
    }
    
    /**
     * Add a timer instance
     *
     * @param string $name
     * @param Zym_Timer_Timer $timer
     * @param string $group
     * @return Zym_Timer_Manager
     */
    public function addTimer($name, Zym_Timer $timer, $group = null)
    {
        // Set timer
        $this->_timers[$group][$name] = $timer;
        
        // Count
        $this->_count++;
        
        return $this;
    }
    
    /**
     * Get all timer instances
     *
     * @return array
     */
    public function getTimers()
    {
        return $this->_timers;
    }
    
    /**
     * Clear all timer instances
     *
     * @return Zym_Timer_Manager
     */
    public function clearTimers()
    {
        // Clear timers
        $this->_timers = array();
        
        // Reset count
        $this->_count = 0;
        
        return $this;
    }
    
    /**
     * Get timer count
     *
     * @return integer
     */
    public function count()
    {
        return $this->_count;
    }
}
