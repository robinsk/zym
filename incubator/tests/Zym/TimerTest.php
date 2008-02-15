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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
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
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_Timer
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_TimerTest extends PHPUnit_Framework_TestCase
{    
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
	    parent::tearDown();
	}

	public function testGetCalls()
	{
        $timer = new Zym_Timer();
        
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
	    $timer = new Zym_Timer();
	    
	    $timer->start();
	    $this->assertTrue($timer->isStarted());
	    $timer->stop();
	}
}