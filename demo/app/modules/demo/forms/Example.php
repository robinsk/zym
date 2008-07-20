<?php
/**
 * Zym Framework Demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Form
 */
require_once 'Zend/Form.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_Form_Example extends Zend_Form
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->setName('example_form');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                 ->setRequired(true)
                 ->addValidators(array(
                    'NotEmpty'
                 ));

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setRequired(true)
                 ->addValidators(array(
                    'NotEmpty'
                 ));

        $rememberMe = new Zend_Form_Element_Checkbox('remember_me');
        $rememberMe->setLabel('Remember me');

        $login = new Zend_Form_Element_Submit('Login');

        $this->addElements(array($username, $password, $rememberMe, $login));
    }
}