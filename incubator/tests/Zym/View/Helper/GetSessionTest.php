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
 * @see Zym_View_Helper_GetSession
 */
require_once 'Zym/View/Helper/GetSession.php';

/**
 * Zym_View_Helper_GetSession test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_GetSessionTest extends PHPUnit_Framework_TestCase
{
    public function testGetSession()
    {
        $this->setExpectedException('Zend_Session_Exception');
        $helper = new Zym_View_Helper_GetSession();
        $this->assertEquals('Zend_Session_Namespace', get_class($helper->getSession()));
    }
}