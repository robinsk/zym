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
require_once 'trunk/library/Zym/Notification.php';

/**
 * @see Zym_Notification_Interface
 */
require_once 'trunk/library/Zym/Notification/Interface.php';

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
    private $Zym_Notification;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->Zym_Notification = Zym_Notification::get();
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Notification = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Notification->attach()
     */
    public function testAttach ()
    {
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
        $this->Zym_Notification->attach($this, 'test');
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        $this->Zym_Notification->attach($this);
        $this->assertTrue($this->Zym_Notification->hasObserver($this, '*'));
        $this->Zym_Notification->detach($this);
        $this->Zym_Notification->reset();
    }
    /**
     * Tests Zym_Notification->detach()
     */
    public function testDetach ()
    {
        $this->Zym_Notification->attach($this, 'test');
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        $this->Zym_Notification->detach($this);
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);

        $this->Zym_Notification->attach($this, 'test');
        $this->Zym_Notification->detach($this, 'test');
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
        $this->assertFalse($hasObserver);
    }
    /**
     * Tests Zym_Notification::get()
     */
    public function testGet ()
    {
        $notification = Zym_Notification::get();
        $this->assertType('Zym_Notification', $notification);
    }
    /**
     * Tests Zym_Notification->getWildcard()
     */
    public function testGetWildcard ()
    {
        $wildcard = $this->Zym_Notification->getWildcard();
        $this->assertEquals('*', $wildcard);
    }
    /**
     * Tests Zym_Notification::has()
     */
    public function testHas ()
    {
        Zym_Notification::get('foo');
        $this->assertTrue(Zym_Notification::has('foo'));
        Zym_Notification::remove('foo');
        $this->assertFalse(Zym_Notification::has('foo'));
    }
    /**
     * Tests Zym_Notification->hasObserver()
     */
    public function testHasObserver ()
    {
        $this->Zym_Notification->attach($this, 'foo');
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'foo');
        $this->assertTrue($hasObserver);
        $this->Zym_Notification->reset();
    }
    /**
     * Tests Zym_Notification->isRegistered()
     */
    public function testIsRegistered ()
    {
        $this->Zym_Notification->attach($this, 'foo');
        $isRegistered = $this->Zym_Notification->isRegistered('foo');
        $this->assertTrue($isRegistered);
        $this->Zym_Notification->reset();
    }
    /**
     * Tests Zym_Notification->post()
     */
    public function testPost()
    {
        $interfaceTest = new TestNotificationInterface();
        $this->Zym_Notification->attach($interfaceTest, 'foo');
        $this->Zym_Notification->attach($interfaceTest);
        $this->Zym_Notification->post('foo', 'bar', array('baz'));

        $notifications = $interfaceTest->getNotifications();
        $this->assertEquals(1, count($notifications));

        $interfaceTest2 = new TestNotificationInterface();
        $this->Zym_Notification->attach($interfaceTest2, 'fo*');
        $this->Zym_Notification->attach($interfaceTest2);
        $this->Zym_Notification->post('foo', 'bar', array('baz'));

        $notifications = $interfaceTest2->getNotifications();
        $this->assertEquals(1, count($notifications));

        $customMethodTest = new TestNotification();
        $this->Zym_Notification->attach($customMethodTest, 'foo', 'msgMe');
        $this->Zym_Notification->post('foo', 'bar', array('baz'));

        $notifications = $customMethodTest->getNotifications();
        $this->assertEquals(1, count($notifications));

        $customMethodTest2 = new TestNotification();
        $this->Zym_Notification->attach($customMethodTest2, 'foo', 'doesNotExist');

        try {
            $this->Zym_Notification->post('foo', 'bar', array('baz'));
            $this->fail('Didnt throw exception');
        } catch (Zym_Notification_Exception_MethodNotImplemented $e) {
            $this->assertType('Zym_Notification_Exception_MethodNotImplemented', $e);
        }
    }

    /**
     * Tests Zym_Notification->reset()
     */
    public function testReset ()
    {
        $this->Zym_Notification->attach($this, 'test');
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
        $this->assertTrue($hasObserver);
        $this->Zym_Notification->reset();
        $hasObserver = $this->Zym_Notification->hasObserver($this, 'test');
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