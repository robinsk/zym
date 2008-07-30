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
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * @see Zend_Translate_Adapter_Array
 */
require_once 'Zend/Translate/Adapter/Array.php';

/**
 * @see Zym_Controller_Action_Helper_Translator
 */
require_once 'Zym/Controller/Action/Helper/Translator.php';

/**
 * Tests the class Zym_Controller_Action_Helper_Translate
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_TranslatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_Controller_Action_Helper_Translator
     */
    protected $_helper;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->clearRegistry();
        $this->_helper = new Zym_Controller_Action_Helper_Translator();
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->_helper);
        $this->clearRegistry();
    }

    /**
     * Clear the registry
     *
     * @return Zym_Controller_Action_Helper_TranslatorTest
     */
    public function clearRegistry()
    {
        $regKey = 'Zend_Translate';
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }

        return $this;
    }

    public function testTranslationObjectPassedToConstructorUsedForTranslation()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $helper = new Zym_Controller_Action_Helper_Translator($trans);
        $this->assertEquals('eins', $helper->translate('one'));
        $this->assertEquals('three', $helper->translate('three'));
    }

    public function testLocalTranslationObjectUsedForTranslationsWhenPresent()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $this->_helper->setTranslator($trans);
        $this->assertEquals('eins', $this->_helper->translate('one'));
        $this->assertEquals('three', $this->_helper->translate('three'));
    }

    public function testTranslationObjectInRegistryUsedForTranslationsInAbsenceOfLocalTranslationObject()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        Zend_Registry::set('Zend_Translate', $trans);
        $this->assertEquals('eins', $this->_helper->translate('one'));
    }

    public function testOriginalMessagesAreReturnedWhenNoTranslationObjectPresent()
    {
        $this->assertEquals('one', $this->_helper->translate('one'));
        $this->assertEquals('three', $this->_helper->translate('three'));
    }

    public function testPassingNonNullNonTranslationObjectToConstructorThrowsException()
    {
        try {
            $helper = new Zym_Controller_Action_Helper_Translator('something');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testPassingNonTranslationObjectToSetTranslatorThrowsException()
    {
        try {
            $this->_helper->setTranslator('something');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testRetrievingLocaleWhenNoTranslationObjectSetThrowsException()
    {
        try {
            $this->_helper->getLocale();
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testSettingLocaleWhenNoTranslationObjectSetThrowsException()
    {
        try {
            $this->_helper->setLocale('de');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testCanSetLocale()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');
        $trans->setLocale('de');

        $this->_helper->setTranslator($trans);
        $this->assertEquals('eins', $this->_helper->translate('one'));

        $new = $this->_helper->setLocale('it');
        $this->assertTrue($new instanceof Zym_Controller_Action_Helper_Translator);
        $this->assertEquals('it', $new->getLocale());
        $this->assertEquals('uno', $this->_helper->translate('one'));
    }

    public function testHelperImplementsFluentInterface()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');
        $trans->setLocale('de');

        $locale = $this->_helper->translate()->setTranslator($trans)->getLocale();

        $this->assertEquals('de', $locale);
    }

    public function testCanTranslateWithOptions()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins',
                                                   "two %1\$s" => "zwei %1\$s",
                                                   "three %1\$s %2\$s" => "drei %1\$s %2\$s"
                                            ), 'de');
        $trans->addTranslation(array(
            'one' => 'uno',
            "two %1\$s" => "duo %2\$s",
            "three %1\$s %2\$s" => "tre %1\$s %2\$s"
        ), 'it');
        $trans->setLocale('de');

        $this->_helper->setTranslator($trans);
        $this->assertEquals("drei 100 200", $this->_helper->translate("three %1\$s %2\$s", "100", "200"));
        $this->assertEquals("tre 100 200", $this->_helper->translate("three %1\$s %2\$s", "100", "200", 'it'));
        $this->assertEquals("drei 100 200", $this->_helper->translate("three %1\$s %2\$s", array("100", "200")));
        $this->assertEquals("tre 100 200", $this->_helper->translate("three %1\$s %2\$s", array("100", "200"), 'it'));
    }

    public function testTranslationObjectNullByDefault()
    {
        $this->assertNull($this->_helper->getTranslator());
    }

    public function testLocalTranslationObjectIsPreferredOverRegistry()
    {
        $transReg = new Zend_Translate('array', array());
        Zend_Registry::set('Zend_Translate', $transReg);

        $this->assertSame($transReg->getAdapter(), $this->_helper->getTranslator());

        $transLoc = new Zend_Translate('array', array());
        $this->_helper->setTranslator($transLoc);
        $this->assertSame($transLoc->getAdapter(), $this->_helper->getTranslator());
        $this->assertNotSame($transLoc->getAdapter(), $transReg->getAdapter());
    }

    public function testHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->_helper->translate();
        $this->assertSame($this->_helper, $helper);

        $transLoc = new Zend_Translate('array', array());
        $this->_helper->setTranslator($transLoc);
        $helper = $this->_helper->translate();
        $this->assertSame($this->_helper, $helper);
    }

    public function testDirectOriginalMessagesAreReturnedWhenNoTranslationObjectPresent()
    {
        $this->assertEquals('one', $this->_helper->direct('one'));
        $this->assertEquals('three', $this->_helper->direct('three'));
    }

    public function testDirectHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->_helper->direct();
        $this->assertSame($this->_helper, $helper);

        $transLoc = new Zend_Translate('array', array());
        $this->_helper->setTranslator($transLoc);
        $helper = $this->_helper->direct();
        $this->assertSame($this->_helper, $helper);
    }

    public function testDirectCanTranslateWithOptions()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins',
                                                   "two %1\$s" => "zwei %1\$s",
                                                   "three %1\$s %2\$s" => "drei %1\$s %2\$s"
                                            ), 'de');
        $trans->addTranslation(array(
            'one' => 'uno',
            "two %1\$s" => "duo %2\$s",
            "three %1\$s %2\$s" => "tre %1\$s %2\$s"
        ), 'it');
        $trans->setLocale('de');

        $this->_helper->setTranslator($trans);
        $this->assertEquals("drei 100 200", $this->_helper->direct("three %1\$s %2\$s", "100", "200"));
        $this->assertEquals("tre 100 200", $this->_helper->direct("three %1\$s %2\$s", "100", "200", 'it'));
        $this->assertEquals("drei 100 200", $this->_helper->direct("three %1\$s %2\$s", array("100", "200")));
        $this->assertEquals("tre 100 200", $this->_helper->direct("three %1\$s %2\$s", array("100", "200"), 'it'));
    }

    public function test_OriginalMessagesAreReturnedWhenNoTranslationObjectPresent()
    {
        $this->assertEquals('one', $this->_helper->_('one'));
        $this->assertEquals('three', $this->_helper->_('three'));
    }

    public function test_HelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->_helper->_();
        $this->assertSame($this->_helper, $helper);

        $transLoc = new Zend_Translate('array', array());
        $this->_helper->setTranslator($transLoc);
        $helper = $this->_helper->_();
        $this->assertSame($this->_helper, $helper);
    }

    public function test_CanTranslateWithOptions()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins',
                                                   "two %1\$s" => "zwei %1\$s",
                                                   "three %1\$s %2\$s" => "drei %1\$s %2\$s"
                                            ), 'de');
        $trans->addTranslation(array(
            'one' => 'uno',
            "two %1\$s" => "duo %2\$s",
            "three %1\$s %2\$s" => "tre %1\$s %2\$s"
        ), 'it');
        $trans->setLocale('de');

        $this->_helper->setTranslator($trans);
        $this->assertEquals("drei 100 200", $this->_helper->_("three %1\$s %2\$s", "100", "200"));
        $this->assertEquals("tre 100 200", $this->_helper->_("three %1\$s %2\$s", "100", "200", 'it'));
        $this->assertEquals("drei 100 200", $this->_helper->_("three %1\$s %2\$s", array("100", "200")));
        $this->assertEquals("tre 100 200", $this->_helper->_("three %1\$s %2\$s", array("100", "200"), 'it'));
    }
}