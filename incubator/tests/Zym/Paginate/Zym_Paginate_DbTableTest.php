<?php
require_once 'trunk/incubator/library/Zym/Paginate/DbTable.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Paginate_DbTable test case.
 */
class Zym_Paginate_DbTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Paginate_DbTable
     */
    private $Zym_Paginate_DbTable;
    private $testTable;
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
        $this->testTable = new Paginate_TestTable();

        $select = $this->testTable->select();

        $this->Zym_Paginate_DbTable = new Zym_Paginate_DbTable($this->testTable, $select);
        $this->Zym_Paginate_DbTable->setRowLimit(2);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Paginate_DbTable = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Paginate_DbTable->getPage()
     */
    public function testGetPage ()
    {
        $page = $this->Zym_Paginate_DbTable->getPage(2);
        $this->markTestIncomplete('Check the page');
    }
}

