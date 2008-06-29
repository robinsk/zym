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
 * @see Zym_View_Helper_Cycle
 */
require_once 'Zym/View/Helper/Cycle.php';

/**
 * Zym_View_Helper_Cycle test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_CycleTest extends PHPUnit_Framework_TestCase
{
    public function testSetAndGetValues()
    {
        $helper = new Zym_View_Helper_Cycle();
        $helper->setValues(array(1, 2, 3));

        $this->assertEquals(array(1, 2, 3), $helper->getValues());
    }

    public function testGetValue()
    {
        $helper = new Zym_View_Helper_Cycle();
        $helper->setValues(array(1, 2, 3));

        $this->assertEquals(1, $helper->getValue());
    }

    public function testCycleReturnsInstance()
    {
        $helper = new Zym_View_Helper_Cycle();
        $this->assertEquals($helper, $helper->cycle());
    }

    public function testCycleReturnsNextValue()
    {
        $helper = new Zym_View_Helper_Cycle();
        $helper->setValues(array(1, 2, 3));

        $this->assertEquals(1, (string) $helper->cycle());
        $this->assertEquals(2, (string) $helper->cycle());
        $this->assertEquals(3, (string) $helper->cycle());
        $this->assertEquals(1, (string) $helper->cycle());
    }

    public function testCycleSetValues()
    {
        $helper = new Zym_View_Helper_Cycle();
        $helper->cycle(array(1, 2, 3));

        $this->assertEquals(array(1, 2, 3), $helper->getValues());
    }

    public function testToString()
    {
        $helper = new Zym_View_Helper_Cycle();
        $helper->cycle(array(1, 2, 3));

        $this->assertEquals('1', (string) $helper);
        $this->assertEquals('2', $helper->toString());
    }

    public function testIteratorLoop()
    {
        $helper = new Zym_View_Helper_Cycle();
        $helper->setValues(array(1, 2, 3));

        $values = array();
        foreach ($helper as $item) {
        	$values[] = $item;
        }

        $this->assertEquals(array(1, 2, 3), $values);
    }
}