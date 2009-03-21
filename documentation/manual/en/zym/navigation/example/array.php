<?php
/*
 * Create a container from an array
 *
 * Each element in the array will be passed to
 * Zym_Navigation_Page::factory() when constructing.
 */
$container = new Zym_Navigation(array(
    array(
        'label' => 'Page 1',
        'id' => 'home-link'
    ),
    array(
        'label' => 'Zym',
        'uri' => 'http://www.zym-project.com/',
        'order' => 100
    ),
    array(
        'label' => 'Page 2',
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
        'route' => 'nav-route-example',
        'params' => array(
            'format' => 'json'
        )
    ),
    array(
        'label' => 'Page 3',
        'action' => 'index',
        'controller' => 'index',
        'module' => 'mymodule',
        'reset_params' => false
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
        'uri' => '/setting/the/order/option',
        // setting order to -1 should make it appear first
        'order' => -1
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
    ),
    array(
        'label' => 'ACL page 1 (guest)',
        'uri' => '#acl-guest',
        'resource' => 'nav-guest',
        'pages' => array(
            array(
                'label' => 'ACL page 1.1 (foo)',
                'uri' => '#acl-foo',
                'resource' => 'nav-foo'
            ),
            array(
                'label' => 'ACL page 1.2 (bar)',
                'uri' => '#acl-bar',
                'resource' => 'nav-bar'
            ),
            array(
                'label' => 'ACL page 1.3 (baz)',
                'uri' => '#acl-baz',
                'resource' => 'nav-baz'
            ),
            array(
                'label' => 'ACL page 1.4 (bat)',
                'uri' => '#acl-bat',
                'resource' => 'nav-bat'
            )
        )
    ),
    array(
        'label' => 'ACL page 2 (member)',
        'uri' => '#acl-member',
        'resource' => 'nav-member'
    ),
    array(
        'label' => 'ACL page 3 (admin',
        'uri' => '#acl-admin',
        'resource' => 'nav-admin',
        'pages' => array(
            array(
                'label' => 'ACL page 3.1 (nothing)',
                'uri' => '#acl-nada'
            )
        )
    ),
    array(
        'label' => 'Zend Framework',
        'route' => 'zf-route'
    )
));