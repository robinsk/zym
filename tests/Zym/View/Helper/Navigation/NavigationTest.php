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
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * Imports
 *
 * @see Zym_View_Helper_Navigation_TestAbstract
 * @see Zym_View_Helper_Breadcrumbs
 */
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zym/View/Helper/Navigation.php';

/**
 * Tests Zym_View_Helper_Navigation
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Navigation_NavigationTest
    extends Zym_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zym_View_Helper_Navigation';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Navigation
     */
    protected $_helper;

    public function testShouldProxyToMenuHelperByDeafult()
    {
        // setup
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);
        $this->_helper->setContainer(null);

        // result
        $expected = file_get_contents($this->_files . '/menu.html');
        $actual = $this->_helper->render();

        // teardown
        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testHasContainer()
    {
        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setContainer(null);
        $this->assertFalse($this->_helper->hasContainer());
        $this->_helper->setContainer($oldContainer);
    }

    public function testInjectingContainer()
    {
        // setup
        $this->_helper->setContainer($this->_nav2);
        $expected = array(
            'menu' => file_get_contents($this->_files . '/menu2.html'),
            'breadcrumbs' => file_get_contents($this->_files . '/breadcrumbs.html')
        );
        $actual = array();

        // result
        $actual['menu'] = $this->_helper->render();
        $this->_helper->setContainer($this->_nav1);
        $actual['breadcrumbs'] = $this->_helper->breadcrumbs()->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingContainerInjection()
    {
        // setup
        $this->_helper->setInjectContainer(false);
        $this->_helper->setContainer($this->_nav2);
        $this->_helper->menu()->setContainer(null);
        $this->_helper->breadcrumbs()->setContainer(null);

        // result
        $expected = array(
            'menu'        => '',
            'breadcrumbs' => ''
        );
        $actual = array(
            'menu'        => $this->_helper->render(),
            'breadcrumbs' => $this->_helper->breadcrumbs()->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testInjectingAcl()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/menu_acl.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAclInjection()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);
        $this->_helper->setInjectAcl(false);

        $expected = file_get_contents($this->_files . '/menu.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testInjectingTranslator()
    {
        $this->_helper->setTranslator($this->_getTranslator());

        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingTranslatorInjection()
    {
        $this->_helper->setTranslator($this->_getTranslator());
        $this->_helper->setInjectTranslator(false);

        $expected = file_get_contents($this->_files . '/menu.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testSpecifyingDefaultProxy()
    {
        $expected = array(
            'breadcrumbs' => file_get_contents($this->_files . '/breadcrumbs.html'),
            'menu' => file_get_contents($this->_files . '/menu.html')
        );
        $actual = array();

        // result
        $this->_helper->setDefaultProxy('breadcrumbs');
        $actual['breadcrumbs'] = $this->_helper->render($this->_nav1);
        $this->_helper->setDefaultProxy('menu');
        $actual['menu'] = $this->_helper->render($this->_nav1);

        $this->assertEquals($expected, $actual);
    }
}