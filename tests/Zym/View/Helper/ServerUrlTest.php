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
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SERVER = $this->_serverBackup;
    }

    public function testServerUrlWithNonStandardPort()
    {
        // Non standard port
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com:8888', $url->serverUrl());

        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $this->assertEquals('http://example.com:8888', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com:8888', $url->serverUrl());

        $this->assertEquals('http://example.com:8888/test', $url->serverUrl('/test'));
    }

    public function testServerUrlWithNonStandardPortSecure()
    {
        // Non standard port
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8888', $url->serverUrl());

        $_SERVER['HTTPS']     = true;
        $_SERVER['HTTP_HOST'] = 'example.com:8888';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8888', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8888', $url->serverUrl());

        $this->assertEquals('https://example.com:8888/test', $url->serverUrl('/test'));
    }

    public function testServerUrlWithStandardPort()
    {
        // Non standard port
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());

        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '80';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());

        $this->assertEquals('http://example.com/test', $url->serverUrl('/test'));
    }

    public function testServerUrlWithStandardPortSecure()
    {
        // Non standard port
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());

        $_SERVER['HTTPS'] = true;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());

        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '443';
        $url = new Zym_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());

        $this->assertEquals('https://example.com/test', $url->serverUrl('/test'));
    }
}