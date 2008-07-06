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
 * @see Zym_View_Helper_Object
 */
require_once 'Zym/View/Helper/Object.php';

/**
 * Zym_View_Helper_Object test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_ObjectTest extends PHPUnit_Framework_TestCase
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
        $this->_helper = new Zym_View_Helper_Object();
        $this->_helper->setView($view);
    }

    public function testObject()
    {
        $object = $this->_helper->object('http://test.com', 'text/html');
        $this->assertRegExp('/object/si', $object);
        $this->assertRegExp('/data="http:\/\/test.com"/si', $object);
        $this->assertRegExp('/type="text\/html"/si', $object);
    }

    public function testObjectWithAttribs()
    {
        $object = $this->_helper->object('http://test.com', 'text/html', array('test' => 'value'));
        $this->assertRegExp('/test="value"/si', $object);
    }

    public function testObjectWithParams()
    {
        $object = $this->_helper->object('http://test.com', 'text/html', array(), array('test' => 'value'));
        $this->assertRegExp('/name="test"/si', $object);
        $this->assertRegExp('/value="value"/si', $object);

    }

    public function testObjectWithContent()
    {
        $object = $this->_helper->object('http://test.com', 'text/html', array(), array(), 'Oops');
        $this->assertRegExp('/Oops/si', $object);
    }
}