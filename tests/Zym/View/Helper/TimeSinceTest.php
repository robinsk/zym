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
 * @see Zym_View_Helper_TimeSince
 */
require_once 'Zym/View/Helper/TimeSince.php';

/**
 * Zym_View_Helper_TimeSince
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_TimeSinceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_View_Helper_TimeSince
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
        $helper = new Zym_View_Helper_TimeSince();
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

    public function testTimeSinceUsesCurrentTime()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 day', $helper->timeSince(strtotime('-1 day')));
        $this->assertEquals('2 weeks', $helper->timeSince(strtotime('-2 weeks')));
    }

    public function testTimeSinceUsesCustomTime()
    {
        $helper = $this->_helper;

        $this->assertEquals('2 days', $helper->timeSince(strtotime('-3 day'), strtotime('-1 day')));
        $this->assertEquals('2 years', $helper->timeSince(strtotime('-3 year'), strtotime('-1 year')));
    }

    public function testTimeSinceReturnsYear()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 year', $helper->timeSince(strtotime('-1 year')));
        $this->assertEquals('2 years', $helper->timeSince(strtotime('-3 years'), strtotime('-1 year')));
        $this->assertEquals('-2 years', $helper->timeSince(strtotime('+2 year')));
    }

    public function testTimeSinceReturnsMonth()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 month', $helper->timeSince(strtotime('-1 month')));
        $this->assertEquals('2 months', $helper->timeSince(strtotime('-3 months'), strtotime('-1 month')));
        $this->assertEquals('-2 months', $helper->timeSince(strtotime('+2 months')));
    }

    public function testTimeSinceReturnsAWeek()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 week', $helper->timeSince(strtotime('-1 week')));
        $this->assertEquals('2 weeks', $helper->timeSince(strtotime('-2 weeks')));
        $this->assertEquals('-2 weeks', $helper->timeSince(strtotime('+2 weeks')));
    }

    public function testTimeSinceReturnsADay()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 day', $helper->timeSince(strtotime('-1 day')));
        $this->assertEquals('2 days', $helper->timeSince(strtotime('-2 days')));
        $this->assertEquals('-2 days', $helper->timeSince(strtotime('+2 days')));
    }

    public function testTimeSinceReturnsAnHour()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 hour', $helper->timeSince(strtotime('-1 hour')));
        $this->assertEquals('2 hours', $helper->timeSince(strtotime('-2 hours')));
        $this->assertEquals('-2 hours', $helper->timeSince(strtotime('+2 hours')));
    }

    public function testTimeSinceReturnsAMinute()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 minute', $helper->timeSince(strtotime('-1 minute')));
        $this->assertEquals('2 minutes', $helper->timeSince(strtotime('-2 minutes')));
        $this->assertEquals('-2 minutes', $helper->timeSince(strtotime('+2 minutes')));
    }

    public function testTimeSinceReturnsASecond()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 second', $helper->timeSince(strtotime('-1 second')));
        $this->assertEquals('2 seconds', $helper->timeSince(strtotime('-2 seconds')));
        $this->assertEquals('-2 seconds', $helper->timeSince(strtotime('+2 seconds')));
    }

    public function testTimeSinceReturnsSmallerChunk()
    {
        $helper = $this->_helper;

        $this->assertEquals('1 day and 5 hours', $helper->timeSince(strtotime('-1 day, -5 hours')));
        $this->assertEquals('2 days and 5 hours', $helper->timeSince(strtotime('-2 days, -5 hours')));
        $this->assertEquals('2 days', $helper->timeSince(strtotime('-2 days, -5 minutes')));
        $this->assertEquals('-2 days and -5 hours', $helper->timeSince(strtotime('+2 days, +5 hours')));
    }

    public function testTimeSinceReturnsLessThanASecond()
    {
        $helper = $this->_helper;
        $this->assertEquals('less than a second', $helper->timeSince(time()));
    }

    public function testTimeSinceWorksWithTranslate()
    {
        $data = array(
            'less than a second' => 'bar',
            '%d weeks'            => '%d bar'
        );

        $translate = new Zend_Translate('array', $data, 'en');
        $helper    = $this->_helper;
        Zend_Registry::set('Zend_Translate', $translate);

        $this->assertEquals('bar', $helper->timeSince(time()));
        $this->assertEquals('2 bar', $helper->timeSince(strtotime('-2 weeks')));

        $registry = Zend_Registry::getInstance();
        unset($registry['Zend_Translate']);
    }
}