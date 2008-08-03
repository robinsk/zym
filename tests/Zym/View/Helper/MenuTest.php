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
 */
require_once dirname(__FILE__) . '/NavigationTestAbstract.php';
require_once 'Zym/View/Helper/Menu.php';

/**
 * Tests Zym_View_Helper_Menu
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_MenuTest
    extends Zym_View_Helper_NavigationTestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zym_View_Helper_Menu';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Menu
     */
    protected $_helper;

    /**
     * It should be possible to null out the nav structure in the helper
     *
     */
    public function testShouldBeAbleToNullOutNavigation()
    {
        $old = $this->_helper->getNavigation();
        $oldCount = count($old);

        $this->assertGreaterThan(0, $oldCount, 'Empty container before test');

        $this->_helper->setNavigation();
        $newCount = count($this->_helper->getNavigation());
        $this->assertEquals(0, $newCount);

        $this->_helper->setNavigation($old);
    }

    /**
     * It should be possible to autoload the nav structure from Zend_Registry
     *
     */
    public function testShouldBeAbleToAutoloadNavFromRegistry()
    {
        $old = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $old = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->toString());

        Zend_Registry::set(self::REGISTRY_KEY, $old);
    }

    /**
     * It should be possible to set indenting in the helper
     *
     */
    public function testShouldBeAbleToBothSetIndentAndOverrideItInToString()
    {
        $old = $this->_helper->getIndent();
        $this->_helper->setIndent(8);

        $expected1 = file_get_contents($this->_files . '/menu_indent4.html');
        $expected2 = file_get_contents($this->_files . '/menu_indent8.html');

        $this->assertEquals($expected1, $this->_helper->toString(4));
        $this->assertEquals($expected2, $this->_helper->toString());

        $this->_helper->setIndent($old);
    }

    /**
     * It should be possible to render another nav structure without
     * interfering with the one registered in the helper
     *
     */
    public function testShouldBePossibleToRenderAnotherNavWithoutInterfering()
    {
        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->toString());

        $expected2 = file_get_contents($this->_files . '/menu2.html');
        $this->assertEquals($expected2, $this->_helper->renderMenu($this->_nav2));

        $this->assertEquals($expected, $this->_helper->toString());
    }

    /**
     * It should be possible to filter out pages based on ACL roles
     *
     */
    public function testShouldBeAbleToUseAclRoles()
    {
        $oldAcl = $this->_helper->getAcl();
        $oldRole = $this->_helper->getRole();

        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/menu_acl.html');
        $this->assertEquals($expected, $this->_helper->toString());

        $this->_helper->setAcl($oldAcl);
        $this->_helper->setRole($oldRole);
    }

    /**
     * It should be possible to set CSS class for the UL element
     *
     */
    public function testShouldBePossibleToSetUlCssClass()
    {
        $old = $this->_helper->getUlClass();
        $this->_helper->setUlClass('My_Nav');

        $expected = file_get_contents($this->_files . '/menu_css.html');
        $this->assertEquals($expected, $this->_helper->renderMenu($this->_nav2));

        $this->_helper->setUlClass($old);
    }
}