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
 * @see Zym_Paginate_Iterator
 */
require_once 'trunk/library/Zym/Paginate/Iterator.php';

/**
 * Test suite for Zym_Notification_Message
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
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

    /**
     * Test the getPage method
     */
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

