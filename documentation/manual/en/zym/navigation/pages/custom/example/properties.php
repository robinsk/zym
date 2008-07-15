<?php
require_once 'Zym/Navigation/Page.php';

class My_Navigation_Page extends Zym_Navigation_Page
{
    protected $_foo;
    protected $_fooBar;
    
    public function setFoo($foo)
    {
        $this->_foo = $foo;
    }
    
    public function setFooBar($fooBar)
    {
        $this->_fooBar = $fooBar;
    }
}

// can now construct using
$page = new My_Navigation_Page(array(
    'label'   => 'Property names are translated',
    'foo'     => 'bar',
    'foo_bar' => 'baz'
));
