<?php
$route = new Zym_Controller_Router_Route_HttpHost(
    ':user.*.*', ':action/*', 
    array(
        'controller' => 'foo',
        'action'     => 'bar'
    )
);
$router->addRoute('user', $route);