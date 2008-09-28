<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zym/Couch/Connection.php';

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
        
        var_dump($list->getBody());die();
    }
}