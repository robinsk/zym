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
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once('PHPUnit/Framework/TestCase.php');

/**
 * @see Zym_Timer
 */
require_once('Zym/Timer.php');

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Timer
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_TimerTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Timer
     *
     * @var Zym_Timer
     */
    protected $_timer;
    
    /**
     * Prepares the environment before running a test.
     * 
     */
    protected function setUp()
    {        
        $this->_timer = new Zym_Timer();
    }
    
    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {
        unset($this->_timer);
    }
    
    public function testStart()
    {
        $timer = clone $this->_timer;
        $timer->start();
        
        $this->assertAttributeNotEquals(null, '_start', $timer);
    }
    
    public function testStop()
    {
        $timer1 = clone $this->_timer;

        $timer1->start();
        $run = $timer1->stop();
        
        $this->assertGreaterThan(0, $run);
        $this->assertEquals(1, count($timer1->getRunAsArray()));
        
        // Test exception
        $timer2 = clone $this->_timer;
        $this->setExpectedException('Zym_Timer_Exception');
        $timer2->stop();
    }

    public function testGetCalls()
    {
        $timer = clone $this->_timer;

        // Test first run
        $timer->start();
        $timer->stop();
        $this->assertEquals(1, $timer->getCalls());
        
        // Test second run
        $timer->start();
        $timer->stop();
        $this->assertEquals(2, $timer->getCalls());
        
        // Test third mid run
        $timer->start();
        $this->assertEquals(3, $timer->getCalls());
        $timer->stop();
    }
    
    public function testIsStarted()
    {
        $timer = clone $this->_timer;
        
        $timer->start();
        $this->assertTrue($timer->isStarted());
        
        $timer->stop();
        $this->assertFalse($timer->isStarted());
    }
    
    public function testGetElapsed()
    {
        $timer = clone $this->_timer;
        
        $this->assertEquals(0, $timer->getElapsed());
        
        $timer->start();
        $this->assertNotEquals(0, $timer->getElapsed());
    }
    
    public function testGetElapsedAverage()
    {
        $timer = clone $this->_timer;
        $timer->start();
        $timer->stop();
        
        $this->assertNotEquals(0, $timer->getElapsedAverage());
    }
    
    public function testGetRun()
    {
        $timer = clone $this->_timer;
        $timer->start();
        $timer->stop();
        
        $this->assertNotEquals(0, $timer->getRun());
    }
    
    public function testGetRunAverage()
    {
        $timer = clone $this->_timer;
        $timer->start();
        $timer->stop();
        
        $this->assertNotEquals(0, $timer->getRunAverage());
    }
    
    public function testGetRunAsArray()
    {
        $timer = clone $this->_timer;
        
        for ($x = 0; $x <= 5; ++$x) {
           $timer->start();
           $timer->stop();
        }
        
        $this->assertEquals(6, count($timer->getRunAsArray()));
    }
    
    public function testReset()
    {
        $timer = new Zym_Timer();
        $timer->start();
        $timer->stop();
        
        $timer->reset();
        
        $this->assertAttributeEquals(null, '_start', $timer);
        $this->assertAttributeEquals(array(), '_totalTime', $timer);
    }
}