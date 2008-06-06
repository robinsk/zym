<?php
class Foo_BarController extends Zend_Controller_Action 
{
    public function indexAction()
    {
        // Loads from current module 'Foo'/forms/Example.php
        $form = $this->getHelper('Form')->create('Example');
        
        $this->view->form = $form;
    }
}