<?php
class MyForm extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $orderForm = new Zend_Form_SubForm();
        // Setup the rest of the form...
        $this->addSubForm($orderForm, 'order');


        $userForm = new Zend_Form_SubForm();
        // Setup the rest of the form...
        $this->addSubForm($userForm, 'order');

        $surveyForm = new Zend_Form_SubForm();
        // Setup the rest of the form...
        $this->addSubForm($surveyForm, 'order');
    }
}