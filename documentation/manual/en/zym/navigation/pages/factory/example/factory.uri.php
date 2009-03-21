<?php
$page = Zym_Navigation_Page::factory(array(
    'label' => 'My URI page',
    'uri'   => 'http://www.example.com/'
));

$page = Zym_Navigation_Page::factory(array(
    'label'  => 'Search',
    'uri'    => 'http://www.example.com/search',
    'active' => true
));

$page = Zym_Navigation_Page::factory(array(
    'label' => 'My URI page',
    'uri'   => '#'
));

$page = Zym_Navigation_Page::factory(array(
    'type'   => 'uri',
    'label'  => 'My URI page'
));