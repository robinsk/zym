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
 * @see Zym_View_Helper_Navigation_TestAbstract
 * @see Zym_View_Helper_Sitemap
 */
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zym/View/Helper/Navigation/Sitemap.php';

/**
 * Tests Zym_View_Helper_Navigation_Sitemap
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_SitemapTest
    extends Zym_View_Helper_Navigation_TestAbstract
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
    protected $_helperName = 'Zym_View_Helper_Navigation_Sitemap';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Navigation_Sitemap
     */
    protected $_helper;

    protected function setUp()
    {
        date_default_timezone_set('Europe/Berlin');

        if (isset($_SERVER['SERVER_NAME'])) {
            $this->_oldServer['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
        }

        if (isset($_SERVER['SERVER_PORT'])) {
            $this->_oldServer['SERVER_PORT'] = $_SERVER['SERVER_PORT'];
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->_oldServer['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        }

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

    protected function tearDown()
    {
        if (null !== $this->_oldRequest) {
            $this->_front->setRequest($this->_oldRequest);
        } else {
            $this->_front->setRequest(new Zend_Controller_Request_Http());
        }
        $this->_front->setRouter($this->_oldRouter);

        foreach ($this->_oldServer as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }

    public function testNullingOutNavigation()
    {
        $this->_helper->setContainer();
        $this->assertEquals(0, count($this->_helper->getContainer()));
    }

    public function testAutoloadContainerFromRegistry()
    {
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);

        $this->_helper->setContainer(null);

        $expected = file_get_contents($this->_files . '/sitemap.xml');
        $actual = $this->_helper->render();

        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

        $this->assertEquals($expected, $expected);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $rendered1 = file_get_contents($this->_files . '/sitemap.xml');
        $rendered2 = file_get_contents($this->_files . '/sitemap2.xml');
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

    public function testUseAclRoles()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = file_get_contents($this->_files . '/sitemap_acl.xml');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testSettingMaxDepth()
    {
        $this->_helper->setMaxDepth(0);

        $expected = file_get_contents($this->_files . '/sitemap_depth1.xml');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testDropXmlDeclaration()
    {
        $old = $this->_helper->getUseXmlDeclaration();
        $this->_helper->setUseXmlDeclaration(false);

        $expected = file_get_contents($this->_files . '/sitemap2_nodecl.xml');
        $this->assertEquals($expected, $this->_helper->render($this->_nav2));

        $this->_helper->setUseXmlDeclaration($old);
    }

    public function testThrowExceptionOnInvalidLoc()
    {
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->_helper->render($nav);
        } catch (Zend_View_Exception $e) {
            $expected = sprintf(
                    'Encountered an invalid URL for Sitemap XML: "%s"',
                    'http://w.');
            $actual = $e->getMessage();
            $this->assertEquals($expected, $actual);
            return;
        }

        $this->fail('A Zend_View_Exception was not thrown on invalid <loc />');
    }

    public function testDisablingValidators()
    {
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));
        $this->_helper->setUseSitemapValidators(false);

        $expected2 = file_get_contents($this->_files . '/sitemap2_invalid.xml');
        $this->assertEquals($expected2, $this->_helper->render($nav));
    }

    public function testUseSchemaValidation()
    {
        $this->markTestSkipped('Skipped because it fetches XSD from web');
        return;
        $nav = clone $this->_nav2;
        $this->_helper->setUseSitemapValidators(false);
        $this->_helper->setUseSchemaValidation(true);
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->_helper->render($nav);
        } catch (Zend_View_Exception $e) {
            $expected = sprintf(
                    'Sitemap is invalid according to XML Schema at "%s"',
                    Zym_View_Helper_Navigation_Sitemap::SITEMAP_XSD);
            $actual = $e->getMessage();
            $this->assertEquals($expected, $actual);
            return;
        }

        $this->fail('A Zend_View_Exception was not thrown when using Schema validation');
    }
}