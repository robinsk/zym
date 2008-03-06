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
        /*$dbconn = Zend_Db::factory('pdo_mysql', array('user'     => 'zymtest',
                                                      'password' => 'zymtest',
                                                      'host'     => 'localhost',
                                                      'dbname'   => 'zymtest'));
        Zend_Db_Table_Abstract::setDefaultAdapter($dbconn);
        $this->testTable = new Paginate_TestTable();

        $select = $this->testTable->select();
        // TODO Auto-generated Zym_Paginate_DbTableTest::setUp()
        $this->Zym_Paginate_DbTable = new Zym_Paginate_DbTable($this->testTable, $select);
        $this->Zym_Paginate_DbTable->setRowLimit(2);*/
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Zym_Paginate_DbTableTest::tearDown()
        $this->Zym_Paginate_DbTable = null;
        parent::tearDown();
    }
    /**
     * Constructs the test case.
     */
    public function __construct ()
    {    // TODO Auto-generated constructor
    }
    /**
     * Tests Zym_Paginate_DbTable->getPage()
     */
    public function testGetPage ()
    {
        // TODO Auto-generated Zym_Paginate_DbTableTest->testGetPage()
        $this->markTestIncomplete("getPage test not implemented");
        $this->Zym_Paginate_DbTable->getPage(/* parameters */);
    }
    /**
     * Tests Zym_Paginate_DbTable->__construct()
     */
    public function test__construct ()
    {
        // TODO Auto-generated Zym_Paginate_DbTableTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        $this->Zym_Paginate_DbTable->__construct(/* parameters */);
    }
}

