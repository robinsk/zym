<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym_Tests
 * @package   Zym_App
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_App
 */
require_once 'Zym/App.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Tests the class Zym_App
 *
 * @author    Geoffrey Tran
 * @category  Zym_Tests
 * @package   Zym_App
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_AppTest extends PHPUnit_Framework_TestCase
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

    public function testSingletonIsEnforcedFromConstruct()
    {
        $this->markTestSkipped();
        $app = new Zym_App();
    }

    public function testSingletonIsEnforcedFromClone()
    {
        $this->markTestSkipped();
        $app   = Zym_App::getInstance();
        $clone = clone $app;
    }

    public function testGetInstanceCreatesInstance()
    {
        $app = Zym_App::getInstance();
        $this->assertEquals($this->isInstanceOf('Zym_App'), $app);
    }

    public function testGetInstanceGetsSameInstance()
    {
        $app  = Zym_App::getInstance();
        $app->foo = 'bar';

        $app2 = Zym_App::getInstance();
        $this->assertSame($app, $app2);
    }

    public function testResetInstanceCreatesNewInstance()
    {
        $this->markTestIncomplete();
    }

    public function testResetInstance()
    {
        $app = Zym_App::getInstance();
        $app->addResourcePrefix('Test_Prefix', 1);

        Zym_App::resetInstance();
        $app2 = Zym_App::getInstance();
    }

    public function testSetConfigArray()
    {
        $this->markTestIncomplete();
        $app = Zym_App::getInstance();
        $app->setConfig(array('test' => 'test'));

        $this->assertEquals('test', $app->getConfig()->get('test'));
    }

    public function testSetConfigFromZendConfig()
    {
        $app = Zym_App::getInstance();
        $app->setConfig(new Zend_Config(array(
            'test' => 'test'
        )));

        $this->assertEquals('test', $app->getConfig()->get('test'));
    }
}