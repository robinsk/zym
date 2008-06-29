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
 * @license http://www.zym-project.com/license New BSD License
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
 * @see Zym_View_Helper_ObjectFlash
 */
require_once 'Zym/View/Helper/ObjectFlash.php';

/**
 * Zym_View_Helper_ObjectFlash test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_ObjectFlashTest extends PHPUnit_Framework_TestCase
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
        $this->_helper = new Zym_View_Helper_ObjectFlash();
        $this->_helper->setView($view);
    }

    public function testObjectFlash()
    {
        $object = $this->_helper->objectFlash('http://test.com');

        $this->assertRegExp('/object/si', $object);
        $this->assertRegExp('/data="http:\/\/test.com"/si', $object);

        // Check type
        $this->assertRegExp('/type="application\/x-shockwave-flash"/si', $object);

        // Check classid
        $this->assertRegExp('/classid="/si', $object);

        // Check codebase
        $this->assertRegExp('/codebase="/si', $object);

        // Check params
        $this->assertRegExp('/value="http:\/\/test.com"/si', $object);
        $this->assertRegExp('/name="movie"/si', $object);
    }

    public function testObjectFlashWithAttribs()
    {
        $object = $this->_helper->objectFlash('http://test.com', array('test' => 'value'));
        $this->assertRegExp('/test="value"/si', $object);
        $this->assertRegExp('/classid="/si', $object);
    }

    public function testObjectFlashWithParams()
    {
        $object = $this->_helper->objectFlash('http://test.com', array(), array('test' => 'value'));
        $this->assertRegExp('/name="test"/si', $object);
        $this->assertRegExp('/value="value"/si', $object);

    }

    public function testObjectFlashWithContent()
    {
        $object = $this->_helper->objectFlash('http://test.com', array(), array(), 'Oops');
        $this->assertRegExp('/Oops/si', $object);
    }
}