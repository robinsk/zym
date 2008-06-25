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
 * @package Zym_Validate
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Validate_Confirm
 */
require_once 'Zym/Validate/Confirm.php';

/**
 * Zym_Validate_Confirm
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym_Tests
 * @package Zym_Validate
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Validate_ConfirmTest extends PHPUnit_Framework_TestCase
{
    public function testContextInMethodIsValid()
    {
        $confirm = new Zym_Validate_Confirm('test');
        $isValid = $confirm->isValid('value', array('test' => 'value'));

        $this->assertEquals(true, $isValid);
    }

    public function testContextInMethodIsNotValid()
    {
        $confirm = new Zym_Validate_Confirm('test');
        $isValid = $confirm->isValid('invalid', array('test' => 'value'));

        $this->assertEquals(false, $isValid);
    }

    public function testContextStringInMethodIsValid()
    {
        $confirm = new Zym_Validate_Confirm(null);
        $isValid = $confirm->isValid('test', 'test');

        $this->assertEquals(false, $isValid);
    }

    public function testContextStringInMethodIsNotValid()
    {
        $confirm = new Zym_Validate_Confirm(null);
        $isValid = $confirm->isValid('invalid', 'test');

        $this->assertEquals(false, $isValid);
    }

    public function testContextInConstructorIsValid()
    {
        $confirm = new Zym_Validate_Confirm('test', array('test' => 'value'));
        $isValid = $confirm->isValid('value');

        $this->assertEquals(true, $isValid);
    }

    public function testContextInConstructorIsNotValid()
    {
        $confirm = new Zym_Validate_Confirm('test', array('test' => 'value'));
        $isValid = $confirm->isValid('invalid');

        $this->assertEquals(false, $isValid);
    }

    public function testContextStringInConstructorIsValid()
    {
        $confirm = new Zym_Validate_Confirm(null, 'test');
        $isValid = $confirm->isValid('test');

        $this->assertEquals(false, $isValid);
    }

    public function testContextStringInConstructorIsNotValid()
    {
        $confirm = new Zym_Validate_Confirm(null, 'test');
        $isValid = $confirm->isValid('invalid');

        $this->assertEquals(false, $isValid);
    }

    public function testNoContext()
    {
        $confirm = new Zym_Validate_Confirm(null);
        $isValid = $confirm->isValid('invalid');

        $this->setExpectedException('Zym_Validate_Exception');
    }
}