<?php
// Route layout switcher: will swith the active layout to 
// admineLayout.phtml when the route called 'admin' is used.
$routeLayoutSwitcher = new Zym_Controller_Plugin_LayoutSwitcher_Route();
$routeLayoutSwitcher->addLayout('adminLayout', 'admin');

// Front Controller
$frontController->registerPlugin($routeLayoutSwitcher);

// Module layout switcher: will swith the active layout to 
// blogLayout.phtml when the blog module is requested.
$moduleLayoutSwitcher = new Zym_Controller_Plugin_LayoutSwitcher_Module();
$moduleLayoutSwitcher->addLayout('blogLayout', 'blog');

// Front Controller
$frontController->registerPlugin($moduleLayoutSwitcher);