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
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_View_Helper_Cycle
 */
require_once 'Zym/View/Helper/Cycle.php';

/**
 * Zym_View_Helper_FileSize test case.
 * 
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_CycleTest extends PHPUnit_Framework_TestCase
{

    /**
     * Cycle view helper
     * 
     * @var Zym_View_Helper_Cycke
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_helper = new Zym_View_Helper_Cycle();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_helper = null;
    }
}