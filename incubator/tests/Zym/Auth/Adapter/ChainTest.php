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
 * @see Zym_Auth_Adapter_Chain
 */
require_once 'Zym/Auth/Adapter/Chain.php';

/**
 * @see Zym_Auth_Adapter_Mock
 */
require_once 'Zym/Auth/Adapter/Mock.php';

/**
 * Tests the class Zym_Auth
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Auth
 * @subpackage Adapter
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Auth_Adapter_ChainTest extends PHPUnit_Framework_TestCase
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

    public function testGetAdaptersReturnsArrayOfAdapters()
    {
        $adapter1 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS);
        $adapter2 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS);

        $chain = new Zym_Auth_Adapter_Chain();
        $chain->addAdapter($adapter1)
              ->addAdapter($adapter2);

        $this->assertSame(array($adapter1, $adapter2), $chain->getAdapters());
    }

    public function testAddAdapter()
    {
        $adapter = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS);

        $chain = new Zym_Auth_Adapter_Chain();
        $chain->addAdapter($adapter);

        $this->assertSame(array($adapter), $chain->getAdapters());
    }

    public function testAuthenticateReturnsSucessResult()
    {
        $adapter1 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::FAILURE);
        $adapter2 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS);

        $chain = new Zym_Auth_Adapter_Chain();
        $chain->addAdapter($adapter1)
              ->addAdapter($adapter2);

        $result = $chain->authenticate();
        $this->assertType('Zend_Auth_Result', $result);
        $this->assertTrue($result->isValid());
    }

    public function testAuthenticateReturnsFailResult()
    {
        $adapter1 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::FAILURE);
        $adapter2 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::FAILURE);

        $chain = new Zym_Auth_Adapter_Chain();
        $chain->addAdapter($adapter1)
              ->addAdapter($adapter2);

        $result = $chain->authenticate();
        $this->assertType('Zend_Auth_Result', $result);
        $this->assertFalse($result->isValid());
    }

    public function testGetLastSuccessfulAdapterReturnsAdapter()
    {
        $adapter1 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::FAILURE);
        $adapter2 = new Zym_Auth_Adapter_Mock(Zend_Auth_Result::SUCCESS);

        $chain = new Zym_Auth_Adapter_Chain();
        $chain->addAdapter($adapter1)
              ->addAdapter($adapter2);

        $result = $chain->authenticate();

        $this->assertSame($adapter2, $chain->getLastSuccessfulAdapter());
    }
}