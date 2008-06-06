<?php
class Foo_BarController extends Zend_Controller_Action 
{
    public function indexAction()
    {
        // Loads from current module 'Foo'/forms/BarIndex.php
        $form = $this->getHelper('Form')->create();
        
        $this->view->form = $form;
    }
}