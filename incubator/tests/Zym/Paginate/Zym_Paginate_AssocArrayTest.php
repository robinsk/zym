<?php
require_once 'trunk/incubator/library/Zym/Paginate/Array.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Paginate_AssocArrayTest test case.
 */
class Zym_Paginate_AssocArrayTest extends PHPUnit_Framework_TestCase
{
    protected $mockData = array();

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
            $mockData['k' . $tmp] = 'v' . $tmp;
        }

        $this->Zym_Paginate_Array = new Zym_Paginate_Array($mockData);
        $this->Zym_Paginate_Array->setRowLimit(2);
    }

    public function testPageCount()
    {
        $this->assertEquals(5, $this->Zym_Paginate_Array->getPageCount());
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Paginate_Array = null;
        parent::tearDown();
    }
}

