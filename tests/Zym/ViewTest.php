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
 * @package   Zym_View
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license   http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_View
 */
require_once 'Zym/View.php';

/**
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym_Tests
 * @package   Zym_View
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * View
     *
     * @var Zym_View
     */
    private $_view;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->_view = new Zym_View();
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->_view);
    }

    public function testConstructOptions()
    {
        $config = array(
            'streamFlag'     => false,
            'streamProtocol' => 'test',
            'streamWrapper'  => 'test',
            'streamFilter'   => 'filter'
        );

        $view = new Zym_View($config);

        $this->assertFalse($view->getStreamFlag());
        $this->assertEquals('test', $view->getStreamProtocol());
        $this->assertEquals('test', $view->getStreamWrapper());
        $this->assertEquals(array('filter'), $view->getStreamFilters());
    }

    public function testConstructOptionsFromZend()
    {
        // Ensure default is false
        $defaultStrictView = new Zend_View();
        $this->assertAttributeEquals(false, '_strictVars', $defaultStrictView);

        $view = new Zym_View(array('strictVars' => true));
        $this->assertAttributeEquals(true, '_strictVars', $view);
    }

    public function testGetStreamFilters()
    {
        $view = new Zym_View();
        $view->addStreamFilter('test');

        $this->assertContains('test', $view->getStreamFilters());
    }

    public function testAddStreamFilterWithString()
    {
        $view = new Zym_View();
        $view->addStreamFilter('test');

        $this->assertContains('test', $view->getStreamFilters());
    }

    public function testAddStreamFilterWithArray()
    {
        $view = new Zym_View();
        $view->addStreamFilter(array('test', 'test2'));

        $this->assertContains('test', $view->getStreamFilters());
        $this->assertContains('test2', $view->getStreamFilters());
    }

    public function testAddStreamFilterIsFluent()
    {
        $view = new Zym_View();
        $this->assertTrue($view->addStreamFilter('test') instanceof Zym_View);
    }

    public function testSetStreamFilterClearsFilters()
    {
        $view = new Zym_View();
        $view->addStreamFilter('test');
        $view->setStreamFilter(array());
        $this->assertNotContains('test', $view->getStreamFilters());

        $view->addStreamFilter('test');
        $view->setStreamFilter(null);
        $this->assertNotContains('test', $view->getStreamFilters());
    }

    public function testSetStreamFilterAddsFromString()
    {
        $view = new Zym_View();
        $view->setStreamFilter('test');

        $this->assertContains('test', $view->getStreamFilters());
    }

    public function testSetStreamFilterAddsFromArray()
    {
        $view = new Zym_View();
        $view->setStreamFilter(array('test', 'test2'));

        $this->assertContains('test', $view->getStreamFilters());
        $this->assertContains('test2', $view->getStreamFilters());
    }

    public function testSetStreamFlagIsFluent()
    {
        $view = new Zym_View();
        $this->assertEquals($view, $view->setStreamFlag(true));
    }

    public function testSetStreamFlagChangesFlag()
    {
        $view = new Zym_View();
        $view->setStreamFlag(false);

        $this->assertFalse($view->getStreamFlag());
    }

    public function testSetStreamProtocolThrowsExceptionOnEmptyProtocol()
    {
        $view  = new Zym_View();
        $test1 = null;
        $test2 = null;
        $test3 = null;

        try {
            $view->setStreamProtocol('');
        } catch (Zym_View_Exception $e) {
            $test1 = $e;
        }

        try {
            $view->setStreamProtocol(false);
        } catch (Zym_View_Exception $e) {
            $test2 = $e;
        }

        try {
            $view->setStreamProtocol('0');
        } catch (Zym_View_Exception $e) {
            $test3 = $e;
        }

        if (!$test1 instanceof Zym_View_Exception) {
            $this->fail();
        }

        if (!$test2 instanceof Zym_View_Exception) {
            $this->fail();
        }

        if (!$test3 instanceof Zym_View_Exception) {
            $this->fail();
        }
    }

    public function testSetStreamProtocolIsFluent()
    {
        $view = new Zym_View();
        $this->assertEquals($view, $view->setStreamProtocol('test'));
    }

    public function testSetStreamProtocolIsSet()
    {
        $view = new Zym_View();
        $view->setStreamProtocol('test');

        $this->assertEquals('test', $view->getStreamProtocol());
    }

    public function testGetStreamProtocol()
    {
        $view = new Zym_View();
        $this->assertEquals('view', $view->getStreamProtocol());
    }

    public function testSetStreamWrapperIsFluent()
    {
        $view = new Zym_View();
        $this->assertEquals($view, $view->setStreamWrapper('test'));
    }

    public function testSetStreamWrapperWorks()
    {
        $view = new Zym_View();
        $view->setStreamWrapper('test');

        $this->assertEquals('test', $view->getStreamWrapper());
    }

    public function testGetStreamWrapperReturnsString()
    {
        $view = new Zym_View();
        $this->assertEquals('Zym_View_Stream_Wrapper', $view->getStreamWrapper());
    }

    public function testRenderDoesNothingWithoutStreamFlag()
    {
        $view = new Zym_View();
        $view->setStreamFlag(false)
             ->addStreamFilter('AspTags')
             ->addScriptPath(dirname(__FILE__) . '/View');

        $this->assertEquals('<% echo ""; %>', $view->render('_files/test.phtml'));
    }

    public function testRenderUsesStream()
    {
        $view = new Zym_View();
        $view->addStreamFilter('AspTags')
             ->addScriptPath(dirname(__FILE__) . '/View');

        $this->assertEquals('', $view->render('_files/test.phtml'));
    }

    public function testPluginLoaderAddsZymFilters()
    {
        $view = new Zym_View();

        // Issue 43
        if (!method_exists($view, 'getPluginLoader')) {
            $this->markTestSkipped();
        }

        $pluginLoader = $view->getPluginLoader('filter');

        $this->assertContains('Zend/View/Filter/', $pluginLoader->getPaths());
        $this->assertContains('Zym/View/Filter/', $pluginLoader->getPaths());
    }

    public function testPluginLoaderAddsZymHelpers()
    {
        $view = new Zym_View();

        // Issue 43
        if (!method_exists($view, 'getPluginLoader')) {
            $this->markTestSkipped();
        }

        $pluginLoader = $view->getPluginLoader('Helper');

        $this->assertContains('Zend/View/Helper/', $pluginLoader->getPaths());
        $this->assertContains('Zym/View/Helper/', $pluginLoader->getPaths());
    }

    public function testLoadStreamWrapper()
    {
        $this->markTestIncomplete();
    }

    public function testRun()
    {
        $this->markTestIncomplete();
    }
}