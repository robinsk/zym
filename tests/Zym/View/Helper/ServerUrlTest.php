<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
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
 * @see Zym_View_Helper_ServerUrl
 */
require_once 'Zym/View/Helper/ServerUrl.php';

/**
 * ServerUrl Test Case
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_ServerUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * View helper
     *
     * @var Zym_View_Helper_ServerUrl
     */
    protected $_helper;

    /**
     * Back up of $_SERVER
     *
     * @var array
     */
    protected $_serverBackup;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_serverBackup    = $_SERVER;
        $_SERVER['REQUEST_URI'] = '/test';
        $this->_helper          = new Zym_View_Helper_ServerUrl();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_helper = null;
        $_SERVER = $this->_serverBackup;
    }

    public function testServerUrlWithNonStandardPort()
    {
        // Non standard port
        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $this->assertEquals('http://example.com:8888', $this->_helper->serverUrl());


        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $this->assertEquals('http://example.com:8888', $this->_helper->serverUrl());

        $this->assertEquals('http://example.com:8888/test', $this->_helper->serverUrl('/test'));
    }

    public function testServerUrlWithNonStandardPortSecure()
    {
        // Non standard port
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['HTTPS'] = true;
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $this->assertEquals('https://example.com:8888', $this->_helper->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $this->assertEquals('https://example.com:8888', $this->_helper->serverUrl());

        $this->assertEquals('https://example.com:8888/test', $this->_helper->serverUrl('/test'));
    }

    public function testServerUrlWithStandardPort()
    {
        // Non standard port
        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertEquals('http://example.com', $this->_helper->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '80';
        $this->assertEquals('http://example.com', $this->_helper->serverUrl());

        $this->assertEquals('http://example.com/test', $this->_helper->serverUrl('/test'));
    }

    public function testServerUrlWithStandardPortSecure()
    {
        // Non standard port
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['HTTPS'] = true;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertEquals('https://example.com', $this->_helper->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '443';
        $this->assertEquals('https://example.com', $this->_helper->serverUrl());

        $this->assertEquals('https://example.com/test', $this->_helper->serverUrl('/test'));
    }
}