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
            "1 KB" => 1024,
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
        $this->assertEquals("976.563 KB", $this->_fs->fileSize(1000000, 3));
        $this->assertEquals("976.5625 KB", $this->_fs->fileSize(1000000, 4));
        $this->assertEquals("976.5625 KB", $this->_fs->fileSize(1000000, 10));
    }
}

