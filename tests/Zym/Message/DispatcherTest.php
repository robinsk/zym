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
 * @category   Zym_Tests
 * @package    Zym_Message
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Message_Notification
 */
require_once 'Zym/Message.php';

/**
 * @see Zym_Message_Interface
 */
require_once 'Zym/Message/Interface.php';

/**
 * Test for Zym_Message
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Message
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Message_DispatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zym_Message instance
     *
     * @var Zym_Message
     */
    private $_dispatcher;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_dispatcher = Zym_Message_Dispatcher::get();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_dispatcher = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Message->attach()
     */
    public function testAttach()
    {
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
        
        $this->_dispatcher->attach($this, 'test');
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        
        $this->_dispatcher->attach($this);
        $this->assertTrue($this->_dispatcher->hasObserver($this, '*'));
        
        $this->_dispatcher->detach($this);
        $this->_dispatcher->reset();
    }
    /**
     * Tests Zym_Message->detach()
     */
    public function testDetach()
    {
        $this->_dispatcher->attach($this, 'test');
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        $this->_dispatcher->detach($this);
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);

        $this->_dispatcher->attach($this, 'test');
        $this->_dispatcher->detach($this, 'test');
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
    }
    /**
     * Tests Zym_Message_Dispatcher::get()
     */
    public function testGet()
    {
        $notification = Zym_Message_Dispatcher::get();
        $this->assertType('Zym_Message', $notification);
    }
    
    /**
     * Tests Zym_Message->getWildcard()
     */
    public function testGetWildcard()
    {
        $wildcard = $this->_dispatcher->getWildcard();
        $this->assertEquals('*', $wildcard);
    }
    
    /**
     * Tests Zym_Message_Dispatcher::has()
     */
    public function testHas()
    {
        Zym_Message_Dispatcher::get('foo');
        $this->assertTrue(Zym_Message_Dispatcher::has('foo'));
        Zym_Message_Dispatcher::remove('foo');
        $this->assertFalse(Zym_Message_Dispatcher::has('foo'));
    }
    
    /**
     * Tests Zym_Message->hasObserver()
     */
    public function testHasObserver()
    {
        $this->_dispatcher->attach($this, 'foo');
        $hasObserver = $this->_dispatcher->hasObserver($this, 'foo');
        $this->assertTrue($hasObserver);
        
        $this->_dispatcher->reset();
    }
    
    /**
     * Tests Zym_Message->isRegistered()
     */
    public function testIsRegistered()
    {
        $this->_dispatcher->attach($this, 'foo');
        $isRegistered = $this->_dispatcher->isRegistered('foo');
        $this->assertTrue($isRegistered);
        
        $this->_dispatcher->reset();
    }
    
    /**
     * Tests Zym_Message->post()
     */
    public function testPost()
    {
        $interfaceTest = new TestNotificationInterface();
        $this->_dispatcher->attach($interfaceTest, 'foo');
        $this->_dispatcher->attach($interfaceTest);
        $this->_dispatcher->post('foo', 'bar', array('baz'));

        $notifications = $interfaceTest->getNotifications();
        $this->assertEquals(1, count($notifications));

        $interfaceTest2 = new TestNotificationInterface();
        $this->_dispatcher->attach($interfaceTest2, 'fo*');
        $this->_dispatcher->attach($interfaceTest2);
        $this->_dispatcher->post('foo', 'bar', array('baz'));

        $notifications = $interfaceTest2->getNotifications();
        $this->assertEquals(1, count($notifications));

        $customMethodTest = new TestNotification();
        $this->_dispatcher->attach($customMethodTest, 'foo', 'msgMe');
        $this->_dispatcher->post('foo', 'bar', array('baz'));

        $notifications = $customMethodTest->getNotifications();
        $this->assertEquals(1, count($notifications));

        $customMethodTest2 = new TestNotification();
        $this->_dispatcher->attach($customMethodTest2, 'foo', 'doesNotExist');

        try {
            $this->_dispatcher->post('foo', 'bar', array('baz'));
            $this->fail('Didn\'t throw exception');
        } catch (Zym_Message_Exception $e) {
            $this->assertType('Zym_Message_Exception', $e);
        }
    }

    /**
     * Tests Zym_Message->reset()
     */
    public function testReset()
    {
        $this->_dispatcher->attach($this, 'test');
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        
        $this->_dispatcher->reset();
        
        $hasObserver = $this->_dispatcher->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
    }
}

/**
 * Test class for receiving a notification without the interface
 */
class TestNotification
{
    /**
     * Notifications
     *
     * @var array
     */
    protected $_notifications = array();

    /**
     * Notify method
     *
     * @var Zym_Message $notification
     */
    public function msgMe($notification)
    {
        $this->_notifications[] = $notification;
    }

    /**
     * Get the notifications
     *
     * @return array
     */
    public function getNotifications()
    {
        return $this->_notifications;
    }
}

/**
 * Test class for receiving a notification with the interface
 */
class TestNotificationInterface implements Zym_Message_Interface
{
    /**
     * Notifications
     *
     * @var array
     */
    protected $_notifications = array();

    /**
     * Notify method
     *
     * @var Zym_Message $notification
     */
    public function notify(Zym_Message $notification)
    {
        $this->_notifications[] = $notification;
    }

    /**
     * Get the notifications
     *
     * @return array
     */
    public function getNotifications()
    {
        return $this->_notifications;
    }
}