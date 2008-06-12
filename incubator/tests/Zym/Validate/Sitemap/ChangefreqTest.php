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
 * @see Zym_Validate_Sitemap_Changefreq
 */
require_once 'Zym/Validate/Sitemap/Changefreq.php';

/**
 * Tests Zym_Validate_Sitemap_Changefreq
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Validate
 * @subpackage Zym_Validate_Sitemap
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Validate_Sitemap_ChangefreqTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zym_Validate_Sitemap_Changefreq
     */
    protected $_validator;
    
    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zym_Validate_Sitemap_Changefreq();
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
            'always',  'hourly', 'daily', 'weekly',
            'monthly', 'yearly', 'never'
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
            'alwayz',  '_hourly', 'Daily', 'wEekly',
            'mönthly ', ' yearly ', 'never ', 'rofl',
            'yesterday', 
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