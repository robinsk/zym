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
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Config/Xml.php';
require_once 'Zend/Registry.php';
require_once 'Zend/View.php';
require_once 'Zym/Navigation.php';

/**
 * Base class for navigation view helper tests
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_View_Helper_NavigationTestAbstract extends PHPUnit_Framework_TestCase
{
    const REGISTRY_KEY = 'Zym_Navigation';

    /**
     * Path to files needed for test
     *
     * @var string
     */
    protected $_files;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName;

    /**
     * View helper
     *
     * @var Zym_View_Helper_NavigationAbstract
     */
    protected $_helper;

    /**
     * Navigation structure
     *
     * @var Zym_Navigation
     */
    protected $_nav1;

    /**
     * Navigation structure
     *
     * @var Zym_Navigation
     */
    protected $_nav2;

    /**
     * Prepares the environment before running a test
     *
     */
    protected function setUp()
    {
        // read navigation config
        $this->_files = dirname(__FILE__) . '/_files/navigation';
        $config = new Zend_Config_Xml($this->_files . '/navigation.xml');

        // create nav structures
        $this->_nav1 = new Zym_Navigation($config->get('nav_test1'));
        $this->_nav2 = new Zym_Navigation($config->get('nav_test2'));

        // create view
        $view = new Zend_View();
        $view->addHelperPath('Zym/View/Helper', 'Zym_View_Helper');

        // create helper
        $this->_helper = new $this->_helperName();
        $this->_helper->setView($view);

        // set nav1 in helper as default
        $this->_helper->setNavigation($this->_nav1);
    }

    /**
     * Cleans up the environment after running a test
     *
     */
    protected function tearDown()
    {

    }

    /**
     * Sets up ACL
     *
     */
    protected function _getAcl()
    {
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('member'), 'guest');
        $acl->addRole(new Zend_Acl_Role('admin'), 'member');
        $acl->addRole(new Zend_Acl_Role('special'));

        return array('acl' => $acl, 'role' => array('member', 'special'));
    }
}