<?php
require_once 'trunk/library/Zym/Acl/Assert/Ip.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Acl_Assert_Ip test case.
 */
class Zym_Acl_Assert_IpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Acl_Assert_Ip
     */
    private $Zym_Acl_Assert_Ip;
    private $Zend_Acl;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $whitelist = array('1.2.3.4',
                           '5.6.*',
                           '7.8.9.(1-9)');

        $this->Zym_Acl_Assert_Ip = new Zym_Acl_Assert_Ip($whitelist);
        $this->Zend_Acl = new Zend_Acl();
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Acl_Assert_Ip = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Acl_Assert_Ip->assert()
     */
    public function testAssertInList()
    {
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '4.3.2.1';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertFalse($allowed);
    }

    public function testAssertWildcard()
    {
        $_SERVER['REMOTE_ADDR'] = '5.6.7.8';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '5.7.8.9';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertFalse($allowed);
    }

    public function testAssertRange()
    {
        $_SERVER['REMOTE_ADDR'] = '7.8.9.0';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertFalse($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.1';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.5';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.9';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertTrue($allowed);

        $_SERVER['REMOTE_ADDR'] = '7.8.9.10';
        $allowed = $this->Zym_Acl_Assert_Ip->assert($this->Zend_Acl);
        $this->assertFalse($allowed);
    }
}

