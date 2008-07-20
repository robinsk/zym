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
 * @see Zym_Validate_Sitemap_Priority
 */
require_once 'Zym/Validate/Sitemap/Priority.php';

/**
 * Tests Zym_Validate_Sitemap_Priority
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Validate
 * @subpackage Zym_Validate_Sitemap
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Validate_Sitemap_PriorityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zym_Validate_Sitemap_Priority
     */
    protected $_validator;
    
    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zym_Validate_Sitemap_Priority();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_validator = null;
    }
    
    /**
     * Tests valid priorities
     *
     */
    public function testValidPriorities()
    {
        $values = array(
            '0.0', '0.1', '0.2', '0.3', '0.4', '0.5',
            '0.6', '0.7', '0.8', '0.9', '1.0', '0.99',
            0.1, 0.6667, 0.0001, 0.4, 0, 1, .35
        );
        
        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }
    
    /**
     * Tests invalid priorities
     *
     */
    public function testInvalidPriorities()
    {
        $values = array(
            'alwayz',  '_hourly', 'Daily', 'wEekly',
            'mönthly ', ' yearly ', 'never ', 'rofl',
            '0,0', '1.1', '02', '3', '01.4', '0.f', 
            1.1, -0.001, 1.0001
        );
        
        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
}