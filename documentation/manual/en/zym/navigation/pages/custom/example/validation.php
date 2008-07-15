<?php
require_once 'Zym/Navigation/Page.php';

class Zym_Navigation_Page_Mvc extends Zym_Navigation_Page
{
    protected $_action = null;
    protected $_controller = null;
    
    /**
     * An MVC page must have controller and action set
     *
     * @return void
     * @throws Zym_Navigation_Page_InvalidException  if page is invalid
     */
    protected function _validate()
    {
        if (!isset($this->_controller)) {
            $msg = 'Page controller is not set';
        } elseif (!isset($this->_action)) {
            $msg = 'Page action is not set';
        }
        
        if (isset($msg)) {
            require_once 'Zym/Navigation/Page/InvalidException.php';
            throw new Zym_Navigation_Page_InvalidException($msg);
        } else {
            parent::_validate();
        }
    }
}
