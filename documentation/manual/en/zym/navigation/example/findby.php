<?php
$nav = new Zym_Navigation(array(
    array(
        'label' => 'Page 1',
        'uri'   => 'page-1',
        'foo'   => 'bar',
        'pages' => array(
            array(
                'label' => 'Page 1.1',
                'uri'   => 'page-1.1',
                'foo'   => 'bar',
            ),
            array(
                'label' => 'Page 1.2',
                'uri'   => 'page-1.2',
                'class' => 'my-class',
            ),
            array(
                'type'   => 'uri',
                'label'  => 'Page 1.3',
                'uri'    => 'page-1.3',
                'action' => 'about'
            )
        )
    ),
    array(
        'label'      => 'Page 2',
        'id'         => 'page_2_and_3',
        'class'      => 'my-class',
        'module'     => 'page2',
        'controller' => 'index',
        'action'     => 'page1'
    ),
    array(
        'label'      => 'Page 3',
        'id'         => 'page_2_and_3',
        'module'     => 'page3',
        'controller' => 'index'
    )
));

// The 'id' is not required to be unique, but be aware that
// having two pages with the same id will render the same id attribute
// in menus and breadcrumbs.
$found = $nav->findBy('id', 'page_2_and_3');       // returns Page 2
$found = $nav->findOneBy('id', 'page_2_and_3');    // returns Page 2
$found = $nav->findBy('id', 'page_2_and_3', true); // returns Page 2 and Page 3
$found = $nav->findById('page_2_and_3');           // returns Page 2
$found = $nav->findOneById('page_2_and_3');        // returns Page 2
$found = $nav->findAllById('page_2_and_3');        // returns Page 2 and Page 3

// Find all matching CSS class my-class
$found = $nav->findAllBy('class', 'my-class'); // returns Page 1.2 and Page 2
$found = $nav->findAllByClass('my-class');     // returns Page 1.2 and Page 2

// Find first matching CSS class my-class
$found = $nav->findOneByClasS('my-class');     // returns Page 1.2

// Find all matching CSS class non-existant
$found = $nav->findAllByClass('non-existant'); // returns array()

// Find first matching CSS class non-existant
$found = $nav->findOneByClass('non-existant'); // returns null

// Find all pages with custom property 'foo' = 'bar'
$found = $nav->findAllBy('foo', 'bar'); // returns Page 1 and Page 1.1

// To achieve the same magically, 'foo' must be in lowercase.
// This is because 'foo' is a custom property, and thus the
// property name is not normalized to 'Foo'
$found = $nav->findAllByfoo('bar');

// Find all with controller = 'index'
$found = $nav->findAllByController('index'); // returns Page 2 and Page 3
