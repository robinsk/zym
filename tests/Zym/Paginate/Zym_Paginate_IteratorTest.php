<?php
require_once 'trunk/library/Zym/Paginate/Iterator.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Zym_Paginate_Iterator test case.
 */
class Zym_Paginate_IteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Paginate_Iterator
     */
    private $Zym_Paginate_Iterator;
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

        $arrayObject = new ArrayObject($mockData);

        $this->Zym_Paginate_Iterator = new Zym_Paginate_Iterator($arrayObject->getIterator());
        $this->Zym_Paginate_Iterator->setRowLimit(2);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Paginate_Iterator = null;
        parent::tearDown();
    }

    public function testGetPage ()
    {
        $page = $this->Zym_Paginate_Iterator->getPage(2);
        $this->assertType('LimitIterator', $page);

        $expected = array('kitem3' => 'vitem3', 'kitem4' => 'vitem4');
        $content = array();

        foreach ($page as $key => $value) {
        	$content[$key] = $value;
        }

        $this->assertEquals($expected, $content);
    }
}

