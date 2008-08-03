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
 * @license    http://www.zym-project.com//License New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_View_Helper_FlashMessenger
 */
require_once 'Zym/View/Helper/FlashMessenger.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * Zym_View_Helper_FlashMessenger test case.
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com//License New BSD License
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_FlashMessengerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_View_Helper_FlashMessenger
     */
    protected $_helper;

    /**
     * Setup
     *
     */
    protected function setUp()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot test: cannot start session because headers already sent');
        }

        Zend_Session::start();

        $this->_helper = new Zym_View_Helper_FlashMessenger();
    }

    public function testReturnsFlashMessenger()
    {
        $object = $this->_helper->flashMessenger();
        $assert = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $this->assertEquals($assert, $object);
    }

    public function testReturnsFlashMessengerCustomNamespace()
    {
        $object = $this->_helper->flashMessenger('Test');
        $assert = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $assert->setNamespace('Test');

        $this->assertEquals($assert, $object);
    }
}