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
    protected $_oldIncludePath;

    protected function setUp()
    {
        // store old include path
        $this->_oldIncludePath = get_include_path();

        // add _files dir to include path
        $addToPath = dirname(__FILE__) . '/_files';
        set_include_path($addToPath . PATH_SEPARATOR . $this->_oldIncludePath);
    }

    protected function tearDown()
    {
        // reset include path
        set_include_path($this->_oldIncludePath);
    }

    public function testDetectMvcPage()
    {
        $pages = array(
            Zym_Navigation_Page::factory(array(
                'label' => 'MVC Page',
                'action' => 'index'
            )),
            Zym_Navigation_Page::factory(array(
                'label' => 'MVC Page',
                'controller' => 'index'
            )),
            Zym_Navigation_Page::factory(array(
                'label' => 'MVC Page',
                'module' => 'index'
            )),
            Zym_Navigation_Page::factory(array(
                'label' => 'MVC Page',
                'route' => 'home'
            ))
        );

        $this->assertContainsOnly('Zym_Navigation_Page_Mvc', $pages);
    }

    public function testDetectUriPage()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'URI Page',
            'uri' => '#'
        ));

        $this->assertType('Zym_Navigation_Page_Uri', $page);
    }

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

    public function testSupportsMvcShorthand()
    {
        $mvcPage = Zym_Navigation_Page::factory(array(
            'type' => 'mvc',
            'label' => 'MVC Page',
            'action' => 'index',
            'controller' => 'index'
        ));

        $this->assertType('Zym_Navigation_Page_Mvc', $mvcPage);
    }

    public function testSupportsUriShorthand()
    {
        $uriPage = Zym_Navigation_Page::factory(array(
            'type' => 'uri',
            'label' => 'URI Page',
            'uri' => 'http://www.example.com/'
        ));

        $this->assertType('Zym_Navigation_Page_Uri', $uriPage);
    }

    public function testSupportsCustomPageTypes()
    {
        $page = Zym_Navigation_Page::factory(array(
            'type' => 'My_Page',
            'label' => 'My Custom Page'
        ));

        return $this->assertType('My_Page', $page);
    }

    public function testShouldFailForInvalidType()
    {
        try {
            $page = Zym_Navigation_Page::factory(array(
                'type' => 'My_InvalidPage',
                'label' => 'My Invalid Page'
            ));
        } catch(Zym_Navigation_Exception $e) {
            return;
        }

        $this->fail('An exception has not been thrown for invalid page type');
    }

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

    public function testShouldFailIfUnableToDetermineType()
    {
        try {
            $page = Zym_Navigation_Page::factory(array(
                'label' => 'My Invalid Page'
            ));
        } catch(Zym_Navigation_Exception $e) {
            return;
        }

        $this->fail('An exception has not been thrown for invalid page type');
    }
}