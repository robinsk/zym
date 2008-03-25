<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Notification_Registration
 */
require_once 'trunk/library/Zym/Notification/Registration.php';

/**
 * Test for Zym_Notification_Registration
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Notification_RegistrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zym_Notification_Registration
     */
    private $Zym_Notification_Registration;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();

        $this->Zym_Notification_Registration = new Zym_Notification_Registration('foo', 'bar');
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Zym_Notification_Registration = null;
        parent::tearDown();
    }

    /**
     * Tests Zym_Notification_Registration->getCallback()
     */
    public function testGetCallback ()
    {
        $this->assertEquals('bar', $this->Zym_Notification_Registration->getCallback());
    }
    /**
     * Tests Zym_Notification_Registration->getObserver()
     */
    public function testGetObserver ()
    {
        $this->assertEquals('foo', $this->Zym_Notification_Registration->getObserver());
    }
}

