<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_View_Helper_FileSize
 */
require_once 'Zym/View/Helper/FileSize.php';

/**
 * Zym_View_Helper_FileSize test case.
 * 
 * @author  Martin Hujer mhujer@gmail.com
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
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
    protected function setUp()
    {
        $this->_fs = new Zym_View_Helper_FileSize();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_fs = null;
    }

    /**
     * Tests Mhujer_View_Helper_FileSize->fileSize()
     */
    public function testFileSize ()
    {
        $equals = array(
            "0 B"     => 0,
            "1 B"     => 1,
            "1 kB"    => 1024,
            "1 MB"    => 1024 * 1024,
            "1 GB"    => 1024 * 1024 * 1024,
            "1 TB"    => 1024 * 1024 * 1024 * 1024,
            "1024 TB" => 1024 * 1024 * 1024 * 1024 * 1024
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
        $this->assertEquals('1048576 kB', $this->_fs->fileSize(1024 * 1024 * 1024, null, null, 'KILOBYTE'));
        $this->assertEquals('1024 MB', $this->_fs->fileSize(1024 * 1024 * 1024, null, null, 'MEGABYTE'));
        $this->assertEquals('1 GB', $this->_fs->fileSize(1024 * 1024 * 1024, null, null, 'GIGABYTE'));
        
    }
    
	/**
     * Test defined export type
     */
    public function testNormSi()
    {
        $this->assertEquals('1.00000 B', $this->_fs->fileSize(1, 5, 'si'));
        $this->assertEquals('1.00000 kB.', $this->_fs->fileSize(1000, 5, 'si'));
        $this->assertEquals('1.00000 MB.', $this->_fs->fileSize(1000 * 1000, 5, 'si'));
        $this->assertEquals('1.00000 GB.', $this->_fs->fileSize(1000 * 1000 * 1000, 5, 'si'));
        
    }

    /**
     * Test iec convert
     */
    public function testNormIec()
    {
        $this->assertEquals('1 B', $this->_fs->fileSize(1, null, 'iec'));
        $this->assertEquals('1 KiB', $this->_fs->fileSize(1024, null, 'iec'));
        $this->assertEquals('1 MiB', $this->_fs->fileSize(1024 * 1024, null, 'iec'));
        $this->assertEquals('1 GiB', $this->_fs->fileSize(1024 * 1024 * 1024, null, 'iec'));
        
    }
}