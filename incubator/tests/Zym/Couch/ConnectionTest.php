<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zym/Couch/Connection.php';

// TODO: Stop using the CouchDb test suite databases
class Zym_Couch_ConnectionTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Enter description here...
     *
     * @var Zym_Couch_Connection
     */
    private $_couchConn = null;
    
    public function setUp()
    {
        $this->_couchConn = new Zym_Couch_Connection();
    }
    
    public function testListAll()
    {
        $list = $this->_couchConn->listAll();
        $required = array('test_suite_db', 
                          'test_suite_db_a',
                          'test_suite_db_b');
        
        $this->assertEquals(3, count(array_intersect($list, $required)));
    }
    
    public function testInfoNoParam()
    {
        $info = $this->_couchConn->info();
        $expected = array('couchdb' => 'Welcome');
        
        $this->assertEquals(2, count($info));
        $this->assertGreaterThanOrEqual(1, count(array_intersect($expected, $info)));
    }
    
    public function testInfoSpecifyDb()
    {
        $info = $this->_couchConn->info('test_suite_db');
        $expected = array('db_name'   => 'test_suite_db',
                          'doc_count' => 1);
        
        $this->assertEquals(6, count($info));
        $this->assertGreaterThanOrEqual(2, count(array_intersect($expected, $info)));
    }
    
    public function testCreateAndDeleteDb()
    {
        $createResponse = $this->_couchConn->createDb('foo');
        $this->assertEquals(201, $createResponse->getStatus());
        $this->assertEquals(array('ok' => true), $createResponse->getBody(true));
        
        $deleteResponse = $this->_couchConn->deleteDb('foo');
        $this->assertEquals(200, $deleteResponse->getStatus());
        $this->assertEquals(array('ok' => true), $deleteResponse->getBody(true));
    }
    
    public function testCreateExistingDbFails()
    {
        try {
            $this->_couchConn->createDb('test_suite_db');
            $this->fail('No exception thrown');
        } catch (Exception $e) {
            $this->assertEquals('Zym_Couch_Exception', get_class($e));
        }
    }
    
    public function testDeleteNonExistingDbFails()
    {
        try {
            $this->_couchConn->deleteDb('NON_EXISTING_DB');
            $this->fail('No exception thrown');
        } catch (Exception $e) {
            $this->assertEquals('Zym_Couch_Exception', get_class($e));
        }
    }
}