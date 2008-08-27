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
 * @see Zend_Controller_Request_Http
 * @see Zym_View_Helper_NavigationTestAbstract
 * @see Zym_View_Helper_Sitemap
 */
require_once 'Zend/Controller/Request/Http.php';
require_once dirname(__FILE__) . '/NavigationTestAbstract.php';
require_once 'Zym/View/Helper/Sitemap.php';
require_once 'Zend/Controller/Front.php';

/**
 * Tests Zym_View_Helper_Sitemap
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_SitemapTest
    extends Zym_View_Helper_NavigationTestAbstract
{
    protected $_front;
    protected $_oldRequest;
    protected $_oldRouter;
    protected $_oldServer = array();

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zym_View_Helper_Sitemap';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Sitemap
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test.
     *
     */
    protected function setUp()
    {
        $oldServer['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
        $oldServer['SERVER_PORT'] = $_SERVER['SERVER_PORT'];
        $oldServer['REQUEST_URI'] = $_SERVER['REQUEST_URI'];

        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/';

        $this->_front = Zend_Controller_Front::getInstance();
        $this->_oldRequest = $this->_front->getRequest();
        $this->_oldRouter = $this->_front->getRouter();

        $this->_front->resetInstance();
        $this->_front->setRequest(new Zend_Controller_Request_Http());
        $this->_front->getRouter()->addDefaultRoutes();

        parent::setUp();

        $this->_helper->setFormatOutput(true);
    }

    /**
     * Cleans up the environment after running a test
     *
     */
    protected function tearDown()
    {
        if (null !== $this->_oldRequest) {
            $this->_front->setRequest($this->_oldRequest);
        } else {
            $this->_front->setRequest(new Zend_Controller_Request_Http());
        }
        $this->_front->setRouter($this->_oldRouter);

        foreach ($this->_oldServer as $key => $val) {
            $_SERVER[$key] = $value;
        }
    }

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

        $expected = file_get_contents($this->_files . '/sitemap.xml');
        $this->assertEquals($expected, $this->_helper->toString());

        Zend_Registry::set(self::REGISTRY_KEY, $old);
    }

    /**
     * It should be possible to render another nav structure without
     * interfering with the one registered in the helper
     *
     */
    public function testShouldBePossibleToRenderAnotherNavWithoutInterfering()
    {
        $expected = file_get_contents($this->_files . '/sitemap.xml');

        $expected2 = file_get_contents($this->_files . '/sitemap2.xml');
        $this->assertEquals($expected2, $this->_helper->renderSitemap($this->_nav2));

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

        $expected = file_get_contents($this->_files . '/sitemap_acl.xml');
        $this->assertEquals($expected, $this->_helper->toString());

        $this->_helper->setAcl($oldAcl);
        $this->_helper->setRole($oldRole);
    }

    /**
     * It should be possible to specify max depth to render sitemap
     *
     */
    public function testShouldBeAbletoSetMaxDepth()
    {
        $old = $this->_helper->getMaxDepth();
        $this->_helper->setMaxDepth(0);

        $expected = file_get_contents($this->_files . '/sitemap_depth1.xml');
        $this->assertEquals($expected, $this->_helper->toString());

        $this->_helper->setMaxDepth($old);
    }

    /**
     * It should be possible to not print the XML declaration
     *
     */
    public function testShouldBeAbleToDropXmlDeclaration()
    {
        $old = $this->_helper->getUseXmlDeclaration();
        $this->_helper->setUseXmlDeclaration(false);

        $expected = file_get_contents($this->_files . '/sitemap2_nodecl.xml');
        $this->assertEquals($expected, $this->_helper->renderSitemap($this->_nav2));

        $this->_helper->setUseXmlDeclaration($old);
    }

    /**
     * An exception should be thrown when the loc is invalid
     *
     */
    public function testShouldThrowExceptionOnInvalidLoc()
    {
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->_helper->renderSitemap($nav);
        } catch (DomainException $e) {
            return;
        }

        $this->fail('A DomainException was not thrown on invalid <loc />');
    }

    /**
     * An exception should not be thrown when the loc is invalid and
     * sitemap validators are disabled
     *
     */
    public function testShouldBeAbleToDisableValidators()
    {
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));
        $this->_helper->setUseSitemapValidators(false);

        $expected2 = file_get_contents($this->_files . '/sitemap2_invalid.xml');
        $this->assertEquals($expected2, $this->_helper->renderSitemap($nav));

        $this->_helper->setUseSitemapValidators(true);
    }

    /**
     * It should be possible to perform schema validation on the
     * generated sitemap
     *
     */
    //public function testShouldBeAbleToUseSchemaValidation()
    public function jokeShouldBeAbleToUseSchemaValidation()
    {
        $nav = clone $this->_nav2;
        $this->_helper->setUseSitemapValidators(false);
        $this->_helper->setUseSchemaValidation(true);
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->_helper->renderSitemap($nav);
        } catch (DomainException $e) {
            return;
        }

        $this->fail('A DomainException was not thrown when using Schema validation');
    }
}