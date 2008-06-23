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
 * Zym_Db_Table_Abstract test case.
 * @TODO Create a DB test environment once and for all
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
    private $Zym_Db_Table_Abstract;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
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
    public function testIsIdentity()
    {
        $this->assertTrue($this->Zym_Db_Table_Abstract->isIdentity('id'));
        $this->assertFalse($this->Zym_Db_Table_Abstract->isIdentity('key'));
        $this->assertFalse($this->Zym_Db_Table_Abstract->isIdentity('value'));
    }
}

/**
 * Custom table class used for testing
 */
class Zym_Db_Table_Abstract_Test extends Zym_Db_Table_Abstract
{
    protected $_name = 'zym_db_table';
}