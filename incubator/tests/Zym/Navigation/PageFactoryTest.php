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
 * @package    Zym_Navigation
 * @subpackage Zym_Navigation_Page
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Navigation_Page
 */
require_once 'Zym/Navigation/Page.php';

/**
 * Tests Zym_Navigation_Page::factory()
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Navigation
 * @subpackage Zym_Navigation_Page
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Navigation_PageFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Contains include path before starting test
     *
     * @var string
     */
    protected $_oldIncludePath;
    
    /**
     * Prepares the environment before running a test
     * 
     */
    protected function setUp()
    {
        // store old include path
        $this->_oldIncludePath = get_include_path();
        
        // add _files dir to include path
        $addToPath = dirname(__FILE__) . '/_files';
        set_include_path($addToPath . PATH_SEPARATOR . $this->_oldIncludePath);
    }

    /**
     * Cleans up the environment after running a test
     * 
     */
    protected function tearDown()
    {
        // reset include path
        set_include_path($this->_oldIncludePath);
    }
    
    /**
     * Factory should detect an MVC page from the options 'action'
     * and 'controller'
     *
     */
    public function testShouldDetectMvcPage()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'MVC Page',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $this->assertType('Zym_Navigation_Page_Mvc', $page);
    }
    
    /**
     * Factory should detect an URI page from the 'uri' option
     *
     */
    public function testShouldDetectUriPage()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'MVC Page',
            'uri' => '#'
        ));
        
        $this->assertType('Zym_Navigation_Page_Uri', $page);
    }
    
    /**
     * When detecting type, MVC pages should have precedence
     *
     */
    public function testMvcShouldHaveDetectionPrecedence()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'MVC Page',
            'action' => 'index',
            'controller' => 'index',
            'uri' => '#'
        ));
        
        $this->assertType('Zym_Navigation_Page_Mvc', $page);
    }
    
    /**
     * Factory should support short 'type' options for mvc and uri
     *
     */
    public function testShouldSupportShortTypes()
    {
        $mvcPage = Zym_Navigation_Page::factory(array(
            'type' => 'mvc',
            'label' => 'MVC Page',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $this->assertType('Zym_Navigation_Page_Mvc', $mvcPage);
        
        $uriPage = Zym_Navigation_Page::factory(array(
            'type' => 'uri',
            'label' => 'URI Page',
            'uri' => 'http://www.example.com/'
        ));
        
        $this->assertType('Zym_Navigation_Page_Uri', $uriPage);
    }
    
    /**
     * The page factory should support custom pages types
     *
     */
    public function testShouldSupportCustomPageTypes()
    {
        $pageConfig = array(
            'type' => 'My_Page',
            'label' => 'My Custom Page'
        );
        
        $page = Zym_Navigation_Page::factory($pageConfig);
        
        return $this->assertEquals('#', $page->getHref());
    }
    
    /**
     * The page factory should not work with page types that don't extend
     * Zym_Navigation_Page
     *
     */
    public function testShouldFailForInvalidType()
    {
        $pageConfig = array(
            'type' => 'My_InvalidPage',
            'label' => 'My Invalid Page'
        );
        
        try {
            $page = Zym_Navigation_Page::factory($pageConfig);
        } catch(InvalidArgumentException $e) {
            return;
        }
        
        $this->fail('An exception has not been thrown for invalid page type');
    }
    
    /**
     * The page factory should not work when neither type, uri, action
     * or controller is given
     *
     */
    public function testShouldFailForInsufficientOptions()
    {
        $pageConfig = array(
            'label' => 'My Invalid Page'
        );
        
        try {
            $page = Zym_Navigation_Page::factory($pageConfig);
        } catch(UnexpectedValueException $e) {
            return;
        }
        
        $this->fail('An UnexpectedValueException has not been thrown');
    }
    
    /**
     * The page factory should not work with page types that don't exist
     *
     */
    public function testShouldFailForNonExistantType()
    {
        $pageConfig = array(
            'type' => 'My_NonExistant_Page',
            'label' => 'My non-existant Page'
        );
        
        try {
            $page = Zym_Navigation_Page::factory($pageConfig);
        } catch(Zend_Exception $e) {
            return;
        }
        
        $msg = 'A Zend_Exception has not been thrown for non-existant class';
        $this->fail($msg);
    }
}