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
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Notification_Notification
 */
require_once 'Zym/Notification.php';

/**
 * @see Zym_Notification_Interface
 */
require_once 'Zym/Notification/Interface.php';

/**
 * Test for Zym_Notification
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_NotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zym_Notification instance
     *
     * @var Zym_Notification
     */
    private $_notification;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_notification = Zym_Notification::get();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_notification = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Notification->attach()
     */
    public function testAttach()
    {
        $hasObserver = $this->_notification->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
        
        $this->_notification->attach($this, 'test');
        $hasObserver = $this->_notification->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        
        $this->_notification->attach($this);
        $this->assertTrue($this->_notification->hasObserver($this, '*'));
        
        $this->_notification->detach($this);
        $this->_notification->reset();
    }
    /**
     * Tests Zym_Notification->detach()
     */
    public function testDetach()
    {
        $this->_notification->attach($this, 'test');
        $hasObserver = $this->_notification->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        $this->_notification->detach($this);
        $hasObserver = $this->_notification->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);

        $this->_notification->attach($this, 'test');
        $this->_notification->detach($this, 'test');
        $hasObserver = $this->_notification->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
    }
    /**
     * Tests Zym_Notification::get()
     */
    public function testGet()
    {
        $notification = Zym_Notification::get();
        $this->assertType('Zym_Notification', $notification);
    }
    
    /**
     * Tests Zym_Notification->getWildcard()
     */
    public function testGetWildcard()
    {
        $wildcard = $this->_notification->getWildcard();
        $this->assertEquals('*', $wildcard);
    }
    
    /**
     * Tests Zym_Notification::has()
     */
    public function testHas()
    {
        Zym_Notification::get('foo');
        $this->assertTrue(Zym_Notification::has('foo'));
        Zym_Notification::remove('foo');
        $this->assertFalse(Zym_Notification::has('foo'));
    }
    
    /**
     * Tests Zym_Notification->hasObserver()
     */
    public function testHasObserver()
    {
        $this->_notification->attach($this, 'foo');
        $hasObserver = $this->_notification->hasObserver($this, 'foo');
        $this->assertTrue($hasObserver);
        
        $this->_notification->reset();
    }
    
    /**
     * Tests Zym_Notification->isRegistered()
     */
    public function testIsRegistered()
    {
        $this->_notification->attach($this, 'foo');
        $isRegistered = $this->_notification->isRegistered('foo');
        $this->assertTrue($isRegistered);
        
        $this->_notification->reset();
    }
    
    /**
     * Tests Zym_Notification->post()
     */
    public function testPost()
    {
        $interfaceTest = new TestNotificationInterface();
        $this->_notification->attach($interfaceTest, 'foo');
        $this->_notification->attach($interfaceTest);
        $this->_notification->post('foo', 'bar', array('baz'));

        $notifications = $interfaceTest->getNotifications();
        $this->assertEquals(1, count($notifications));

        $interfaceTest2 = new TestNotificationInterface();
        $this->_notification->attach($interfaceTest2, 'fo*');
        $this->_notification->attach($interfaceTest2);
        $this->_notification->post('foo', 'bar', array('baz'));

        $notifications = $interfaceTest2->getNotifications();
        $this->assertEquals(1, count($notifications));

        $customMethodTest = new TestNotification();
        $this->_notification->attach($customMethodTest, 'foo', 'msgMe');
        $this->_notification->post('foo', 'bar', array('baz'));

        $notifications = $customMethodTest->getNotifications();
        $this->assertEquals(1, count($notifications));

        $customMethodTest2 = new TestNotification();
        $this->_notification->attach($customMethodTest2, 'foo', 'doesNotExist');

        try {
            $this->_notification->post('foo', 'bar', array('baz'));
            $this->fail('Didn\'t throw exception');
        } catch (Zym_Notification_Exception_MethodNotImplemented $e) {
            $this->assertType('Zym_Notification_Exception_MethodNotImplemented', $e);
        }
    }

    /**
     * Tests Zym_Notification->reset()
     */
    public function testReset()
    {
        $this->_notification->attach($this, 'test');
        $hasObserver = $this->_notification->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        
        $this->_notification->reset();
        
        $hasObserver = $this->_notification->hasObserver($this, 'test');
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
     * @var Zym_Notification_Message $notification
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
class TestNotificationInterface implements Zym_Notification_Interface
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
     * @var Zym_Notification_Message $notification
     */
    public function notify(Zym_Notification_Message $notification)
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