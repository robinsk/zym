<?php
// create container
$container = new Zym_Navigation();

// add page by giving a page instance
$container->addPage(Zym_Navigation_Page::factory(array(
    'uri' => 'http://www.example.com/'
)))

// add page by giving an array
$container->addPage(array(
    'uri' => 'http://www.example.com/'
)))

// add page by giving a config object
$container->addPage(new Zend_Config(array(
    'uri' => 'http://www.example.com/'
)))

$pages = array(
    array(
        'label'  => 'Save'
        'action' => 'save',
    ),
    array(
        'label' =>  'Delete',
        'action' => 'delete'
    )
);

// add two pages
$container->addPages($pages);

// remove existing pages and add the given pages
$container->setPages($pages);