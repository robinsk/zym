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
 * @package    Zym_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Db_Table_Abstract
 */
require_once 'Zym/Db/Table/Abstract.php';

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';

/**
 * Zym_Db_Table_Abstract test case.
 * 
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Db_Table_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Table instance
     *
     * @var Zym_Db_Table_Abstract
     */
    private $_zymTable;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $adapter = new Zend_Db_Adapter_Pdo_Sqlite(array(
            'dbname' => dirname(__FILE__) . '/_files/testdb.sqlite'
        ));
        
        Zend_Db_Table_Abstract::setDefaultAdapter($adapter);

        $this->_zymTable = new Bugs();
        $this->_zymTable->addReference('Reporter', 'reported_by', 'Accounts', 'account_name')
                        ->addReference('Engineer', 'assigned_to', 'Accounts', 'account_name')
                        ->addReference('Verifier', array('verified_by'), 'Accounts', array('account_name'));
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Zym_Db_Table_Abstract = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Db_Table_Abstract->addReference()
     */
    public function testAddReference()
    {
        
                                    
        $reference = $this->_zymTable->getReference('Reporter');
        
        $expected = array('columns'       => 'reported_by',
                          'refTableClass' => 'Accounts',
                          'refColumns'    => 'account_name');

        $this->assertEquals($expected, $reference);
    }

    /**
     * Tests Zym_Db_Table_Abstract->isIdentity()
     */
    public function testIsIdentity()
    {
        $this->assertTrue($this->_zymTable->isIdentity('id'));
        $this->assertFalse($this->_zymTable->isIdentity('assigned_to'));
        $this->assertFalse($this->_zymTable->isIdentity('verified_by'));
        $this->assertFalse($this->_zymTable->isIdentity('reported_by'));
    }
}

/**
 * Custom table class used for testing
 * TODO: Move this to seperate files
 */

class Accounts extends Zym_Db_Table_Abstract
{
    protected $_name = 'Accounts';
}

class Bugs extends Zym_Db_Table_Abstract
{
    protected $_name = 'Bugs';
}