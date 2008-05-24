<?php
/**
 * @author 	Martin Hujer mhujer@gmail.com
 */
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'trunk/incubator/library/Zym/View/Helper/FileSize.php';
/**
 * Zym_View_Helper_FileSize test case.
 */
class Zym_View_Helper_FileSizeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zym_View_Helper_FileSize
     */
    private $_fs;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->_fs = new Zym_View_Helper_FileSize();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->_fs = null;
        parent::tearDown();
    }

    /**
     * Tests Mhujer_View_Helper_FileSize->fileSize()
     */
    public function testFileSize ()
    {
        $equals = array(
            "0 B" => 0,
            "1 B" => 1,
            "1 kB" => 1024,
        	"1 MB" => 1024*1024,
            "1 GB" => 1024*1024*1024,
            "1 TB" => 1024*1024*1024*1024,
            "1024 TB" => 1024*1024*1024*1024*1024
        );
        foreach ($equals as $result => $size) {
            $this->assertEquals($result, $this->_fs->fileSize($size));
        }
    }

    /**
     * Test filesize convert with specified precision
     */
    public function testFileSizePrecision()
    {
        $this->assertEquals("976.563 kB", $this->_fs->fileSize(1000000, 3));
        $this->assertEquals("976.5625 kB", $this->_fs->fileSize(1000000, 4));
        $this->assertEquals("976.5625000000 kB", $this->_fs->fileSize(1000000, 10));
    }

    /**
     * Test defined export type
     */
    public function testDefinedType()
    {
        $this->assertEquals('1048576 kB', $this->_fs->fileSize(1024*1024*1024, null, 'KILOBYTE'));
        $this->assertEquals('1024 MB', $this->_fs->fileSize(1024*1024*1024, null, 'MEGABYTE'));
        $this->assertEquals('1 GB', $this->_fs->fileSize(1024*1024*1024, null, 'GIGABYTE'));
        
    }

    /**
     * Test iec convert
     */
    public function testIec()
    {
        $this->assertEquals('1 B', $this->_fs->fileSize(1, null, null, true));
        $this->assertEquals('1 kB.', $this->_fs->fileSize(1024, null, null, true));
        $this->assertEquals('1 MB.', $this->_fs->fileSize(1024*1024, null, null, true));
        $this->assertEquals('1 GB.', $this->_fs->fileSize(1024*1024*1024, null, null, true));
        
    }
}

