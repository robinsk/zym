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
 * @license    http://www.zym-project.com/license New BSD License
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
 * @see Zym_View_Helper_BaseUrl
 */
require_once 'Zym/View/Helper/BaseUrl.php';

/**
 * BaseUrl Test Case
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_BaseUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Previous baseUrl before changing
     *
     * @var string
     */
    protected $_previousBaseUrl;

    /**
     * View helper
     *
     * @var Zym_View_Helper_BaseUrl
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_helper          = new Zym_View_Helper_BaseUrl();
        $this->_previousBaseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_helper = null;
        Zend_Controller_Front::getInstance()->setBaseUrl($this->_previousBaseUrl);
        Zend_Controller_Front::getInstance()->resetInstance();
    }

    /**
     * Test and make sure base url returned is consistent with the FC
     *
     */
    public function testBaseUrlIsSameAsFrontController()
    {
        $baseUrls = array('', '/subdir', '/subdir/', '/sub/sub/dir');
        foreach ($baseUrls as $baseUrl) {
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
            $this->assertEquals(rtrim($baseUrl, '/\\'), $this->_helper->baseUrl());
        }
    }

    /**
     * Test and make sure if paths given without / prefix are fixed
     *
     */
    public function testBaseUrlIsCorrectingFilePath()
    {
        $baseUrls = array(
            ''             => '/file.js',
            '/subdir'      => '/subdir/file.js',
            '/sub/sub/dir' => '/sub/sub/dir/file.js',
        );

        foreach ($baseUrls as $baseUrl => $val) {
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);

            $this->assertEquals($val, $this->_helper->baseUrl('file.js'));
        }
    }

    /**
     * Test and make sure baseUrl appended with file works
     *
     */
    public function testBaseUrlIsAppendedWithFile()
    {
        $baseUrls = array(
            ''             => '/file.js',
            '/subdir'      => '/subdir/file.js',
            '/sub/sub/dir' => '/sub/sub/dir/file.js',
        );

        foreach ($baseUrls as $baseUrl => $val) {
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);

            $this->assertEquals($val, $this->_helper->baseUrl('/file.js'));
        }
    }

    /**
     * Test and makes sure that baseUrl appended with path works
     *
     */
    public function testBaseUrlIsAppendedWithPath()
    {
        $baseUrls = array(
            ''             => '/path/bar',
            '/subdir'      => '/subdir/path/bar',
            '/sub/sub/dir' => '/sub/sub/dir/path/bar',
        );

        foreach ($baseUrls as $baseUrl => $val) {
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);

            $this->assertEquals($val, $this->_helper->baseUrl('/path/bar'));
        }
    }
}