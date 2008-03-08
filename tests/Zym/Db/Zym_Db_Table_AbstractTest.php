<?php
require_once 'trunk/library/Zym/Db/Table/Abstract.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zym_Db_Table_Abstract_Test extends Zym_Db_Table_Abstract
{
    protected $_name = 'zym_db_table';
}

/**
 * Zym_Db_Table_Abstract test case.
 */
class Zym_Db_Table_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Db_Table_Abstract
     */
    private $Zym_Db_Table_Abstract;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $dbconn = Zend_Db::factory('pdo_mysql', array('username' => 'zym',
                                                      'password' => 'zym',
                                                      'host'     => 'localhost',
                                                      'dbname'   => 'test'));
        Zend_Db_Table_Abstract::setDefaultAdapter($dbconn);

        $this->Zym_Db_Table_Abstract = new Zym_Db_Table_Abstract_Test();
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Db_Table_Abstract = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Db_Table_Abstract->addReference()
     */
    public function testAddReference ()
    {
        $this->Zym_Db_Table_Abstract->addReference('Reporter', 'reported_by', 'Accounts', 'account_name')
                                    ->addReference('Engineer', 'assigned_to', 'Accounts', 'account_name')
                                    ->addReference('Verifier', array('verified_by'), 'Accounts', array('account_name'));
        $reference = $this->Zym_Db_Table_Abstract->getReference('Reporter');
        $expected = array('columns'       => 'reported_by',
                          'refTableClass' => 'Accounts',
                          'refColumns'    => 'account_name');

        $this->assertEquals($expected, $reference);
    }
    /**
     * Tests Zym_Db_Table_Abstract->isIdentity()
     */
    public function testIsIdentity ()
    {
        $this->assertTrue($this->Zym_Db_Table_Abstract->isIdentity('id'));
        $this->assertFalse($this->Zym_Db_Table_Abstract->isIdentity('key'));
        $this->assertFalse($this->Zym_Db_Table_Abstract->isIdentity('value'));
    }
}

