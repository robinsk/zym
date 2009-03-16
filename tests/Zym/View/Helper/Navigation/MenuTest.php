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
 * @see Zym_View_Helper_Menu
 */
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zym/View/Helper/Navigation/Menu.php';

/**
 * Tests Zym_View_Helper_Navigation_Menu
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Navigation_MenuTest
    extends Zym_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zym_View_Helper_Navigation_Menu';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Navigation_Menu
     */
    protected $_helper;

    public function testNullingOutContainerInHelper()
    {
        $this->_helper->setContainer();
        $this->assertEquals(0, count($this->_helper->getContainer()));
    }

    public function testAutoloadingContainerFromRegistry()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $this->_helper->setContainer(null);

        $expected = file_get_contents($this->_files . '/menu.html');
        $actual = $this->_helper->render();

        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $actual);
    }

    public function testSetIndentAndOverrideInRenderMenu()
    {
        $this->_helper->setIndent(8);

        $expected = array(
            'indent4' => file_get_contents($this->_files . '/menu_indent4.html'),
            'indent8' => file_get_contents($this->_files . '/menu_indent8.html')
        );
        $actual = array(
            'indent4' => rtrim($this->_helper->renderMenu(null, 4), PHP_EOL),
            'indent8' => rtrim($this->_helper->renderMenu(), PHP_EOL)
        );

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $rendered1 = file_get_contents($this->_files . '/menu.html');
        $rendered2 = file_get_contents($this->_files . '/menu2.html');
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

    public function testUseAclRoleAsString()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole('member');

        $expected = file_get_contents($this->_files . '/menu_acl_string.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testFilterOutPagesBasedOnAcl()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/menu_acl.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAcl()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);
        $this->_helper->setUseAcl(false);

        $expected = file_get_contents($this->_files . '/menu.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testUseAnAclRoleInstanceFromAclObject()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['acl']->getRole('member'));

        $expected = file_get_contents($this->_files . '/menu_acl_role_interface.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testUseConstructedAclRolesNotFromAclObject()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole(new Zend_Acl_Role('member'));

        $expected = file_get_contents($this->_files . '/menu_acl_role_interface.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSetUlCssClass()
    {
        $this->_helper->setUlClass('My_Nav');
        $expected = file_get_contents($this->_files . '/menu_css.html');
        $this->assertEquals($expected, $this->_helper->render($this->_nav2));
    }

    public function testTranslationUsingZendTranslate()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);

        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator->getAdapter());

        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testTranslationUsingTranslatorFromRegistry()
    {
        $oldReg = Zend_Registry::isRegistered('Zend_Translate')
                ? Zend_Registry::get('Zend_Translate')
                : null;

        $translator = $this->_getTranslator();
        Zend_Registry::set('Zend_Translate', $translator);

        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $actual = $this->_helper->render();

        Zend_Registry::set('Zend_Translate', $oldReg);

        $this->assertEquals($expected, $actual);

    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);
        $this->_helper->setUseTranslator(false);

        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testRenderingPartial()
    {
        $this->_helper->setPartial('menu.phtml');

        $expected = file_get_contents($this->_files . '/menu_partial.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialBySpecifyingAnArrayAsPartial()
    {
        $this->_helper->setPartial(array('menu.phtml', 'default'));

        $expected = file_get_contents($this->_files . '/menu_partial.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialShouldFailOnInvalidPartialArray()
    {
        $this->_helper->setPartial(array('menu.phtml'));

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