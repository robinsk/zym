<?php
$frontController = Zend_Controller_Front::getInstance();
$router          = $frontController->getRouter();
$router->addRoute('default', new Zym_Controller_Router_Route_HttpQuery());