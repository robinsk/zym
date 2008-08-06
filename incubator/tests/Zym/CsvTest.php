<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym_Tests
 * @package   Zym_Csv
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Csv
 */
require_once 'Zym/Csv.php';

/**
 * Tests the class Zym_Csv
 *
 * @category  Zym_Tests
 * @package   Zym_Csv
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_CsvTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Zym_Csv
     */
    private $Zym_Csv_NoHeader;
    private $Zym_Csv_Header;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        // TODO Auto-generated Zym_CsvTest::setUp()
        $this->Zym_Csv_Header = new Zym_Csv(dirname(__FILE__) . '/Csv/TestCsv.csv');
        $this->Zym_Csv_NoHeader = new Zym_Csv(dirname(__FILE__) . '/Csv/TestCsv.csv', ',', false);
    }
    /**
     * Cleans up the environment after running a test.
     *
     * @return void
     */
    protected function tearDown()
    {
        // TODO Auto-generated Zym_CsvTest::tearDown()
        $this->Zym_Csv_NoHeader = null;
    }

    public function testConstruct()
    {
        try {
            $test = new Zym_Csv('FileDoesntExists.csv');
        } catch (Zym_Csv_Exception $e) {
            $this->assertTrue($e != null);
        }

        try {
            $test = new Zym_Csv(dirname(__FILE__) . '/Csv/TestCsvNoRead.csv');
        } catch (Zym_Csv_Exception $e) {
            $this->assertTrue($e != null);
        }
    }

/*
    public function testGetRowByColumnName()
    {
        foreach ($this->Zym_Csv_NoHeader as $row) {
            $this->assertEquals('r1c1', $row['col1']);
            $this->assertEquals('r1c2', $row['col2']);
            break;
        }
    }
*/
    /**
     * Tests Zym_Csv->current()
     */
    public function testCurrentNoHeader()
    {
        $current = $this->Zym_Csv_NoHeader->current();

        $this->assertEquals('col1', $current[0]);
        $this->assertEquals('col2', $current[1]);

        $this->Zym_Csv_NoHeader->rewind();
    }

    public function testCurrentHeader()
    {
        $current = $this->Zym_Csv_Header->current();

        $this->assertEquals('r1c1', $current['col1']);
        $this->assertEquals('r1c2', $current['col2']);

        $this->Zym_Csv_Header->rewind();
    }
    /**
     * Tests Zym_Csv->key()
     */
    public function testKey()
    {
        $this->assertEquals(0, $this->Zym_Csv_NoHeader->key());
    }
    /**
     * Tests Zym_Csv->next()
     */
    public function testNext()
    {
        $this->Zym_Csv_NoHeader->rewind();
        $this->assertTrue($this->Zym_Csv_NoHeader->next());
    }
    /**
     * Tests Zym_Csv->rewind()
     */
    public function testRewind()
    {
        $this->Zym_Csv_NoHeader->rewind();
        $this->assertEquals(0, $this->Zym_Csv_NoHeader->key());
    }
    /**
     * Tests Zym_Csv->toArray()
     */
    public function testToArray()
    {
        $array = $this->Zym_Csv_NoHeader->toArray();

        $original = array(array('col1', 'col2'),
                          array('r1c1', 'r1c2'),
                          array('r2c1', 'r2c2'),
                          array('r3c1', 'r3c2'),
                          array('r4c1', 'r4c2'),
                          array('r5c1', 'r5c2'));

        $this->assertEquals($original, $array);
    }
    /**
     * Tests Zym_Csv->valid()
     */
    public function testValid()
    {
        $this->Zym_Csv_NoHeader->rewind();
        $this->assertTrue($this->Zym_Csv_NoHeader->valid());
    }
}