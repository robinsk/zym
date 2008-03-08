<?php
require_once 'trunk/incubator/library/Zym/Notification.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Notification test case.
 */
class Zym_NotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Notification
     */
    private $Zym_Notification;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        // TODO Auto-generated Zym_NotificationTest::setUp()
        $this->Zym_Notification = Zym_Notification::get(/* parameters */);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Zym_NotificationTest::tearDown()
        $this->Zym_Notification = null;
        parent::tearDown();
    }
    /**
     * Constructs the test case.
     */
    public function __construct ()
    {    // TODO Auto-generated constructor
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
        $this->Zym_Notification->detach($this);
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
        $this->Zym_Notification->detach($this);
    }
    /**
     * Tests Zym_Notification->isRegistered()
     */
    public function testIsRegistered ()
    {
        $this->Zym_Notification->attach($this, 'foo');
        $isRegistered = $this->Zym_Notification->isRegistered('foo');
        $this->assertTrue($isRegistered);
        $this->Zym_Notification->detach($this);
    }
    /**
     * Tests Zym_Notification->post()
     */
    public function testPost ()
    {
        // TODO Auto-generated Zym_NotificationTest->testPost()
        $this->markTestIncomplete("post test not implemented");
        $this->Zym_Notification->post(/* parameters */);
    }
    /**
     * Tests Zym_Notification::remove()
     */
    public function testRemove ()
    {
        // TODO Auto-generated Zym_NotificationTest::testRemove()
        $this->markTestIncomplete("remove test not implemented");
        Zym_Notification::remove(/* parameters */);
    }
    /**
     * Tests Zym_Notification->reset()
     */
    public function testReset ()
    {
        // TODO Auto-generated Zym_NotificationTest->testReset()
        $this->markTestIncomplete("reset test not implemented");
        $this->Zym_Notification->reset(/* parameters */);
    }
}

