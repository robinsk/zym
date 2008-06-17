<?php
/*
 * An array of example pages
 * 
 * Each element in the array will be passed to
 * Zym_Navigation_Page::factory() when constructing
 * the navigation object below.
 */
$config = array(
    array(
        'label' => 'Page 1',
        'action' => 'index',
        'controller' => 'index',
        'id' => 'home-link'
    ),
    array(
        'label' => 'Zym',
        'uri' => 'http://www.zym-project.com/',
        'position' => 100
    ),
    array(
        'label' => 'Page 2',
        'action' => 'index',
        'controller' => 'page2',
        'pages' => array(
            array(
                'label' => 'Page 2.1',
                'action' => 'page2_1',
                'controller' => 'page2',
                'class' => 'special-one',
                'title' => 'This element has a special class',
                'active' => true
            ),
            array(
                'label' => 'Page 2.2',
                'action' => 'page2_2',
                'controller' => 'page2',
                'class' => 'special-two',
                'title' => 'This element has a special class too'
            )
        )
    ),
    array(
        'label' => 'Page 2 with params',
        'action' => 'index',
        'controller' => 'page2',
        // specify a param or two
        'params' => array(
            'format' => 'json',
            'foo' => 'bar'
        )
    ),
    array(
        'label' => 'Page 2 with params and a route',
        'action' => 'index',
        'controller' => 'page2',
        // specify a route name and a param for the route
        'route' => 'myroute',
        'params' => array(
            'format' => 'json'
        )
    ),
    array(
        'label' => 'Page 3',
        'action' => 'index',
        'controller' => 'index',
        'module' => 'mymodule'
    ),
    array(
        'label' => 'Page 4',
        'uri' => '#',
        'pages' => array(
            array(
                'label' => 'Page 4.1',
                'uri' => '/page4',
                'title' => 'Page 4 using uri',
                'pages' => array(
                    array(
                        'label' => 'Page 4.1.1',
                        'title' => 'Page 4 using mvc params',
                        'action' => 'index',
                        'controller' => 'page4',
                        // let's say this page is active
                        'active' => '1'
                    )
                )
            )
        )
    ),
    array(
        'label' => 'Page 0?',
        'uri' => '/setting/the/position/option',
        // setting position to -1 should make it appear first
        'position' => -1
    ),
    array(
        'label' => 'Page 5',
        'uri' => '/',
        // this page should not be visible
        'visible' => false,
        'pages' => array(
            array(
                'label' => 'Page 5.1',
                'uri' => '#',
                'pages' => array(
                    array(
                        'label' => 'Page 5.1.1',
                        'uri' => '#',
                        'pages' => array(
                            array(
                                'label' => 'Page 5.1.2',
                                'uri' => '#',
                                // let's say this page is active
                                'active' => true
                            )
                        )
                    )
                )
            )
        )
    )
);

// create navigation from array
$navigation = new Zym_Navigation($config);

// put navigation in registry so it's found by view helpers
Zend_Registry::set('Zym_Navigation', $navigation);

// add a route to show that zym_navigation
// can be aware of routes and params
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();
$router->addRoute(
    'myroute',
    new Zend_Controller_Router_Route('page2/:format', array(
        'controller' => 'page2', 'action' => 'index')
    )
);