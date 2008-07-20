<?php

class My_Navigation_Page extends Zym_Navigation_Page
{
    protected $_fooBar = 'ok';
    
    public function setFooBar($fooBar)
    {
        $this->_fooBar = $fooBar;
    }
}

$page = Zym_Navigation_Page::factory(array(
    'type'    => 'My_Navigation_Page',
    'label'   => 'My custom page',
    'foo_bar' => 'foo bar'
));