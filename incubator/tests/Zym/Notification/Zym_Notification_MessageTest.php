<?php
require_once 'trunk/incubator/library/Zym/Notification/Message.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Notification_Message test case.
 */
class Zym_Notification_MessageTest extends PHPUnit_Framework_TestCase
{
    private $mockData = array('foo' => 'bar');

    private $mockName = 'name';

    private $mockSender = 'sender';
    /**
     * @var Zym_Notification_Message
     */
    private $Zym_Notification_Message;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        // TODO Auto-generated Zym_Notification_MessageTest::setUp()
        $this->Zym_Notification_Message = new Zym_Notification_Message($this->mockName, $this->mockSender, $this->mockData);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Zym_Notification_MessageTest::tearDown()
        $this->Zym_Notification_Message = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Notification_Message->getData()
     */
    public function testGetData ()
    {
        $data = $this->Zym_Notification_Message->getData(/* parameters */);
        $this->assertEquals($this->mockData, $data);
    }
    /**
     * Tests Zym_Notification_Message->getName()
     */
    public function testGetName ()
    {
        $name = $this->Zym_Notification_Message->getName(/* parameters */);
        $this->assertEquals($this->mockName, $name);
    }
    /**
     * Tests Zym_Notification_Message->getSender()
     */
    public function testGetSender ()
    {
        $sender = $this->Zym_Notification_Message->getSender(/* parameters */);
        $this->assertEquals($this->mockSender, $sender);
    }
}