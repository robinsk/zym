<?php
require_once 'trunk/incubator/library/Zym/Paginate/Array.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Paginate_NumericArrayTest test case.
 */
class Zym_Paginate_NumericArrayTest extends PHPUnit_Framework_TestCase
{
    private $mockData = array();

    /**
     * @var Zym_Paginate_Array
     */
    private $Zym_Paginate_Array;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $mockData = array();
        for ($i = 1; $i <= 10; $i++) {
            $tmp = 'item' . $i;
            $mockData[] = 'v' . $tmp;
        }
        $this->mockData = $mockData;

        $this->Zym_Paginate_Array = new Zym_Paginate_Array($this->mockData);
        $this->Zym_Paginate_Array->setRowLimit(2);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Paginate_Array = null;
        parent::tearDown();
    }

    public function testCurrent()
    {
        $this->Zym_Paginate_Array->rewind();
        $this->assertEquals(1, $this->Zym_Paginate_Array->current());
    }

    public function testKey()
    {
        $this->Zym_Paginate_Array->rewind();
        $this->assertEquals(1, $this->Zym_Paginate_Array->key());
    }

    public function testNext()
    {
        $this->Zym_Paginate_Array->next();
        $this->assertEquals(2, $this->Zym_Paginate_Array->key());
        $this->Zym_Paginate_Array->rewind();
    }

    public function testRewind()
    {
        $this->Zym_Paginate_Array->rewind();
        $this->assertEquals(1, $this->Zym_Paginate_Array->key());
    }

    public function testValid()
    {
        $this->assertTrue($this->Zym_Paginate_Array->valid());
    }

    public function testCount()
    {
        $this->assertEquals(5, $this->Zym_Paginate_Array->count());
    }

    /**
     * Tests Zym_Paginate_Array->getAllPages()
     */
    public function testGetAllPages ()
    {
        $expected = array(
            0 => array('vitem1', 'vitem2'),
            1 => array('vitem3', 'vitem4'),
            2 => array('vitem5', 'vitem6'),
            3 => array('vitem7', 'vitem8'),
            4 => array('vitem9', 'vitem10')
        );

        $this->assertEquals($expected, $this->Zym_Paginate_Array->getAllPages());
    }
    /**
     * Tests Zym_Paginate_Array->getPage()
     */
    public function testGetPage ()
    {
        $page = array('vitem1', 'vitem2');

        $this->assertEquals($page, $this->Zym_Paginate_Array->getPage(1));

        try {
            $this->Zym_Paginate_Array->getPage(100);
            $this->fail('No exception throw');
        } catch (Zym_Paginate_Exception_PageNotFound $e) {
            $this->assertType('Zym_Paginate_Exception_PageNotFound', $e);
            $this->assertEquals('Page "100" not found', $e->getMessage());
        }
    }

    public function testHasPages()
    {
        $this->assertTrue($this->Zym_Paginate_Array->hasPages());
    }

    public function testHasNext()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertTrue($this->Zym_Paginate_Array->hasNext());
        $this->Zym_Paginate_Array->setCurrentPageNumber(2);
        $this->assertTrue($this->Zym_Paginate_Array->hasNext());
        $this->Zym_Paginate_Array->setCurrentPageNumber(5);
        $this->assertFalse($this->Zym_Paginate_Array->hasNext());
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    public function testHasPrevious()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertFalse($this->Zym_Paginate_Array->hasPrevious());
        $this->Zym_Paginate_Array->setCurrentPageNumber(2);
        $this->assertTrue($this->Zym_Paginate_Array->hasPrevious());
        $this->Zym_Paginate_Array->setCurrentPageNumber(5);
        $this->assertTrue($this->Zym_Paginate_Array->hasPrevious());
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    public function testGetItemNumber()
    {
        $itemNumber = $this->Zym_Paginate_Array->getItemNumber(1, 3);
        $this->assertEquals(5, $itemNumber);
        $itemNumber2 = $this->Zym_Paginate_Array->getItemNumber(2);
        $this->assertEquals(2, $itemNumber2);
    }

    public function testGetPageCount()
    {
        $this->assertEquals(5, $this->Zym_Paginate_Array->getPageCount());
    }

    public function testGetRowCount()
    {
        $this->assertEquals(10, $this->Zym_Paginate_Array->getRowCount());
    }

    public function testGetRowLimit()
    {
        $this->assertEquals(2, $this->Zym_Paginate_Array->getRowLimit());
    }

    public function testSetRowLimit()
    {
        $this->Zym_Paginate_Array->setRowLimit(5);
        $this->assertEquals(5, $this->Zym_Paginate_Array->getRowLimit());
        $this->Zym_Paginate_Array->setRowLimit(2);
    }

    public function testSetCurrentPageNumber()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(2);
        $this->assertEquals(2, $this->Zym_Paginate_Array->getCurrentPageNumber());
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertEquals(1, $this->Zym_Paginate_Array->getCurrentPageNumber());

        try {
            $this->Zym_Paginate_Array->setCurrentPageNumber(100);
            $this->fail('No exception throw');
        } catch (Zym_Paginate_Exception_PageNotFound $e) {
            $this->assertType('Zym_Paginate_Exception_PageNotFound', $e);
            $this->assertEquals('Page "100" not found', $e->getMessage());
        }

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    public function testGetCurrentPageNumber()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertEquals(1, $this->Zym_Paginate_Array->getCurrentPageNumber());
        $this->Zym_Paginate_Array->setCurrentPageNumber(2);
        $this->assertEquals(2, $this->Zym_Paginate_Array->getCurrentPageNumber());
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    public function testIsCurrentPageNumber()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertTrue($this->Zym_Paginate_Array->isCurrentPageNumber(1));
    }

    /**
     * Get the current page
     *
     * @return Zend_Db_Table_Rowset_Abstract|array
     */
    public function testGetCurrentPage()
    {
        $page = array('vitem1', 'vitem2');

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertEquals($page, $this->Zym_Paginate_Array->getCurrentPage());
    }

    /**
     * Get the next page
     *
     * @throws Zym_Paginate_Exception_NoNextPage
     * @return Zend_Db_Table_Rowset_Abstract|array
     */
    public function testGetNextPage()
    {
        $page = array('vitem3', 'vitem4');

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertEquals($page, $this->Zym_Paginate_Array->getNextPage());
        $this->Zym_Paginate_Array->setCurrentPageNumber(5);

        try {
            $this->Zym_Paginate_Array->getNextPage();
        } catch (Zym_Paginate_Exception_NoNextPage $e) {
            $this->assertType('Zym_Paginate_Exception_NoNextPage', $e);
            $this->assertEquals('No next page', $e->getMessage());
        }

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    /**
     * Get the next page number
     *
     * @return int
     */
    public function testGetNextPageNumber()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
        $this->assertEquals(2, $this->Zym_Paginate_Array->getNextPageNumber());

        $this->Zym_Paginate_Array->setCurrentPageNumber(5);

        try {
            $this->Zym_Paginate_Array->getNextPageNumber();
        } catch (Zym_Paginate_Exception_NoNextPage $e) {
            $this->assertType('Zym_Paginate_Exception_NoNextPage', $e);
            $this->assertEquals('No next page', $e->getMessage());
        }

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    /**
     * Get the previous page
     *
     * @throws Zym_Paginate_Exception_NoPreviousPage
     * @return Zend_Db_Table_Rowset_Abstract|array
     */
    public function testGetPreviousPage()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);

        try {
            $this->Zym_Paginate_Array->getPreviousPage();
        } catch (Zym_Paginate_Exception_NoPreviousPage $e) {
            $this->assertType('Zym_Paginate_Exception_NoPreviousPage', $e);
            $this->assertEquals('No previous page', $e->getMessage());
        }

        $this->Zym_Paginate_Array->setCurrentPageNumber(2);
        $page = array('vitem1', 'vitem2');

        $this->assertEquals($page, $this->Zym_Paginate_Array->getPreviousPage());

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }

    /**
     * Get previous page nr
     *
     * @return int
     */
    public function testGetPreviousPageNumber()
    {
        $this->Zym_Paginate_Array->setCurrentPageNumber(1);

        try {
            $this->Zym_Paginate_Array->getPreviousPageNumber();
        } catch (Zym_Paginate_Exception_NoPreviousPage $e) {
            $this->assertType('Zym_Paginate_Exception_NoPreviousPage', $e);
            $this->assertEquals('No previous page', $e->getMessage());
        }

        $this->Zym_Paginate_Array->setCurrentPageNumber(2);

        $this->assertEquals(1, $this->Zym_Paginate_Array->getPreviousPageNumber());

        $this->Zym_Paginate_Array->setCurrentPageNumber(1);
    }
}

