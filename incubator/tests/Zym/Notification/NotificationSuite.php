<?php
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'trunk/incubator/tests/Zym/Notification/Zym_NotificationTest.php';
require_once 'trunk/incubator/tests/Zym/Notification/Zym_Notification_RegistrationTest.php';
/**
 * Static test suite.
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

