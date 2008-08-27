<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Navigation_Page_Uri
 */
require_once 'Zym/Navigation/Page/Uri.php';

/**
 * Tests the class Zym_Navigation_Page_Uri
 * 
 * @author    Robin Skoglund
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Navigation_Page_UriTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     * 
     */
    protected function setUp()
    {
        
    }
    
    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {
        
    }
    
    /**
     * Tests that the constructor requires label
     *
     */
    public function testConstructionRequiresLabel()
    {
        try {
            $page = new Zym_Navigation_Page_Uri(array(
                'labelz' => 'label',
                'uri' => '#'
            ));
            $this->fail('Should throw exception for missing label');
        } catch (Zym_Navigation_Exception $e) {
            
        }
    }
    
    /**
     * Tests setUri() and getUri() with valid and invalid values
     *
     */
    public function testSetAndGetUri()
    {
        $page = new Zym_Navigation_Page_Uri(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals('#', $page->getUri());
        $page->setUri('bar');
        $this->assertEquals('bar', $page->getUri());
        
        $invalids = array(42, (object) null, -1);
        foreach ($invalids as $invalid) {
            try {
                $page->setUri($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
}