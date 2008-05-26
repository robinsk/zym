<?php
// [...]
// Inside a controller plugin
class My_Auth_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Predispatch
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Check if user is authenticated
        // [...]
        
        // Store url before redirecting user to login page
        $flashRedirector = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashRedirector');
        $flashRedirector->setRedirect($request->getRequestUri());
        
        // Redirect to login page
        // [...]
    }
}