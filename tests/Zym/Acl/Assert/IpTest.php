<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Message
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Acl
 */
require_once 'Zend/Acl.php';

/**
 * @see Zym_Acl_Assert_Ip
 */
require_once 'Zym/Acl/Assert/Ip.php';

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zym_Acl_Assert_Ip test case.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Message
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Acl_Assert_IpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Assert_Ip instance
     *
     * @var Zym_Acl_Assert_Ip
     */
    private $_assertIp;

    /**
     * Acl instance
     *
     * @var Zend_Acl
     */
    private $_acl;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $whitelist = array('1.2.3.4',
                           '5.6.*',
                           '7.8.9.(1-9)');

        $this->_assertIp = new Zym_Acl_Assert_Ip($whitelist);
        $this->_acl      = new Zend_Acl();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_acl      = null;
        $this->_assertIp = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Acl_Assert_Ip->assert()
     */
    public function testAssertInList()
    {
        $ip = $this->_assertIp;
        
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
        $allowed = $ip->assert($this->_acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '4.3.2.1';
        $allowed = $ip->assert($this->_acl);
        $this->assertFalse($allowed);
    }

    /**
     * Tests assert() for the wildcard IP
     */
    public function testAssertWildcard()
    {
        $ip = $this->_assertIp;
        
        $_SERVER['REMOTE_ADDR'] = '5.6.7.8';
        $allowed = $ip->assert($this->_acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '5.7.8.9';
        $allowed = $ip->assert($this->_acl);
        $this->assertFalse($allowed);
    }

    /**
     * Tests assert() for a ranged IP address
     */
    public function testAssertRange()
    {
        $ip = $this->_assertIp;
                
        $_SERVER['REMOTE_ADDR'] = '7.8.9.0';
        $allowed = $ip->assert($this->_acl);
        $this->assertFalse($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.1';
        $allowed = $ip->assert($this->_acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.5';
        $allowed = $ip->assert($this->_acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.9';
        $allowed = $ip->assert($this->_acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.10';
        $allowed = $ip->assert($this->_acl);
        $this->assertFalse($allowed);
    }
}