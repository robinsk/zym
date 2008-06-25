<?php
$password = new Zend_Form_Element_Password('password');
$password->setLabel('Password')
        ->addValidator(new Zym_Validate_Confirm('password_confirm'));

$passwordConfirm = new Zend_Form_Element_Password('password_confirm');
$passwordConfirm->setLabel('Password Confirm');