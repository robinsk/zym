<?php
$page = Zym_Navigation_Page::factory(array(
    'label'  => 'My MVC page',
    'action' => 'index'
));

$page = Zym_Navigation_Page::factory(array(
    'label'      => 'Search blog',
    'action'     => 'index',
    'controller' => 'search',
    'module'     => 'blog'
));

$page = Zym_Navigation_Page::factory(array(
    'label'      => 'Home',
    'action'     => 'index',
    'controller' => 'index',
    'module'     => 'index',
    'route'      => 'home'
));

$page = Zym_Navigation_Page::factory(array(
    'type'   => 'mvc',
    'label'  => 'My MVC page'
));