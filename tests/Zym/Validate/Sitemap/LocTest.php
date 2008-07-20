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
 * @see Zym_Validate_Sitemap_Loc
 */
require_once 'Zym/Validate/Sitemap/Loc.php';

/**
 * Tests Zym_Validate_Sitemap_Loc
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_Validate
 * @subpackage Zym_Validate_Sitemap
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Validate_Sitemap_LocTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zym_Validate_Sitemap_Loc
     */
    protected $_validator;
    
    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zym_Validate_Sitemap_Loc();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_validator = null;
    }
    
    /**
     * Tests valid locations
     *
     */
    public function testValidLocs()
    {
        $values = array(
            'http://www.example.com',
            'http://www.example.com/',
            'http://www.exmaple.lan/',
            'https://www.exmaple.com/?foo=bar',
            'http://www.exmaple.com:8080/foo/bar/',
            'https://user:pass@www.exmaple.com:8080/',
            'https://www.exmaple.com/?foo=&quot;bar&apos;&amp;bar=&lt;bat&gt;'
        );
        
        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }
    
    /**
     * Tests invalid locations
     *
     */
    public function testInvalidLocs()
    {
        $values = array(
            'www.example.com',
            '/news/',
            '#',
            new stdClass(),
            42,
            'http:/example.com/',
            null,
            'https://www.exmaple.com/?foo="bar\'&bar=<bat>'
        );
        
        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
}