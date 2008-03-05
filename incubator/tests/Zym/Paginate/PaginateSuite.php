<?php
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'trunk/incubator/tests/Zym/Paginate/Zym_Paginate_AssocArrayTest.php';
require_once 'trunk/incubator/tests/Zym/Paginate/Zym_Paginate_NumericArrayTest.php';
require_once 'trunk/incubator/tests/Zym/Paginate/Zym_Paginate_IteratorTest.php';
/**
 * Static test suite.
 */
class PaginateSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Constructs the test suite handler.
     */
    public function __construct ()
    {
        $this->setName('PaginateSuite');
        $this->addTestSuite('Zym_Paginate_AssocArrayTest');
        $this->addTestSuite('Zym_Paginate_IteratorTest');
        $this->addTestSuite('Zym_Paginate_NumericArrayTest');
    }
    /**
     * Creates the suite.
     */
    public static function suite ()
    {
        return new self();
    }
}

