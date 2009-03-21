<?php
// getHref() returns /
$page = new Zym_Navigation_Page_Mvc(array(
    'action'     => 'index',
    'controller' => 'index'
));

// getHref() returns /blog/post/view
$page = new Zym_Navigation_Page_Mvc(array(
    'action'     => 'view',
    'controller' => 'post',
    'module'     => 'blog'
));

// getHref() returns /blog/post/view/id/1337
$page = new Zym_Navigation_Page_Mvc(array(
    'action'     => 'view',
    'controller' => 'post',
    'module'     => 'blog',
    'id'         => 1337
));
