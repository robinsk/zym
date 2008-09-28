<?php
/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Config.php';

require_once 'Zym/Couch.php';

class Zym_CouchTest extends PHPUnit_Framework_TestCase
{
    private $_couchHost = 'localhost';
    private $_couchPort = 5984;
    
    public function testConfigFromZendConfig()
    {
        $config = new Zend_Config(array('host' => $this->_couchHost,
                                        'port' => $this->_couchPort));
        
        $this->assertType('Zym_Couch_Connection', Zym_Couch::factory($config));
    }
    
    public function testConfigFromArray()
    {
        $config = array('host' => $this->_couchHost,
                        'port' => $this->_couchPort);
        
        $this->assertType('Zym_Couch_Connection', Zym_Couch::factory($config));
    }
    
    public function testEmptyConfig()
    {
        $conn = Zym_Couch::factory();
        
        $this->assertType('Zym_Couch_Connection', $conn);
    }
}