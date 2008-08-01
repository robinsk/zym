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
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Controller_Action_Helper_Navigation
 */
require_once 'Zym/Controller/Action/Helper/Navigation.php';

/**
 * @see Zym_Navigation
 */
require_once 'Zym/Navigation.php';

/**
 * Tests the class Zym_Controller_Action_Helper_Navigation
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_Controller_Action_Helper_Navigation
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->clearRegistry();
        $this->_helper = new Zym_Controller_Action_Helper_Navigation();
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->_helper);
        $this->clearRegistry();
    }

    /**
     * Clear the registry
     *
     * @return Zym_Controller_Action_Helper_TranslatorTest
     */
    public function clearRegistry()
    {
        $regKey = 'Zym_Navigation';
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }

        return $this;
    }

    public function testConstructAcceptsNavigationObject()
    {
        $container = new Zym_Navigation();
        $helper    = new Zym_Controller_Action_Helper_Navigation($container);

        $this->assertSame($container, $helper->getNavigation());
    }

    public function testConstructWorksWithNoArgs()
    {
        $helper = new Zym_Controller_Action_Helper_Navigation();
    }

    public function testGetNavigationGetsFromRegistry()
    {
        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        $container = new Zym_Navigation();
        Zend_Registry::set('Zym_Navigation', $container);

        $helper = $this->_helper;
        $this->assertSame($container, $helper->getNavigation());

        $this->clearRegistry();
    }

    public function testDirectGetsNavigation()
    {
        $helper = $this->_helper;
        $this->assertSame($helper->getNavigation(), $helper->direct());
    }

    public function testSetNavigation()
    {
        $helper = new Zym_Controller_Action_Helper_Navigation();
        $helper->setNavigation(new Zym_Navigation());
        $this->assertSame($navigation, $helper->getNavigation());
    }
}