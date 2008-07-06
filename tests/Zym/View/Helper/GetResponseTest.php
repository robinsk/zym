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
 * @see Zym_View_Helper_GetResponse
 */
require_once 'Zym/View/Helper/GetResponse.php';

/**
 * Zym_View_Helper_GetResponse test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_GetResponseTest extends PHPUnit_Framework_TestCase
{
    public function testGetResponse()
    {
        $helper = new Zym_View_Helper_GetResponse();
        $this->assertEquals(Zend_Controller_Front::getInstance()->getResponse(), $helper->getResponse());
    }
}