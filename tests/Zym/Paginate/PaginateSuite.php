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
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * @see Zym_Paginate_AssocArrayTest
 */
require_once 'trunk/tests/Zym/Paginate/Zym_Paginate_AssocArrayTest.php';

/**
 * @see Zym_Paginate_NumericArrayTest
 */
require_once 'trunk/tests/Zym/Paginate/Zym_Paginate_NumericArrayTest.php';

/**
 * @see Zym_Paginate_IteratorTest
 */
require_once 'trunk/tests/Zym/Paginate/Zym_Paginate_IteratorTest.php';

/**
 * Test suite for Zym_Paginate
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
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