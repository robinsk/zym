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
 * @see Zym_Filter_Sprintf
 */
require_once('Zym/Filter/Nl2Br.php');

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Filter
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Filter_Nl2BrTest extends PHPUnit_Framework_TestCase
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
	    $this->_filter = new Zym_Filter_Nl2Br();
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
	 * Test filter
	 *
	 */
	public function testFilter()
	{
	    $value = "foo\nbar\nbat\t";
	    $this->assertSame(nl2br($value), $this->_filter->filter($value));
	}
}