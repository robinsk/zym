<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym_Tests
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zym_View_Helper_SimpleUrl
 */
require_once 'Zym/View/Helper/SimpleUrl.php';

/**
 * SimpleUrl Test Case
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym_Tests
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_SimpleUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * View helper
     *
     * @var Zym_View_Helper_SimpleUrl
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        Zend_Controller_Front::getInstance()->getRouter()->addDefaultRoutes();
        $this->_helper = new Zym_View_Helper_SimpleUrl();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->_helper = null;
    }

    public function testSimpleUrl()
    {
        $url = $this->_helper->simpleUrl('test');
        $this->assertEquals('/index/test', $url);
    }

    public function testSimpleUrlWithController()
    {
        $url = $this->_helper->simpleUrl('test', 'test');
        $this->assertEquals('/test/test', $url);
    }

    public function testSimpleUrlWithControllerAndModule()
    {
        $url = $this->_helper->simpleUrl('test', 'test', 'test');
        $this->assertEquals('/test/test/test', $url);
    }

    public function testSimpleUrlWithParams()
    {
        $url = $this->_helper->simpleUrl('test', null, null, array('bar' => '"bat'));
        $this->assertEquals('/index/test/bar/%22bat', $url);
    }

    public function testSimpleUrlWithParamsAndNoEncode()
    {
        $url = $this->_helper->simpleUrl('test', null, null, array('bar' => 'bat&'), false);
        $this->assertEquals('/index/test/bar/bat&', $url);
    }
}