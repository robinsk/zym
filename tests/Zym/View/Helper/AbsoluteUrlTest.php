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
 * @see Zym_View_Helper_AbsoluteUrl
 */
require_once 'Zym/View/Helper/AbsoluteUrl.php';

/**
 * AbsoluteUrl Test Case
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_AbsoluteUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * View helper
     *
     * @var Zym_View_Helper_AbsoluteUrl
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
        $this->_serverBackup = $_SERVER;

        $server = array(
            'HTTPS' => true,
            'HTTP_HOST' => 'example.com'
        );
        array_merge($_SERVER, $server);

        $this->_helper = new Zym_View_Helper_AbsoluteUrl();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_helper = null;
        $_SERVER = $this->_serverBackup;
    }

    public function testAbsoluteUrl()
    {
        $url = $this->_helper->absoluteUrl();
        $this->assertEquals('https://example.com/', $url);

        $url = $this->_helper->absoluteUrl(array(
            'controller' => 'test'
        ));
        $this->assertEquals('https://example.com/test', $url);

        $url = $this->_helper->absoluteUrl(array(
            'controller' => 'test',
            'action'     => 'action'
        ));
        $this->assertEquals('https://example.com/test/action', $url);

        $url = $this->_helper->absoluteUrl(array(
            'controller' => 'test',
            'action'     => 'action'
        ), 'default');
        $this->assertEquals('https://example.com/test/action', $url);

        $url = $this->_helper->absoluteUrl(array(
            'controller' => 'test',
            'action'     => 'action&'
        ), 'default', true, false);
        $this->assertEquals('https://example.com/test/action&', $url);
    }

    public function testGetHost()
    {
        $host = $this->_helper->getHost();
        $this->assertEquals('example.com', $host);
    }

    public function testSetHost()
    {
        $helper = clone $this->_helper;
        $helper->setHost('test.com:8888');

        $this->assertEquals('test.com:8888', $helper->getHost());
    }

    public function testGetScheme()
    {
        $host = $this->_helper->getScheme();
        $this->assertEquals('https', $host);
    }

    public function testSetScheme()
    {
        $helper = clone $this->_helper;
        $helper->setHost('http');

        $this->assertEquals('http', $helper->getScheme());
    }

    public function testSchemeAndHostDetection()
    {
        // Non standard port
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] =  'example.com';
        $_SERVER['SERVER_PORT'] = 8888;
        $helper = new Zym_View_Helper_AbsoluteUrl();

        $this->assertEquals('example.com:8888', $helper->getHost());
        $this->assertEquals('http', $helper->getScheme());

        // Standard http port
        $_SERVER['SERVER_NAME'] =  'example.com';
        $_SERVER['SERVER_PORT'] = 80;
        $helper = new Zym_View_Helper_AbsoluteUrl();

        $this->assertEquals('example.com', $helper->getHost());
        $this->assertEquals('https', $helper->getScheme());

        // Standard https port
        $_SERVER['HTTPS']       = true;
        $_SERVER['SERVER_NAME'] =  'example.com';
        $_SERVER['SERVER_PORT'] = 443;
        $helper = new Zym_View_Helper_AbsoluteUrl();

        $this->assertEquals('example.com', $helper->getHost());
        $this->assertEquals('https', $helper->getScheme());
    }
}