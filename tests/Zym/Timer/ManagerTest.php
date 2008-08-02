<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym_Tests
 * @package   Zym_Timer
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license   http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Timer_Manager
 */
require_once 'Zym/Timer/Manager.php';

/**
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym_Tests
 * @package   Zym_Timer
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Timer_ManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Manager
     *
     * @var Zym_Timer_Manager
     */
    private $_manager;

    /**
     * Prepares the environment before running a test.
     *
     */
    protected function setUp()
    {
        $this->_manager = new Zym_Timer_Manager();
    }

    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {
        unset($this->_manager);
    }

    public function testGetTimerCreatesTimerWhenNotExist()
    {
        $manager = new Zym_Timer_Manager();
        $timer = $manager->getTimer('doesNotExist');

        $this->assertEquals(true, ($timer instanceof Zym_Timer));
    }

    public function testGetTimerReturnsTimer()
    {
        $manager = new Zym_Timer_Manager();

        $timer1 = new Zym_Timer();
        $timer2 = new Zym_Timer();
        $timer3 = new Zym_Timer();

        $manager->addTimer('first', $timer1);
        $manager->addtimer('second', $timer2);
        $manager->addTimer('third', $timer3, 'group');

        $this->assertEquals($timer1, $manager->getTimer('first'));
        $this->assertEquals($timer2, $manager->getTimer('second'));
        $this->assertEquals($timer3, $manager->getTimer('third', 'group'));
    }

    public function testCreateTimerReturnsTimer()
    {
        $manager = new Zym_Timer_Manager();
        $timer = $manager->createTimer('first');
        $this->assertEquals(true, ($timer instanceof Zym_Timer));
    }

    public function testCreateTimerAddsTimer()
    {
        $manager = new Zym_Timer_Manager();

        $timer1 = $manager->createTimer('first');
        $timer2 = $manager->createTimer('second');
        $timer3 = $manager->createTimer('third', 'group');

        $this->assertEquals($timer1, $manager->getTimer('first'));
        $this->assertEquals($timer2, $manager->getTimer('second'));
        $this->assertEquals($timer3, $manager->getTimer('third', 'group'));
    }

    public function testAddTimer()
    {
        $manager = new Zym_Timer_Manager();
        $timer = $manager->addTimer('timer');

        $this->assertEquals($timer, $manager->getTimer('timer'));
    }

    public function testAddTimerThrowsExceptionWhenExists()
    {
        $timer = new Zym_Timer();

        $manager = new Zym_Timer_Manager();
        $manager->addTimer('timer', $timer);
        $manager->addTimer('timer', $timer);

        $this->setExpectedException('Zym_Timer_Manager_Exception');
    }

    public function testAddTimerGroupThrowsExceptionWhenExists()
    {
        $timer = new Zym_Timer();

        $manager = new Zym_Timer_Manager();
        $manager->addTimer('timer', $timer,'group');
        $manager->addTimer('timer', $timer, 'group');

        $this->setExpectedException('Zym_Timer_Manager_Exception');
    }

    public function testSetTimer()
    {
        $timer  = new Zym_Timer();
        $timer2 = new Zym_Timer();
        $timer3 = new Zym_Timer();

        $manager = new Zym_Timer_Manager();
        $manager->addTimer('test', $timer);

        $manager->setTimer('test', $timer2);
        $manager->setTimer('test2', $timer3);
        $manager->setTimer('test3', $timer3, 'group');


        $this->assertEquals($timer2, $manager->getTimer('test'));
        $this->assertEquals($timer3, $manager->getTimer('test2'));
        $this->assertEquals($timer3, $manager->getTimer('test3', 'group'));
    }

    public function testHasTimer()
    {
        $manager = new Zym_Timer_Manager();
        $manager->createTimer('test');
        $manager->createTimer('test', 'group');

        $this->assertTrue($manager->hasTimer('test'));
        $this->assertTrue($manager->hasTimer('test', 'group'));
    }

    public function testGetTimers()
    {
        $manager = new Zym_Timer_Manager();
        $test  = $manager->createTimer('test');
        $test2 = $manager->createTimer('test', 'group');

        $timers   = $manager->getTimers();
        $expected = array(
            null    => array('test' => $test),
            'group' => array('test' => $test2)
        );

        $this->assertEquals($expected, $timers);
    }

    public function testGetRun()
    {
        $manager = new Zym_Timer_Manager();
        $timer = $manager->createTimer('test');

        $this->assertEquals(0, $manager->getRun());

        $timer2 = $manager->createTimer('test2');
        $timer->start();
        $timer2->start();
        sleep(1);
        $timer->stop();
        $timer2->stop();

        $this->assertTrue($manager->getRun() > 0);
    }

    public function testGetGroupRun()
    {
        $manager = new Zym_Timer_Manager();
        $timer = $manager->createTimer('test', 'group');

        $this->assertEquals(0, $manager->getGroupRun('group'));

        $timer2 = $manager->createTimer('test2', 'group');
        $timer->start();
        $timer2->start();
        sleep(1);
        $timer->stop();
        $timer2->stop();

        $this->assertTrue($manager->getGroupRun('group') > 0);
    }

    public function testClearTimers()
    {
        $manager = new Zym_Timer_Manager();
        $manager->createTimer('first');
        $manager->createTimer('second');
        $manager->createTimer('third');

        $this->assertAttributeEquals(array(). '_timers', $manager);
        $this->assertEquals(array(), $manager->getTimers());
    }

    public function testManagerImplementsCountable()
    {
        $manager = new Zym_Timer_Manager();
        $this->assertEquals(0, count($manager));

        $manager->createTimer('first');
        $this->assertEquals(1, count($manager));
        $this->assertEquals(1, count($manager));

        $manager->createTimer('second');
        $this->assertEquals(2, count($manager));
        $this->assertEquals(1, $manager->count());
    }
}