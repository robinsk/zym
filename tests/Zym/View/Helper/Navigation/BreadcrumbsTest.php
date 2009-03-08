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
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * Imports
 *
 * @see Zym_View_Helper_Navigation_TestAbstract
 * @see Zym_View_Helper_Breadcrumbs
 */
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zym/View/Helper/Navigation/Breadcrumbs.php';

/**
 * Tests Zym_View_Helper_Navigation_Breadcrumbs
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Navigation_BreadcrumbsTest
    extends Zym_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zym_View_Helper_Navigation_Breadcrumbs';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Breadcrumbs
     */
    protected $_helper;

    public function testNullOutContainer()
    {
        $old = $this->_helper->getContainer();
        $oldCount = count($old);

        $this->assertGreaterThan(0, $oldCount, 'Empty container before test');

        $this->_helper->setContainer();
        $newCount = count($this->_helper->getContainer());
        $this->assertEquals(0, $newCount);

        $this->_helper->setContainer($old);
    }

    public function testAutoloadContainerFromRegistry()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setContainer(null);

        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setContainer($oldContainer);
        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);
    }

    public function testSetSeparator()
    {
        $old = $this->_helper->getSeparator();
        $this->_helper->setSeparator('foo');

        $expected = file_get_contents($this->_files . '/breadcrumbs_sep.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setSeparator($old);
    }

    public function testLinkLastElement()
    {
        $old = $this->_helper->getLinkLast();
        $this->_helper->setLinkLast(true);

        $expected = file_get_contents($this->_files . '/breadcrumbs_linklast.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setLinkLast($old);
    }

    public function testSetIndent()
    {
        $old = $this->_helper->getIndent();
        $this->_helper->setIndent(8);

        $expected = '        <a';
        $actual = substr($this->_helper->render(), 0, strlen($expected));

        $this->assertEquals($expected, $actual);

        $this->_helper->setIndent($old);
    }

    public function testRenderAnotherContainerWithoutInterfering()
    {
        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $this->assertEquals($expected, $this->_helper->render());

        $oldMin = $this->_helper->getMinDepth();
        $this->_helper->setMinDepth(0);

        $this->assertEquals("Site 2", $this->_helper->render($this->_nav2));

        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setMinDepth($oldMin);
    }

    public function testUseAclResourceFromPages()
    {
        $oldAcl = $this->_helper->getAcl();
        $oldRole = $this->_helper->getRole();

        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/breadcrumbs_acl.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setAcl($oldAcl);
        $this->_helper->setRole($oldRole);
    }

    public function testTranslationUsingZendTranslate()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);

        $expected = file_get_contents($this->_files . '/breadcrumbs_translated.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setTranslator(null);
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator->getAdapter());

        $expected = file_get_contents($this->_files . '/breadcrumbs_translated.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setTranslator(null);
    }

    public function testTranslationFromTranslatorInRegistry()
    {
        $oldReg = Zend_Registry::isRegistered('Zend_Translate')
                ? Zend_Registry::get('Zend_Translate')
                : null;

        $translator = $this->_getTranslator();
        Zend_Registry::set('Zend_Translate', $translator);

        $expected = file_get_contents($this->_files . '/breadcrumbs_translated.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setTranslator(null);
        Zend_Registry::set('Zend_Translate', $oldReg);
    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);
        $this->_helper->setUseTranslator(false);

        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setTranslator(null);
    }
}