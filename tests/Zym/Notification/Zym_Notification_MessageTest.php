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
 * @see Zym_Notification_Message
 */
require_once 'trunk/library/Zym/Notification/Message.php';

/**
 * Test suite for Zym_Notification_Message
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Notification_MessageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test data
     *
     * @var array
     */
    private $mockData = array('foo' => 'bar');

    /**
     * Test name
     *
     * @var string
     */
    private $mockName = 'name';

    /**
     * Test sender
     *
     * @var string
     */
    private $mockSender = 'sender';

    /**
     * Message instance
     *
     * @var Zym_Notification_Message
     */
    private $Zym_Notification_Message;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->Zym_Notification_Message = new Zym_Notification_Message($this->mockName, $this->mockSender, $this->mockData);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
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