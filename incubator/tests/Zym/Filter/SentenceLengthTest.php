<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once('PHPUnit/Framework/TestCase.php');

/**
 * @see Zym_Filter_SentenceLength
 */
require_once('Zym/Filter/SentenceLength.php');

/**
 * Test case for the SentenceLength filter.
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License

 */
class Zym_Filter_SentenceLengthTest extends PHPUnit_Framework_TestCase
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
	    $this->_filter = new Zym_Filter_SentenceLength(32, false);
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
	 * Test a too long string
	 *
	 */
	public function testTooLongString()
	{
        $string = 'This is a rather long string that should be filtered to
something which is not as long as this';
        $this->assertSame('This is a rather long string', $this->_filter->filter($string));
	}

    /**
     * Tests removing repeated whitespace
     *
     */
    public function testRepeatedWhitespaceReplacement()
    {
        $string = 'Remove all  repeated   whitespace';
        //$this->assertSame('Remove all repeated whitespace', $this->_filter->filter($string));
        $this->assertSame('Remove all repeated whitespace',
Zym_Filter_SentenceLength::sfilter($string, 32, true));
    }

    /**
     * Tests a short string (that shouldn't be filtered')
     *
     */
    public function testShortString()
    {
        $string = 'This is a short string';
        $this->assertSame($string, $this->_filter->filter($string));
    }

    /**
     * Tests using null as input
     *
     */
    public function testNull()
    {
        $string = null;
        $this->assertSame('', $this->_filter->filter($string));
    }

    /**
     * Tests using integer input
     *
     */
    public function testInteger()
    {
        $int = 1337;
        $this->assertSame('1337', $this->_filter->filter($int));
    }

    /**
     * Tests using an object as input
     *
     */    
    public function testObject()
    {
        $obj = new Zym_Filter_SentenceLength();

        try {
            $this->_filter->filter($obj);
        } catch (Exception $e) {
            return;
        }

        $this->fail('An exception has not been raised');
    }
}

