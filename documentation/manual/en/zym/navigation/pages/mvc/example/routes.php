<?php

// the following route is added to the ZF router 
Zend_Controller_Front::getInstance()->getRouter()->addRoute(
    'article_view', // route name
    new Zend_Controller_Router_Route(
        'a/:id',
        array(
            'module'     => 'news',
            'controller' => 'article',
            'action'     => 'view',
            'id'         => null
        )
    )
);

// a page is created with a 'route' option
$page = new Zym_Navigation_Page_Mvc(array(
    'label'  => 'A news article',
    'route'  => 'article_view',
    'params' => array(
        'id' => 42
    )
));

// returns: /a/42
$page->getHref();
