<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym_Tests
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
 * @see Zym_View_Helper_GetSession
 */
require_once 'Zym/View/Helper/GetSession.php';

/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * Zym_View_Helper_GetSession test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym_Tests
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_GetSessionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot test: cannot start session because headers already sent');
        }

        Zend_Session::start();
    }

    public function testGetSession()
    {
        $helper = new Zym_View_Helper_GetSession();
        $this->assertEquals('Zend_Session_Namespace', get_class($helper->getSession()));
    }
}