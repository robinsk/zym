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
 * Tests Zym_View_Helper_Breadcrumbs
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

    /**
     * The helper should proxy to the menu helper by default
     *
     */
    public function testShouldProxyToMenuHelperByDeafult()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setContainer(null);

        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setContainer($oldContainer);
        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);
    }
    
    /**
     * The navigation helper should inject its container to proxied helpers
     * 
     */
    public function testShouldBeAbleToInjectContainer()
    {
        // setup
        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setInjectContainer(false);
        $this->_helper->menu()->setContainer(null);
        $this->_helper->breadcrumbs()->setContainer(null);
        
        // sanity check
        $msg = 'Corruption: Proxied helper should not have a container';
        $this->assertEquals(false, $this->_helper->menu()->hasContainer(), $msg);
        $this->assertEquals(false, $this->_helper->breadcrumbs()->hasContainer(), $msg);
        
        // setup
        $this->_helper->setInjectContainer();
        $this->_helper->setContainer($this->_nav2);
        
        // test 1
        $msg = 'Fail: The render method does not inject container by default';
        $expected = file_get_contents($this->_files . '/menu2.html');
        $this->assertEquals($expected, $this->_helper->render(), $msg);
        
        // setup
        $this->_helper->setContainer($this->_nav1);
        
        // test 2
        $msg = 'Fail: The __call method does not inject container by default';
        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $this->assertEquals($expected, $this->_helper->breadcrumbs()->render(), $msg);
        
        // teardown
        $this->_helper->setContainer($oldContainer);
    }
    
    /**
     * It should be possible to disable container injection in the navigation
     * helper
     * 
     */
    public function testShouldBeAbleToDisableContainerInjection()
    {
        // setup
        $oldInject = $this->_helper->getInjectContainer();
        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setInjectContainer(false);
        $this->_helper->setContainer($this->_nav2);
        $this->_helper->menu()->setContainer(null);
        $this->_helper->breadcrumbs()->setContainer(null);
        
        // test
        $expected = '';
        $this->assertEquals($expected, $this->_helper->render());
        $this->assertEquals($expected, $this->_helper->breadcrumbs()->render());
        
        // teardown
        $this->_helper->setInjectContainer($oldInject);
        $this->_helper->setContainer($oldContainer);
    }
    
    /**
     * The helper should automatically inject the correct helper path for
     * navigation helpers
     * 
     */
    public function testGetViewShouldInjectHelperPath()
    {
        // setup
        $view = new Zend_View();
        $view->addHelperPath('Zym/View/Helper', 'Zym_View_Helper');
        $ns = Zym_View_Helper_Navigation_Abstract::NS;
        $ns .= '_';
        
        // sanity check
        $this->assertArrayNotHasKey($ns, $view->getHelperPaths());
        
        // setup
        $oldView = $this->_helper->getView();
        $this->_helper->setView($view);
        
        // test
        $this->assertArrayHasKey($ns, $this->_helper->getView()->getHelperPaths());
        
        // teardown
        $this->_helper->setView($oldView);
    }
    
    public function testSettingAnInvalidProxyShouldThrowAnException()
    {
        // setup
        $oldProxy = $this->_helper->getDefaultProxy();
        
        // test
        try {
            $this->_helper->setDefaultProxy('sdfkugrkjasdfs');
            $failed = true;
        } catch (Zend_View_Exception $e) {
            $failed = false;
        }
        
        // teardown
        $this->_helper->setDefaultProxy($oldProxy);
        
        if ($failed) {
            $this->fail('Setting an invalid proxy should throw Zend_View_Exception');
        }
    }
    
    /**
     * It should be possible to specify another default proxy
     * 
     */
    public function testShouldBeAbleToSetDefaultProxy()
    {
        // setup
        $oldContainer = $this->_helper->getContainer();
        $oldProxy = $this->_helper->getDefaultProxy();
        
        // test
        $this->_helper->setDefaultProxy('breadcrumbs');
        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $this->assertEquals($expected, $this->_helper->render($this->_nav1));
        
        // test
        $this->_helper->setDefaultProxy('menu');
        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->render($this->_nav1));
        
        // teardown
        $this->_helper->setContainer($oldContainer);
        $this->_helper->setDefaultProxy($oldProxy);
    }
    
    /**
     * It should be possible to specify which proxies to allow
     * 
     */
    public function testShouldBeAbleToSpecifyProxies()
    {
        // setup
        $oldProxies = $this->_helper->getProxies();
        $this->_helper->setProxies(array('breadcrumbs'));
        
        // test
        try {
            $helper = $this->_helper->menu();
            $failed = true;
        } catch (BadMethodCallException $e) {
            $failed = false;
        }
        
        // teardown
        $this->_helper->setProxies($oldProxies);
        
        if ($failed) {
            $this->fail('Setting an invalid proxy should throw Zend_View_Exception');
        }
    }
}