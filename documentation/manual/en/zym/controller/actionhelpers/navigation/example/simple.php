<?php
// Bootstrap
Zend_Registry::set('Zym_Navigation', new Zym_Navigation());

// Inside controller action
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $helper     = $this->getHelper('Navigation');
        $navigation = $helper->getNavigation();

        // Direct method
        $navigation = $this->_helper->navigation();
    }
}