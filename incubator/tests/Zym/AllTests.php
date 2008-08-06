<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

set_include_path(dirname(__FILE__)  . '/../' . PATH_SEPARATOR
                . dirname(__FILE__) . '/../../library' . PATH_SEPARATOR
                . dirname(__FILE__) . '/../../../library' . PATH_SEPARATOR
                . dirname(__FILE__) . '/../../../demo/library/incubator' . PATH_SEPARATOR
                . dirname(__FILE__) . '/../../../demo/library' . PATH_SEPARATOR
                . get_include_path());

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zym_AllTests::main');
}

/**
 * @see PHPUnit_TextUI_TestRunner
 */
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * @see PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';

if (PHPUnit_MAIN_METHOD == 'Zym_AllTests::main') {
    Zym_AllTests::main();
}

/**
 * AllTests for Zym
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_AllTests
{
    /**
     * Main()
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zym Framework - Incubator');
        $suite->addTestFiles(glob(dirname(__FILE__) . '/*Suite.php'));
        return $suite;
    }
}