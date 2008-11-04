<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * @see Zym_View
 */
require_once 'Zym/View.php';

/**
 * @see Zym_View_Helper_TimeUntil
 */
require_once 'Zym/View/Helper/TimeUntil.php';

/**
 * Zym_View_Helper_TimeUntil
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_TimeUntilTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_View_Helper_TimeUntil
     */
    private $_helper;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $view   = new Zym_View();
        $helper = new Zym_View_Helper_TimeUntil();
        $helper->setView($view);

        $this->_helper = $helper;
    }

    /**
     * Cleans up the environment after running a test.
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->_helper);
    }

    public function testTimeUntilUsesCurrentTime()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 day', $helper->timeUntil(strtotime('+1 day')));
        $this->assertEquals('2 weeks', $helper->timeUntil(strtotime('+2 weeks')));
    }

    public function testTimeUntilUsesCustomTime()
    {
        $helper = $this->_helper;

        $this->assertEquals('2 days', $helper->timeUntil(strtotime('+3 day'), strtotime('+1 day')));
        $this->assertEquals('2 years', $helper->timeUntil(strtotime('+3 year'), strtotime('+1 year')));
    }

    public function testTimeUntilReturnsYear()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 year', $helper->timeUntil(strtotime('+1 year')));
        $this->assertEquals('2 years', $helper->timeUntil(strtotime('+3 years'), strtotime('+1 year')));
        $this->assertEquals('-2 years', $helper->timeUntil(strtotime('-2 years')));
    }

    public function testTimeUntilReturnsMonth()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 month', $helper->timeUntil(strtotime('+1 month')));
        $this->assertEquals('2 months', $helper->timeUntil(strtotime('+3 months'), strtotime('+1 month')));
        $this->assertEquals('-2 months', $helper->timeUntil(strtotime('-2 months')));
    }

    public function testTimeUntilReturnsAWeek()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 week', $helper->timeUntil(strtotime('+1 week')));
        $this->assertEquals('2 weeks', $helper->timeUntil(strtotime('+2 weeks')));
        $this->assertEquals('-2 weeks', $helper->timeUntil(strtotime('-2 weeks')));
    }

    public function testTimeUntilReturnsADay()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 day', $helper->timeUntil(strtotime('+1 day')));
        $this->assertEquals('2 days', $helper->timeUntil(strtotime('+2 days')));
        $this->assertEquals('-2 days', $helper->timeUntil(strtotime('-2 days')));
    }

    public function testTimeUntilReturnsAnHour()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 hour', $helper->timeUntil(strtotime('+1 hour')));
        $this->assertEquals('2 hours', $helper->timeUntil(strtotime('+2 hours')));
        $this->assertEquals('-2 hours', $helper->timeUntil(strtotime('-2 hours')));
    }

    public function testTimeUntilReturnsAMinute()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 minute', $helper->timeUntil(strtotime('+1 minute')));
        $this->assertEquals('2 minutes', $helper->timeUntil(strtotime('+2 minutes')));
        $this->assertEquals('-2 minutes', $helper->timeUntil(strtotime('-2 minutes')));
    }

    public function testTimeUntilReturnsASecond()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 second', $helper->timeUntil(strtotime('+1 second')));
        $this->assertEquals('2 seconds', $helper->timeUntil(strtotime('+2 seconds')));
        $this->assertEquals('-2 seconds', $helper->timeUntil(strtotime('-2 seconds')));
    }

    public function testTimeUntilReturnsSmallerChunk()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 day and 5 hours', $helper->timeUntil(strtotime('+1 day, +5 hours')));
        $this->assertEquals('2 days and 5 hours', $helper->timeUntil(strtotime('+2 days, +5 hours')));
        $this->assertEquals('2 days', $helper->timeUntil(strtotime('+2 days, +5 minutes')));
        $this->assertEquals('-2 days and -5 hours', $helper->timeUntil(strtotime('-2 days, -5 hours')));
    }

    public function testTimeUntilReturnsLessThanASecond()
    {
        $helper = $this->_helper;
        $this->assertEquals('less than a second', $helper->timeUntil(time()));
    }

    public function testTimeUntilWorksWithTranslate()
    {
        $data = array(
            'less than a second' => 'bar',
            '%d weeks'            => '%d bar'
        );

        $translate = new Zend_Translate('array', $data, 'en');
        $helper    = $this->_helper;
        Zend_Registry::set('Zend_Translate', $translate);

        $this->assertEquals('bar', $helper->timeUntil(time()));
        $this->assertEquals('2 bar', $helper->timeUntil(strtotime('+2 weeks')));

        $registry = Zend_Registry::getInstance();
        unset($registry['Zend_Translate']);
    }
}