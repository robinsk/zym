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
 * @see Zym_Navigation_Page
 */
require_once 'Zym/Navigation/Page.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Tests the class Zym_Navigation_Page
 * 
 * @author    Robin Skoglund
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Navigation_PageTest extends PHPUnit_Framework_TestCase
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
        // setConfig, setOptions
    }
    
    /**
     * Tests setLabel() and getLabel() with valid and invalid values
     *
     */
    public function testSetAndGetLabel()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals('foo', $page->getLabel());
        $page->setLabel('bar');
        $this->assertEquals('bar', $page->getLabel());
        
        $invalids = array(42, '', (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setLabel($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
    
    /**
     * Tests setId() and getId() with valid and invalid values
     *
     */
    public function testSetAndGetId()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals(null, $page->getId());
        $page->setId('bar');
        $this->assertEquals('bar', $page->getId());
        
        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setId($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
    
    /**
     * Tests setClass() and getClass() with valid and invalid values
     *
     */
    public function testSetAndGetClass()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals(null, $page->getClass());
        $page->setClass('bar');
        $this->assertEquals('bar', $page->getClass());
        
        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setClass($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
    
    /**
     * Tests setTitle() and getTitle() with valid and invalid values
     *
     */
    public function testSetAndGetTitle()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals(null, $page->getTitle());
        $page->setTitle('bar');
        $this->assertEquals('bar', $page->getTitle());
        
        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setTitle($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
    
    /**
     * Tests setTarget() and getTarget() with valid and invalid values
     *
     */
    public function testSetAndGetTarget()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals(null, $page->getTarget());
        $page->setTarget('bar');
        $this->assertEquals('bar', $page->getTarget());
        
        $invalids = array(42, true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setTarget($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
    
    /**
     * Tests setPosition() and getPosition() with valid and invalid values
     *
     */
    public function testSetAndGetPosition()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals(null, $page->getPosition());
        $page->setPosition('1');
        $this->assertEquals(1, $page->getPosition());
        $page->setPosition(1337);
        $this->assertEquals(1337, $page->getPosition());
        $page->setPosition('-25');
        $this->assertEquals(-25, $page->getPosition());
        
        $invalids = array(3.14, 'e', "\n", '0,4', true, (object) null);
        foreach ($invalids as $invalid) {
            try {
                $page->setPosition($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'InvalidArgumentException was thrown';
                $this->fail($msg);
            } catch (InvalidArgumentException $e) {
                
            }
        }
    }
    
    /**
     * Tests setActive() and isActive() with valid and invalid values
     *
     */
    public function testSetAndGetActive()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setActive($valid);
            $this->assertEquals(true, $page->isActive());
        }
        
        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setActive($invalid);
            $this->assertEquals(false, $page->isActive());
        }
    }
    
    /**
     * Tests setVisible() and isVisible() with valid and invalid values
     *
     */
    public function testSetAndGetVisible()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setVisible($valid);
            $this->assertEquals(true, $page->isVisible());
        }
        
        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setVisible($invalid);
            $this->assertEquals(false, $page->isVisible());
        }
    }
    
    /**
     * Tests setting and getting of custom properties
     *
     */
    public function testCustomPropertiesShouldWork()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#',
            'foo' => 'bar'
        ));
        
        $this->assertEquals('bar', $page->foo);
        $this->assertEquals(false, isset($page->baz));
        $page->baz = 'bat';
        $this->assertEquals('bat', $page->baz);
        $this->assertEquals(true, isset($page->baz));
        $this->assertEquals(null, $page->leet);
    }
    
    /**
     * Tests the magic __toString() method
     *
     */
    public function testMagicToStringMethodShouldReturnLabel()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $this->assertEquals('foo', (string) $page);
    }
    
    /**
     * Tests that the setOptions() method translates options correctly
     * to their according accessor methods 
     *
     */
    public function testSetOptionsShouldTranslateToAccessor()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $options = array(
            'label' => 'bar',
            'action' => 'baz',
            'controller' => 'bat',
            'module' => 'test',
            'reset_params' => false,
            'id' => 'foo-test'
        );
        
        $page->setOptions($options);
        
        $this->assertEquals('bar', $page->getLabel());
        $this->assertEquals('baz', $page->getAction());
        $this->assertEquals('bat', $page->getController());
        $this->assertEquals('test', $page->getModule());
        $this->assertEquals(false, $page->getResetParams());
        $this->assertEquals('foo-test', $page->getId());
    }
    
    /**
     * Tests the setConfig() method 
     *
     */
    public function testSetConfig()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $options = array(
            'label' => 'bar',
            'action' => 'baz',
            'controller' => 'bat',
            'module' => 'test',
            'reset_params' => false,
            'id' => 'foo-test'
        );
        
        $page->setConfig(new Zend_Config($options));
        
        $this->assertEquals('bar', $page->getLabel());
        $this->assertEquals('baz', $page->getAction());
        $this->assertEquals('bat', $page->getController());
        $this->assertEquals('test', $page->getModule());
        $this->assertEquals(false, $page->getResetParams());
        $this->assertEquals('foo-test', $page->getId());
    }
    
    /**
     * Tests that the setOptions() method sets custom properties
     * if no accessor is found 
     *
     */
    public function testSetOptionsShouldSetCustomProperties()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#'
        ));
        
        $options = array(
            'test' => 'test',
            'meaning' => 42
        );
        
        $page->setOptions($options);
        
        $this->assertEquals('test', $page->test);
        $this->assertEquals(42, $page->meaning);
    }
    
    /**
     * Tests the getCustomProperties() method
     *
     */
    public function testGetCustomProperties()
    {
        $page = Zym_Navigation_Page::factory(array(
            'label' => 'foo',
            'uri' => '#',
            'baz' => 'bat'
        ));
        
        $options = array(
            'test' => 'test',
            'meaning' => 42
        );
        
        $page->setOptions($options);
        
        $expected = array(
            'baz' => 'bat',
            'test' => 'test',
            'meaning' => 42
        );
        
        $this->assertEquals($expected, $page->getCustomProperties());
    }
    
    /**
     * Tests the toArray() method
     *
     */
    public function testToArrayMethod()
    {
        $options = array(
            'label' => 'foo',
            'uri' => '#',
            'id' => 'my-id',
            'class' => 'my-class',
            'title' => 'my-title',
            'target' => 'my-target',
            'position' => 100,
            'active' => true,
            'visible' => false,
        
            'foo' => 'bar',
            'meaning' => 42
        );
        
        $page = Zym_Navigation_Page::factory($options);
        
        $this->assertEquals($options, $page->toArray());
    }
}