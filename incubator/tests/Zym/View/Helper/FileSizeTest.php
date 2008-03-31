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
        $this->assertEquals("0 B", $this->_fs->fileSize(0));
        $this->assertEquals("1 B", $this->_fs->fileSize(1));
        $this->assertEquals("1 KB", $this->_fs->fileSize(1024));
        $this->assertEquals("1 MB", $this->_fs->fileSize(1048576));
        $this->assertEquals("1 GB", $this->_fs->fileSize(1073741824));
        $this->assertEquals("1 TB", $this->_fs->fileSize(1073741824*1024));
        $this->assertEquals("1024 TB", $this->_fs->fileSize(1073741824*1024*1024));
        $this->assertEquals("976.563 KB", $this->_fs->fileSize(1000000, 3));
        $this->assertEquals("976.5625 KB", $this->_fs->fileSize(1000000, 4));
        $this->assertEquals("976.5625 KB", $this->_fs->fileSize(1000000, 10));
    }
}

