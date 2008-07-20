<?php

/*
 * Dispatched request:
 * - module:     default
 * - controller: index
 * - action:     index
 */

$page1 = new Zym_Navigation_Page_Mvc(array(
    'label'      => 'foo',
    'action'     => 'index',
    'controller' => 'index'
));

$page2 = new Zym_Navigation_Page_Mvc(array(
    'label'      => 'foo',
    'action'     => 'bar',
    'controller' => 'index'
));

$page1->isActive(); // returns true
$page2->isActive(); // returns false 

/*
 * Dispatched request:
 * - module:     blog
 * - controller: post
 * - action:     view
 * - id:         1337
 */

$page = new Zym_Navigation_Page_Mvc(array(
    'label'      => 'foo',
    'action'     => 'view',
    'controller' => 'post',
    'module'     => 'blog'
));

// returns true, because request has the same module, controller and action
$page->isActive(); 

/*
 * Dispatched request:
 * - module:     blog
 * - controller: post
 * - action:     view
 */

$page = new Zym_Navigation_Page_Mvc(array(
    'label'      => 'foo',
    'action'     => 'view',
    'controller' => 'post',
    'module'     => 'blog',
    'id'         => null
));

// returns false, because page requires the id param to be set in the request
$page->isActive(); // returns false
        
