<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym_Tests
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Filter_SentenceLength
 */
require_once 'Zym/Filter/SentenceLength.php';

/**
 * Test case for the SentenceLength filter.
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Filter_SentenceLengthTest extends PHPUnit_Framework_TestCase
{
    /**
     * Filter 1 - default values
     *
     * @var Zym_Filter_SentenceLength
     */
    protected $_filter1;
    
    /**
     * Filter 2 - length 32 and no whitespace deletion
     *
     * @var Zym_Filter_SentenceLength
     */
    protected $_filter2;
    
    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_filter1 = new Zym_Filter_SentenceLength();
        $this->_filter2 = new Zym_Filter_SentenceLength(32, false);
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_filter1 = null;
        $this->_filter2 = null;    
    }

    /**
     * Tests a short string (that shouldn't be filtered')
     *
     */
    public function testShortString()
    {
        $string = 'This is a short string';
        $this->assertSame($string, $this->_filter1->filter($string));
    }
    
    /**
     * Test a too long string
     *
     */
    public function testTooLongString()
    {
        $string = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. '
                . 'Nunc nunc est, eleifend eu, dapibus eget, pretium a, urna. '
                . 'Cras ullamcorper venenatis mauris. Donec eu nisi.';
                
        $expect = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. '
                . 'Nunc nunc est, eleifend eu, dapibus eget, pretium a, urna. '
                . 'Cras';
        
        $this->assertSame($expect, $this->_filter1->filter($string));
    }

    /**
     * Tests removing repeated whitespace using default filter values
     *
     */
    public function testSameLengthAsSpecifiedLength()
    {
        $string = 'This string shouldn\'t be shorter';
        $this->assertSame($string, $this->_filter2->filter($string));
    }

    /**
     * Tests a too long single word
     *
     */
    public function testTooLongSingleWord()
    {
        $string = 'Thisstringshouldbeexactlythirtytwocharacters';
        $this->assertSame(substr($string, 0, 32), $this->_filter2->filter($string));
    }

    /**
     * Tests removing repeated whitespace using default filter values
     *
     */
    public function testRepeatedWhitespaceReplacementDefault()
    {
        $string = ' Remove all  repeated   whitespace';
        $expected = 'Remove all repeated whitespace';
        $this->assertSame($expected, $this->_filter1->filter($string));
    }

    /**
     * Tests removing repeated whitespace with $replaceWhitespace set to false
     *
     */
    public function testRepeatedWhitespaceReplacementNoReplace()
    {
        $string = ' Remove all  repeated   ws';
        $this->assertSame($string, $this->_filter2->filter($string));
    }

    /**
     * Tests using null as input
     *
     */
    public function testNull()
    {
        $string = null;
        $this->assertSame('', $this->_filter1->filter($string));
    }

    /**
     * Tests using integer input
     *
     */
    public function testInteger()
    {
        $int = 1337;
        $this->assertSame('1337', $this->_filter1->filter($int));
    }

    /**
     * Tests using an object as input
     *
     */    
    public function testObject()
    {
        $obj = new Zym_Filter_SentenceLength();

        try {
            $this->_filter1->filter($obj);
        } catch (Exception $e) {
            return;
        }

        $this->fail('An exception has not been raised');
    }
}