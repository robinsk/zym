<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Cache
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * TestSuite
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Cache
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_CacheSuite extends PHPUnit_Framework_TestSuite
{    
    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->setName($this->_createName());
        
        $name     = substr($this->getName(), 0, -5);
        $tests    = array();
        
        $componentFile = dirname(__FILE__) . "/{$name}Test.php";
        if (file_exists($componentFile)) {
            $tests[] = $componentFile;
        }
        
        $componentDir = dirname(__FILE__) . '/' . $name;
        if (file_exists($componentDir)) {
            $iterator = new RecursiveDirectoryIterator($componentDir);
            foreach(new RecursiveIteratorIterator($iterator) as $file) {
                if ($file->isFile() && substr($file, -8) == 'Test.php') {
                    $tests[] = (string) $file;
                }
            }
        }
        
        $this->addTestFiles($tests);
    }
    
    /**
     * Get suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        return new self();
    }
    
    /**
     * Create name
     *
     * @return string
     */
    protected function _createName()
    {
        $parts = explode('_', get_class($this));
        $name  = $parts[count($parts) - 1];
        
        return $name;
    }
}