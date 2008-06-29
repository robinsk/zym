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
 * @see Zym_Filter_UrlString
 */
require_once 'Zym/Filter/UrlString.php';

/**
 * Test case for the UrlString filter.
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Filter_UrlStringTest extends PHPUnit_Framework_TestCase
{
    /**
     * Filter 1 - default values
     *
     * @var Zym_Filter_UrlString
     */
    protected $_filter1;
    
    /**
     * Filter 2 - encode slashes and use _ as word separator
     *
     * @var Zym_Filter_UrlString
     */
    protected $_filter2;
    
    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_filter1 = new Zym_Filter_UrlString();
        $this->_filter2 = new Zym_Filter_UrlString(null, true, null, '_');
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
        $this->assertSame('This-is-a-short-string', $this->_filter1->filter($string));
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
                
        $expect = 'Lorem-ipsum-dolor-sit-amet,-consectetuer-adipiscing-elit.-'
                . 'Nunc-nunc-est,-eleifend-eu,-dapibus-eget,-pretium-a,-urna.-'
                . 'Cras';
        
        $this->assertSame(urlencode($expect), $this->_filter1->filter($string));
    }
    
    /**
     * Tests the $encodeSlashes flag
     *
     */
    public function testSlashEncoding()
    {
        $string = 'should/this/be/encoded';
        
        $this->assertSame($string, $this->_filter1->filter($string));
        $this->assertSame(urlencode($string), $this->_filter2->filter($string));
    }
    
    /**
     * Tests custom word separator
     *
     */
    public function testWordSeparator()
    {
        $string1 = 'this should contain underscores';
        $expect1 = str_replace(' ', '_', $string1);
        
        $string2 = 'this should contain Leo Tolstoys first name';
        $expect2 = urlencode(str_replace(' ', 'Лев', $string2));
        
        $this->assertSame($expect1, $this->_filter2->filter($string1));
        $this->_filter2->setWordSeparator('Лев');
        $this->assertSame($expect2, $this->_filter2->filter($string2));
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