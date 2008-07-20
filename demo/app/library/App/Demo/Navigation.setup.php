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
    ),
    array(
        'label' => 'ACL page 1 (guest)',
        'uri' => '#acl-guest',
        'role' => 'nav-guest',
        'pages' => array(
            array(
                'label' => 'ACL page 1.1 (foo)',
                'uri' => '#acl-foo',
                'role' => 'nav-foo'
            ),
            array(
                'label' => 'ACL page 1.2 (bar)',
                'uri' => '#acl-bar',
                'role' => 'nav-bar'
            ),
            array(
                'label' => 'ACL page 1.3 (baz)',
                'uri' => '#acl-baz',
                'role' => 'nav-baz'
            ),
            array(
                'label' => 'ACL page 1.4 (bat)',
                'uri' => '#acl-bat',
                'role' => 'nav-bat'
            )
        )
    ),
    array(
        'label' => 'ACL page 2 (member)',
        'uri' => '#acl-member',
        'role' => 'nav-member'
    ),
    array(
        'label' => 'ACL page 3 (admin',
        'uri' => '#acl-admin',
        'role' => 'nav-admin',
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
);

// Create navigation from array
$navigation = new Zym_Navigation($config);

// put navigation in registry so it's found by helpers
Zend_Registry::set('Zym_Navigation_Demo', $navigation);

// add a route to show that zym_navigation
// can be aware of routes and params
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();
$router->addRoute(
    'nav-route-example',
    new Zend_Controller_Router_Route('page2/:format', array(
        'controller' => 'page2', 'action' => 'index')
    )
);
$router->addRoute(
    'zf-route',
    new Zend_Controller_Router_Route(array(
        'host' => 'framework.zend.com',
        'path' => '/')
    )
);

// add some ACL stuff
$navAcl = new Zend_Acl();
$navAcl->addRole(new Zend_Acl_Role('nav-guest'));
$navAcl->addRole(new Zend_Acl_Role('nav-member'), 'nav-guest');
$navAcl->addRole(new Zend_Acl_Role('nav-admin'), 'nav-member');
$navAcl->addRole(new Zend_Acl_Role('nav-foo'));
$navAcl->addRole(new Zend_Acl_Role('nav-bar'));
$navAcl->addRole(new Zend_Acl_Role('nav-baz'));
$navAcl->addRole(new Zend_Acl_Role('nav-bat'));
Zend_Registry::set('Zym_Navigation_Demo_Acl', $navAcl);

// do the following in the view (for this demo we keep it simple)
/*
$this->menu()->setAcl(Zend_Registry::get('Zym_Navigation_Demo_Acl'));
$this->menu()->setRole(array('nav-member', 'nav-bar'));
*/