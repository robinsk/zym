<?php
require_once 'laboratory/trunk/incubator/library/Zym/Search/Lucene/Index.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Search_Lucene_Index test case.
 */
class Zym_Search_Lucene_IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Search_Lucene_Index
     */
    private $_index;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        // TODO Auto-generated Zym_Search_Lucene_IndexTest::setUp()
        $this->_index = new Zym_Search_Lucene_Index(/* parameters */);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        // TODO Auto-generated Zym_Search_Lucene_IndexTest::tearDown()
        $this->_index = null;
        parent::tearDown();
    }
    
    public function testFactoryThrowsExceptionWithoutPath()
    {
        try {
            Zym_Search_Lucene_Index::factory();
        } catch(Exception $e) {
            $this->assertType('Zym_Search_Lucene_Exception', $e);
            $this->assertEquals('No index path specified', $e->getMessage());
        }
    }
    
    public function testThrowsExceptionWhenNoIndexAndNoCreate()
    {
        try {
            Zym_Search_Lucene_Index::factory('test', true, false);
        } catch(Exception $e) {
            $this->assertType('Zym_Search_Lucene_Exception', $e);
            $this->assertContains('does not exists', $e->getMessage());
        }
    }
    
    public function testCreatesIndexIfNotExists()
    {
        $this->markTestIncomplete('Not yet implemented.');
    }
    
    public function testFetchFromRegistry()
    {
        $this->markTestIncomplete('Not yet implemented.');
    }
    
    public function testOpensIndexIfExists()
    {
        $this->markTestIncomplete('Not yet implemented.');
    }
}