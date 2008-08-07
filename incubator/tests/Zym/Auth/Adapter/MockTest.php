<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym_Tests
 * @package    Zym_Auth
 * @subpackage Adapter
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Auth_Adapter_Mock
 */
require_once 'Zym/Auth/Adapter/Mock.php';

/**
 * Zym_Auth_Adapter_Mock
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Auth
 * @subpackage Adapter
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Auth_Adapter_MockTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
    }


    public function testConstruct()
    {
        $mock = new Zym_Auth_Adapter_Mock();
    }

    public function testConstructSetsOptions()
    {
        $mock = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS, 'test', array('message'));
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $mock->getCode());
        $this->assertEquals('test', $mock->getIdentity());
        $this->assertEquals(array('message'), $mock->getMessages());
    }

    public function testAuthenticateReturnsResult()
    {
        $mock = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS, 'test', array('message'));
        $result = $mock->authenticate();

        $this->assertEquals(Zend_Auth_Result::SUCCESS, $result->getCode());
        $this->assertEquals('test', $result->getIdentity());
        $this->assertEquals(array('message'), $result->getMessages());
    }

    public function testGetCodeReturnsCode()
    {
        $mock = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS, 'test', array('message'));

        $this->assertEquals(Zend_Auth_Result::SUCCESS, $mock->getCode());
    }

    public function testSetCodeIsFluent()
    {
        $mock = new Zym_Auth_Adapter_Mock();
        $instance = $mock->setCode(Zend_Auth_Result::SUCCESS);

        $this->assertSame($mock, $instance);
    }

    public function testSetCode()
    {
        $mock = new Zym_Auth_Adapter_Mock();
        $mock->setCode(Zend_Auth_Result::SUCCESS);

        $this->assertSame(Zend_Auth_Result::SUCCESS, $mock->getCode());
    }

    public function testGetIdentity()
    {
        $mock = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS, 'test', array('message'));

        $this->assertEquals('test', $mock->getIdentity());
    }

    public function testSetIdentityIsFluent()
    {
        $mock = new Zym_Auth_Adapter_Mock();
        $instance = $mock->setIdentity('test');

        $this->assertSame($mock, $instance);
    }

    public function testSetIdentity()
    {
        $mock = new Zym_Auth_Adapter_Mock();
        $mock->setIdentity('test');

        $this->assertSame('test', $mock->getIdentity());
    }

    public function testGetMessages()
    {
        $mock = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS, 'test', array('message'));

        $this->assertEquals(array('message'), $mock->getMessages());
    }

    public function testSetMessagesIsFluent()
    {
        $mock = new Zym_Auth_Adapter_Mock();
        $instance = $mock->setMessages(array());

        $this->assertSame($mock, $instance);
    }

    public function testSetMessages()
    {
        $mock = new Zym_Auth_Adapter_Mock();
        $mock->setMessages(array('message'));

        $this->assertSame(array('message'), $mock->getMessages());
    }
}