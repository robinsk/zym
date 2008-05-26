<?php
class LoginController extends Zend_Controller_Action 
{
    public function loginAction()
    {
        $flashRedirector = $this->getHelper('FlashRedirector');
        
        // Login [...]
        
        if (!$auth) {
            // User failed auth, extend the redirect 
            // and reshow login form
            
            if ($flashRedirector->hasRedirect()) {
                $flashRedirector->extendRedirect();
            }
        } else {
            // Authentication sucess!
            // Redirect to previous requested page
            
            if ($flashRedirector->hasRedirect()) {
                $flashRedirector->redirect();
                
                // $flashRedirector->redirectAndExit();
            }
        }
    }
}