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
 * @package    Zym_Validate
 * @subpackage Zym_Validate_Sitemap
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Validate_Sitemap_Lastmod
 */
require_once 'Zym/Validate/Sitemap/Lastmod.php';

/**
 * Tests Zym_Validate_Sitemap_Lastmod
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Validate
 * @subpackage Zym_Validate_Sitemap
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Validate_Sitemap_LastmodTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zym_Validate_Sitemap_Lastmod
     */
    protected $_validator;
    
    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zym_Validate_Sitemap_Lastmod();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_validator = null;
    }
    
    /**
     * Tests valid change frequencies
     *
     */
    public function testValidChangefreqs()
    {
        $values = array(
            '1994-05-11T18:00:09-08:45',
            '1997-05-11T18:50:09+00:00',
            '1998-06-11T01:00:09-02:00',
            '1999-11-11T22:23:52+02:00',
            '2000-06-11',
            '2001-04-14',
            '2003-01-13',
            '2005-01-01',
            '2006-03-19',
            '2007-08-31',
            '2007-08-25'
        );
        
        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }
    
    /**
     * Tests strings that should be invalid
     *
     */
    public function testInvalidStrings()
    {
        $values = array(
            '1995-05-11T18:60:09-08:45',
            '1996-05-11T18:50:09+25:00',
            '2002-13-11',
            '2004-00-01'
        );
        
        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
    
    /**
     * Tests values that are not strings
     *
     */
    public function testNotString()
    {
        $values = array(
            1, 1.4, null, new stdClass(), true, false
        );
        
        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
}