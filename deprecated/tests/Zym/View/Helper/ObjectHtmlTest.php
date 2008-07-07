<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
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
 * @see Zym_View_Helper_ObjectHtml
 */
require_once 'Zym/View/Helper/ObjectHtml.php';

/**
 * Zym_View_Helper_ObjectHtml test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_ObjectHtmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_View_Helper_Object
     */
    protected $_helper;

    protected function setUp()
    {
        $view = new Zym_View();
        $this->_helper = new Zym_View_Helper_ObjectHtml();
        $this->_helper->setView($view);
    }

    public function testObjectHtml()
    {
        $object = $this->_helper->objectHtml('http://test.com');

        $this->assertRegExp('/object/si', $object);
        $this->assertRegExp('/data="http:\/\/test.com"/si', $object);

        // Check type
        $this->assertRegExp('/type="text\/html"/si', $object);

        // Check classid
        $this->assertRegExp('/classid="/si', $object);

        // Check params
        $this->assertRegExp('/value="http:\/\/test.com"/si', $object);
        $this->assertRegExp('/name="src"/si', $object);
    }

    public function testObjectHtmlWithAttribs()
    {
        $object = $this->_helper->objectHtml('http://test.com', array('test' => 'value'));
        $this->assertRegExp('/test="value"/si', $object);
        $this->assertRegExp('/classid="/si', $object);
    }

    public function testObjectHtmlWithParams()
    {
        $object = $this->_helper->objectHtml('http://test.com', array(), array('test' => 'value'));
        $this->assertRegExp('/name="test"/si', $object);
        $this->assertRegExp('/value="value"/si', $object);

    }

    public function testObjectHtmlWithContent()
    {
        $object = $this->_helper->objectHtml('http://test.com', array(), array(), 'Oops');
        $this->assertRegExp('/Oops/si', $object);
    }
}