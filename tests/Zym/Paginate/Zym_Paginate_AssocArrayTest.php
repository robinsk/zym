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
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Paginate_Array
 */
require_once 'trunk/library/Zym/Paginate/Array.php';

/**
 * Test suite for Zym_Notification_Message
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Paginate_AssocArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock data
     *
     * @var array
     */
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

    /**
     * Test getPageCount() method
     */
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