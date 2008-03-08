<?php
require_once 'trunk/incubator/library/Zym/Notification/Registration.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Notification_Registration test case.
 */
class Zym_Notification_RegistrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Notification_Registration
     */
    private $Zym_Notification_Registration;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->Zym_Notification_Registration = new Zym_Notification_Registration('foo', 'bar');
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Notification_Registration = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Notification_Registration->getCallback()
     */
    public function testGetCallback ()
    {
        $this->assertEquals('bar', $this->Zym_Notification_Registration->getCallback());
    }
    /**
     * Tests Zym_Notification_Registration->getObserver()
     */
    public function testGetObserver ()
    {
        $this->assertEquals('foo', $this->Zym_Notification_Registration->getObserver());
    }
}

