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
     * @var Zym_View_Helper_Navigation_Breadcrumbs
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

        $this->_helper->setContainer(null);

        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $actual = $this->_helper->render();

        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testSetSeparator()
    {
        $this->_helper->setSeparator('foo');

        $expected = file_get_contents($this->_files . '/breadcrumbs_sep.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testLinkLastElement()
    {
        $this->_helper->setLinkLast(true);

        $expected = file_get_contents($this->_files . '/breadcrumbs_linklast.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSetIndent()
    {
        $this->_helper->setIndent(8);

        $expected = '        <a';
        $actual = substr($this->_helper->render(), 0, strlen($expected));

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $this->_helper->setMinDepth(0);

        $rendered1 = file_get_contents($this->_files . '/breadcrumbs.html');
        $rendered2 = 'Site 2';

        $expected = array(
            'registered'       => $rendered1,
            'supplied'         => $rendered2,
            'registered_again' => $rendered1
        );

        $actual = array(
            'registered'       => $this->_helper->render(),
            'supplied'         => $this->_helper->render($this->_nav2),
            'registered_again' => $this->_helper->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUseAclResourceFromPages()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/breadcrumbs_acl.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingZendTranslate()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);

        $expected = file_get_contents($this->_files . '/breadcrumbs_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator->getAdapter());

        $expected = file_get_contents($this->_files . '/breadcrumbs_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationFromTranslatorInRegistry()
    {
        $oldReg = Zend_Registry::isRegistered('Zend_Translate')
                ? Zend_Registry::get('Zend_Translate')
                : null;

        $translator = $this->_getTranslator();
        Zend_Registry::set('Zend_Translate', $translator);

        $expected = file_get_contents($this->_files . '/breadcrumbs_translated.html');
        $actual = $this->_helper->render();

        Zend_Registry::set('Zend_Translate', $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);
        $this->_helper->setUseTranslator(false);

        $expected = file_get_contents($this->_files . '/breadcrumbs.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testRenderingPartial()
    {
        $this->_helper->setPartial('bc.phtml');

        $expected = file_get_contents($this->_files . '/breadcrumbs_partial.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialBySpecifyingAnArrayAsPartial()
    {
        $this->_helper->setPartial(array('bc.phtml', 'default'));

        $expected = file_get_contents($this->_files . '/breadcrumbs_partial.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialShouldFailOnInvalidPartialArray()
    {
        $this->_helper->setPartial(array('bc.phtml'));

        try {
            $this->_helper->render();
            $fail = true;
        } catch (Zend_View_Exception $e) {
            $fail = false;
        }

        if ($fail) {
            $this->fail('$partial was invalid, but no Zend_View_Exception was thrown');
        }
    }
}