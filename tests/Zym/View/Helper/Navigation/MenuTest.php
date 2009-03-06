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
 * Tests Zym_View_Helper_Menu
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
     * @var Zym_View_Helper_Menu
     */
    protected $_helper;

    /**
     * It should be possible to null out the nav structure in the helper
     *
     */
    public function testShouldBeAbleToNullOutNavigation()
    {
        $old = $this->_helper->getContainer();
        $oldCount = count($old);

        $this->assertGreaterThan(0, $oldCount, 'Empty container before test');

        $this->_helper->setContainer();
        $newCount = count($this->_helper->getContainer());
        $this->assertEquals(0, $newCount);

        $this->_helper->setContainer($old);
    }

    /**
     * It should be possible to autoload the nav structure from Zend_Registry
     *
     */
    public function testShouldBeAbleToAutoloadNavFromRegistry()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setContainer(null);

        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setContainer($oldContainer);
        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);
    }

    /**
     * It should be possible to set indenting in the helper
     *
     */
    public function testShouldBeAbleToBothSetIndentAndOverrideInRenderMenu()
    {
        $old = $this->_helper->getIndent();
        $this->_helper->setIndent(8);

        $expected1 = file_get_contents($this->_files . '/menu_indent4.html');
        $expected2 = file_get_contents($this->_files . '/menu_indent8.html');
        $actual1 = rtrim($this->_helper->renderMenu(null, 4), PHP_EOL);
        $actual2 = rtrim($this->_helper->renderMenu(), PHP_EOL);

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);

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
        $this->assertEquals($expected, $this->_helper->render());

        $expected2 = file_get_contents($this->_files . '/menu2.html');
        $this->assertEquals($expected2, $this->_helper->render($this->_nav2));

        $this->assertEquals($expected, $this->_helper->render());
    }

    /**
     * It should be possible to filter out pages based on ACL roles as a string
     *
     */
    public function testShouldBeAbleToUseAclRolesAsString()
    {
        $oldAcl = $this->_helper->getAcl();
        $oldRole = $this->_helper->getRole();

        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole('member');

        $expected = file_get_contents($this->_files . '/menu_acl_string.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setAcl($oldAcl);
        $this->_helper->setRole($oldRole);
    }

    /**
     * It should be possible to filter out pages based on ACL roles as an array
     *
     */
    public function testShouldBeAbleToUseAcl()
    {
        $oldAcl = $this->_helper->getAcl();
        $oldRole = $this->_helper->getRole();

        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/menu_acl.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setAcl($oldAcl);
        $this->_helper->setRole($oldRole);
    }

    /**
     * It should be possible to filter out pages based on ACL roles
     *
     */
    public function testShouldBeAbleToUseAnActualAclRoleFromAclObject()
    {
        $oldAcl = $this->_helper->getAcl();
        $oldRole = $this->_helper->getRole();

        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['acl']->getRole('member'));

        $expected = file_get_contents($this->_files . '/menu_acl_role_interface.html');
        $this->assertEquals($expected, $this->_helper->render());

        $this->_helper->setAcl($oldAcl);
        $this->_helper->setRole($oldRole);
    }

    /**
     * It should be possible to filter out pages based on ACL roles that are
     * constructed directly when setting in the helper, without fetching it
     * from the existing ACL
     *
     */
    public function testShouldBeAbleToUseConstructedAclRolesNotFromAclObject()
    {
        $oldAcl = $this->_helper->getAcl();
        $oldRole = $this->_helper->getRole();

        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole(new Zend_Acl_Role('member'));

        $expected = file_get_contents($this->_files . '/menu_acl_role_interface.html');
        $this->assertEquals($expected, $this->_helper->render());

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
        $this->assertEquals($expected, $this->_helper->render($this->_nav2));

        $this->_helper->setUlClass($old);
    }
    
    /**
     * It should be possible to explicitly set a Zend_Translate translator to use
     *
     */
    public function testShouldBeAbleToSetTranslatorAndUseIt()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);

        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
        
        $this->_helper->setTranslator(null);
    }
    
    /**
     * It should be possible to explicitly set a Zend_Translate_Adapter to use
     *
     */
    public function testShouldBeAbleToSetTranslateAdapterAndUseIt()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator->getAdapter());

        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
        
        $this->_helper->setTranslator(null);
    }
    
    /**
     * The helper should be able to retrieve a translator from Zend_Registry
     *
     */
    public function testShouldBeAbleToGetTranslatorFromRegistryAndUseIt()
    {
        $oldReg = Zend_Registry::isRegistered('Zend_Translate')
                ? Zend_Registry::get('Zend_Translate')
                : null;
        
        $translator = $this->_getTranslator();  
        Zend_Registry::set('Zend_Translate', $translator);
        
        $expected = file_get_contents($this->_files . '/menu_translated.html');
        $this->assertEquals($expected, $this->_helper->render());
        
        $this->_helper->setTranslator(null);
        Zend_Registry::set('Zend_Translate', $oldReg);
    }
    
    /**
     * It should be possible to disable translation even if the helper has a
     * translator
     *
     */
    public function testShouldBeAbleToDisableTranslation()
    {
        $translator = $this->_getTranslator();
        $this->_helper->setTranslator($translator);
        $this->_helper->setUseTranslator(false);

        $expected = file_get_contents($this->_files . '/menu.html');
        $this->assertEquals($expected, $this->_helper->render());
        
        $this->_helper->setTranslator(null);
    }
}