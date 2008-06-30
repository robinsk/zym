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
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Navigation_Page_Mvc
 */
require_once 'Zym/Navigation/Page/Mvc.php';

/**
 * @see Zend_Controller_Request_Http
 */
require_once 'Zend/Controller/Request/Http.php';

/**
 * @see Zend_Controller_Router_Route
 */
require_once 'Zend/Controller/Router/Route.php';

/**
 * Tests the class Zym_Navigation_Page_Mvc
 * 
 * @author    Robin Skoglund
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Navigation_Page_MvcTest extends PHPUnit_Framework_TestCase
{
    protected $_front;
    protected $_oldRequest;
    protected $_oldRouter;
    
    /**
     * Prepares the environment before running a test.
     * 
     */
    protected function setUp()
    {
        $this->_front = Zend_Controller_Front::getInstance();
        $this->_oldRequest = $this->_front->getRequest();
        $this->_oldRouter = $this->_front->getRouter();
        
        $this->_front->resetInstance();
        $this->_front->setRequest(new Zend_Controller_Request_Http());
        $this->_front->getRouter()->addDefaultRoutes();
    }
    
    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {
        if (null !== $this->_oldRequest) {
            $this->_front->setRequest($this->_oldRequest);
        } else {
            $this->_front->setRequest(new Zend_Controller_Request_Http());
        }
        $this->_front->setRouter($this->_oldRouter);
    }
    
    /**
     * Tests that hrefs are generated correctly using the URL helper
     *
     */
    public function testHrefIsGeneratedUsingUrlHelper()
    {
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $this->assertEquals('/', $page->getHref());
        
        $page->setAction('view');
        $page->setController('news');
        $this->assertEquals('/news/view', $page->getHref());
    }
    
    /**
     * Tests that hrefs are generated correctly using the URL helper
     *
     */
    public function testHrefGeneratedIsRouteAware()
    {
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'myaction',
            'controller' => 'mycontroller',
            'route' => 'myroute',
            'params' => array(
                'page' => 1337
            )
        ));
        
        $router = $this->_front->getRouter();
        $route  = new Zend_Controller_Router_Route(
            'lolcat/:action/:page',
            array(
                'module'     => 'default',
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1
            )
        );
        $router->addRoute('myroute', $route);
        
        $this->assertEquals('/lolcat/myaction/1337', $page->getHref());
    }
    
    /**
     * Tests that isActive() tests against actual request params
     *
     */
    public function testIsActiveShouldTestAgainstRequestParams()
    {
        // TODO: implement
        $this->fail('test not implemented yet');
        /*
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $request = $this->_front->getRequest();
        $request->setActionName('index');
        $request->setControllerName('index');
        $request->setModuleName('index');
        $request->setDispatched();
        $this->_front->setRequest($request);
        
        echo "\n\n====================\n";
        
        $reqParams = Zend_Controller_Front::getInstance()
                        ->getRequest()->getParams();
        var_dump($reqParams);
        
        $request = $this->_front->getRequest();
        
        echo "\n====================\n\n";
        
        $this->assertEquals(true, $page->isActive());
        */
    }
    
    /**
     * Tests that the constructor requires label, action and controller
     *
     */
    public function testConstructionRequiresActionAndController()
    {
        try {
            $page = new Zym_Navigation_Page_Mvc(array(
                'labelz' => 'label',
                'action' => 'index',
                'controller' => 'index'
            ));
            $this->fail('Should throw exception for missing label');
        } catch (Zym_Navigation_Page_InvalidException $e) {
            
        }
        
        try {
            $page = new Zym_Navigation_Page_Mvc(array(
                'label' => 'label',
                'actionz' => 'index',
                'controller' => 'index'
            ));
            $this->fail('Should throw exception for missing action');
        } catch (Zym_Navigation_Page_InvalidException $e) {
            
        }
        
        try {
            $page = new Zym_Navigation_Page_Mvc(array(
                'label' => 'label',
                'action' => 'index',
                'controllerz' => 'index'
            ));
            $this->fail('Should throw exception for missing controller');
        } catch (Zym_Navigation_Page_InvalidException $e) {
            
        }
    }
    
    /**
     * Tests simple accessors for string properties controller and action
     *
     */
    public function testActionAndControllerAccessors()
    {
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $props = array('Action', 'Controller');
        $valids = array('index', 'help', 'home', 'default', '1', ' ');
        $invalids = array(42, '', (object) null, null);
        
        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";
            
            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }
            
            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'InvalidArgumentException was thrown';
                    $this->fail($msg);
                } catch (InvalidArgumentException $e) {
                    
                }
            }
        }
    }
    
    /**
     * Tests simple accessors for string properties module and route
     * 
     * Those allow null as opposed to action and controller.
     *
     */
    public function testModuleAndRouteAccessors()
    {
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $props = array('Module', 'Route');
        $valids = array('index', 'help', 'home', 'default', '1', ' ', null);
        $invalids = array(42, '', (object) null);
        
        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";
            
            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }
            
            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'InvalidArgumentException was thrown';
                    $this->fail($msg);
                } catch (InvalidArgumentException $e) {
                    
                }
            }
        }
    }
    
    /**
     * Tests setResetParams() and getResetParams() with valid and invalid values
     *
     */
    public function testSetAndGetResetParams()
    {
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setResetParams($valid);
            $this->assertEquals(true, $page->getResetParams());
        }
        
        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setResetParams($invalid);
            $this->assertEquals(false, $page->getResetParams());
        }
    }
    
    /**
     * Tests setParams() and getParams()
     *
     */
    public function testSetAndGetParams()
    {
        $page = new Zym_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $params = array('foo' => 'bar', 'baz' => 'bat');
        
        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());
        
        $page->setParams();
        $this->assertEquals(array(), $page->getParams());
        
        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());
        
        $page->setParams(array());
        $this->assertEquals(array(), $page->getParams());
    }
    
    /**
     * Tests the toArray() method
     *
     */
    public function testToArrayMethod()
    {
        $options = array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index',
            'module' => 'test',
            'id' => 'my-id',
            'class' => 'my-class',
            'title' => 'my-title',
            'target' => 'my-target',
            'position' => 100,
            'active' => true,
            'visible' => false,
        
            'foo' => 'bar',
            'meaning' => 42
        );
        
        $page = new Zym_Navigation_Page_Mvc($options);
        
        $toArray = $page->toArray();
        
        $options['reset_params'] = true;
        $options['route'] = 'default';
        $options['params'] = array();
        
        $this->assertEquals($options, $toArray);
    }
}