<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_PhpUnit_Framework_TestSuite
 */
require_once 'Zym/PhpUnit/Framework/TestSuite.php';

/**
 * TestSuite
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_FilterSuite extends Zym_PhpUnit_Framework_TestSuite 
{
    /**
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(__FILE__);   
    }
    
    /**
     * Get suite
     *
     * @return Zym_PhpUnit_Framework_TestSuite
     */
    public static function suite()
    {
        return new self();
    }
}