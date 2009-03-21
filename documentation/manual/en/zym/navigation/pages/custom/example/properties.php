<?php
class My_Navigation_Page extends Zym_Navigation_Page
{
    private $_foo;
    private $_fooBar;

    public function setFoo($foo)
    {
        $this->_foo = $foo;
    }

    public function getFoo()
    {
        return $this->_foo;
    }

    public function setFooBar($fooBar)
    {
        $this->_fooBar = $fooBar;
    }

    public function getFooBar()
    {
        return $this->_fooBar;
    }

    public function getHref()
    {
        return $this->foo . '/' . $this->fooBar;
    }
}

// can now construct using
$page = new My_Navigation_Page(array(
    'label'   => 'Property names are mapped to setters',
    'foo'     => 'bar',
    'foo_bar' => 'baz'
));

// ...or
$page = Zym_Navigation_Page::factory(array(
    'type'    => 'My_Navigation_Page',
    'label'   => 'Property names are mapped to setters',
    'foo'     => 'bar',
    'foo_bar' => 'baz'
));
