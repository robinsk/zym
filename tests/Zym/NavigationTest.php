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
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Navigation
 */
require_once 'Zym/Navigation.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Tests the class Zym_Navigation
 *
 * @author    Robin Skoglund
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_NavigationTest extends PHPUnit_Framework_TestCase
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

    public function testShouldConstructWithArray()
    {
        $pages = array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )
        );

        $nav = new Zym_Navigation($pages);

        $this->assertEquals(2, $nav->count());
    }

    public function testShouldFailForOneDimArray()
    {
        $page = array(
            'label' => 'Page 1',
            'uri' => '#'
        );

        try {
            $nav = new Zym_Navigation($page);
        } catch(Exception $e) {
            return;
        }

        $this->fail('An exception has not been thrown');
    }

    public function testShouldConstructWithConfig()
    {
        $pages = array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )
        );

        $pages = new Zend_Config($pages);

        $nav = new Zym_Navigation($pages);

        $this->assertEquals(2, $nav->count());
    }

    public function testShouldConstructWithNothing()
    {
        $nav = new Zym_Navigation();
        $this->assertEquals(0, $nav->count());
    }

    public function testShouldNotConstructWithInvalidArguments()
    {
        $caught = 0;

        try {
            $nav = new Zym_Navigation((object) null);
        } catch(Zym_Navigation_Exception $e1) {
            $caught++;
        }

        try {
            $nav = new Zym_Navigation(2);
        } catch(Zym_Navigation_Exception $e2) {
            $caught++;
        }

        try {
            $nav = new Zym_Navigation('foo');
        } catch(Zym_Navigation_Exception $e2) {
            $caught++;
        }

        if ($caught != 3) {
            $msg = 'Should throw 3 InvalidArgumentException exceptions. ';
            $msg .= $caught . ' were thrown.';
            $this->fail($msg);
        }
    }
}