<?php
class MyClass
{
    protected $_message;
    
    public function __construct()
    {
        $this->_message = Zym_Message_Dispatcher::get();
        $this->_message->attach($this, 'testEvent');
    }
    
    public function __destruct()
    {
        $this->_message->detach($this);
    }
    
    public function notify(Zym_Message $message)
    {
        if ('testEvent' == $message->getName()) {
            // Assume Zend_Log instance
            $log = Zend_Registry::get('log');
            $log->log('testEvent was triggered and received by MyClass!');
        }
    }
}