<?php
/**
 * @see Zend_Form
 */
require_once 'Zend/Form.php';

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
        $password->setLabel('Password');
        
        $rememberMe = new Zend_Form_Element_Checkbox('remember_me');
        $rememberMe->setLabel('Remember me');
        
        $login = new Zend_Form_Element_Submit('Login');
        
        $this->addElements(array($username, $password, $rememberMe, $login));
    }
}