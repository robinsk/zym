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
 * @see PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * @see Zym_NotificationTest
 */
require_once 'trunk/tests/Zym/Notification/Zym_NotificationTest.php';

/**
 * @see Zym_Notification_MessageTest
 */
require_once 'trunk/tests/Zym/Notification/Zym_Notification_MessageTest.php';

/**
 * @see Zym_Notification_RegistrationTest
 */
require_once 'trunk/tests/Zym/Notification/Zym_Notification_RegistrationTest.php';

/**
 * Test suite for Zym_Notification
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Tests
 * @package    Zym_Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class NotificationSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Constructs the test suite handler.
     */
    public function __construct ()
    {
        $this->setName('NotificationSuite');
        $this->addTestSuite('Zym_NotificationTest');
        $this->addTestSuite('Zym_Notification_MessageTest');
        $this->addTestSuite('Zym_Notification_RegistrationTest');
    }
    /**
     * Creates the suite.
     */
    public static function suite ()
    {
        return new self();
    }
}