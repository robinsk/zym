<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zym/Couch.php';
require_once 'Zym/Couch/Db.php';

// TODO: Stop using the CouchDb test suite databases
class Zym_Couch_DbTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Enter description here...
     *
     * @var Zym_Couch_Connection
     */
    private $_couchConnection = null;
    
    /**
     * Enter description here...
     *
     * @var Zym_Couch_Db
     */
    private $_couchDb = null;
    
    public function setUp()
    {
        $this->_couchConnection = Zym_Couch::factory();
        $this->_couchDb = $this->_couchConnection->getDb('test_suite_db');
    }
    
    public function testGetDbName()
    {
        $this->assertEquals('test_suite_db', $this->_couchDb->getDbName());
    }
    
    public function testGetConnection()
    {
        $this->assertEquals('Zym_Couch_Connection', get_class($this->_couchDb->getConnection()));
    }
    
    public function testGetRequest()
    {
        $request = $this->_couchDb->getRequest('/foo/bar', Zym_Couch_Request::GET, 'data');
        $this->assertEquals('Zym_Couch_Request', get_class($request));
        $this->assertEquals('/test_suite_db/foo/bar', $request->getUrl());
        $this->assertEquals(Zym_Couch_Request::GET, $request->getMethod());
        $this->assertEquals('data', $request->getData());
    }
    
    public function testGetAllDocs()
    {
        $docs = $this->_couchDb->getAllDocs();
        $this->assertType('array', $docs);
        $this->assertEquals(1, $docs['total_rows']);
        $this->assertEquals(0, $docs['offset']);
        $this->assertEquals(array(array('id'    => 'bin_doc',
                                        'key'   => 'bin_doc',
                                        'value' => array('rev' => '4272993301'))), $docs['rows']);
    }
}