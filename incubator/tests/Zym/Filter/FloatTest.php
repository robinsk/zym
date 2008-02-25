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
 * @package Zym_Filter
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once('PHPUnit/Framework/TestCase.php');

/**
 * @see Zym_Filter_Float
 */
require_once('Zym/Filter/Float.php');

/**
 * Fake filter that does not do anything
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Filter
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Filter_FloatTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zym Filter
     *
     * @var Zym_Filter_Interface
     */
    protected $_filter;	
    
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
	    $this->_filter = new Zym_Filter_Float();
		parent::setUp();	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->_filter = null;	
		parent::tearDown();
	}
	
	/**
	 * Test string to float conversion
	 *
	 */
	public function testStringToFloat()
	{
	    $locale = localeconv();
        $string = "13{$locale['decimal_point']}543";
        $this->assertSame(13.543, $this->_filter->filter($string));
	}
	
	/**
	 * Test string with thousands separator
	 *
	 */
	public function testStringWithThousandsSeparator()
	{
	    $locale = localeconv();
	    $string = "1{$locale['thousands_sep']}300";
	    $this->assertSame(1300, $this->_filter->filter($string));
	}
	
	/**
	 * Test float to float
	 *
	 */
	public function testFloat()
	{
	    $float = (float) 13.54;
	    $this->assertSame($float, $this->_filter->filter($float));
	}
	
	/**
	 * Test int to float
	 *
	 */
	public function testIntToFloat()
	{
	    $int = (int) 5;
	    $this->assertSame((float) 5, $this->_filter->filter($int));
	}
}