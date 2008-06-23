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
require_once 'Zym/Notification/Message.php';

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
    private $_mockData = array('foo' => 'bar');

    /**
     * Test name
     *
     * @var string
     */
    private $_mockName = 'name';

    /**
     * Test sender
     *
     * @var string
     */
    private $_mockSender = 'sender';

    /**
     * Message instance
     *
     * @var Zym_Notification_Message
     */
    private $_message;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_message = new Zym_Notification_Message(
                                 $this->_mockName, $this->_mockSender, $this->_mockData);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_message = null;
    }

    /**
     * Tests Zym_Notification_Message->getData()
     */
    public function testGetData()
    {
        $data = $this->_message->getData();
        $this->assertEquals($this->_mockData, $data);
    }
    
    /**
     * Tests Zym_Notification_Message->getName()
     */
    public function testGetName()
    {
        $name = $this->_message->getName();
        $this->assertEquals($this->_mockName, $name);
    }
    
    /**
     * Tests Zym_Notification_Message->getSender()
     */
    public function testGetSender()
    {
        $sender = $this->_message->getSender();
        $this->assertEquals($this->_mockSender, $sender);
    }
}