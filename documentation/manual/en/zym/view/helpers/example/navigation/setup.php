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
        'order' => 100
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
                'active' => true // ACTIVE PAGE
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
                        'active' => '1' // ACTIVE PAGE
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
                                'active' => true // ACTIVE PAGE
                            )
                        )
                    )
                )
            )
        )
    ),
    array(
        'label' => 'ACL page 1 (guest.foo)',
        'uri' => '#acl-guest.foo',
        'resource' => 'guest.foo',
        'pages' => array(
            array(
                'label' => 'ACL page 1.1 (member.foo)',
                'uri' => '#acl-member.foo',
                'resource' => 'member.foo'
            ),
            array(
                'label' => 'ACL page 1.2 (member.bar)',
                'uri' => '#acl-member.bar',
                'resource' => 'member.bar'
            ),
            array(
                'label' => 'ACL page 1.3 (member.baz)',
                'uri' => '#acl-member.baz',
                'resource' => 'member.baz'
            ),
            array(
                'label' => 'ACL page 1.4 (member.baz + read privilege)',
                'uri' => '#acl-member.baz+read',
                'resource' => 'member.baz',
                'privilege' => 'read'
            ),
            array(
                'label' => 'ACL page 1.5 (member.baz + write privilege)',
                'uri' => '#acl-member.baz+write',
                'resource' => 'member.baz',
                'privilege' => 'write'
            )
        )
    ),
    array(
        'label' => 'ACL page 2 (admin.foo)',
        'uri' => '#acl-admin.foo',
        'resource' => 'admin.foo',
        'pages' => array(
            array(
                'label' => 'ACL page 2.1 (nothing)',
                'uri' => '#acl-nada'
            )
        )
    ),
    array(
        'label' => 'No link :o',
        'type' => 'uri',
        'title' => 'This URI page has no URI set, so a span is generated'
    )
);

// Create container from array
$container = new Zym_Navigation($config);

// Store the container in the proxy helper
$view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
$view->getHelper('navigation')->setContainer($container);

// ...or simply:
$view->navigation($container);

// Another option is to store the container in Zend_Registry
Zend_Registry::set('Zym_Navigation', $container);

// Add a route to show that MVC pages can be aware of routes and params
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();
$router->addRoute(
    'nav-route-example',
    new Zend_Controller_Router_Route('page2/:format', array(
        'controller' => 'page2', 'action' => 'index')
    )
);

// Add some ACL stuff to show integration with ACL
$acl = new Zend_Acl();

$acl->addRole(new Zend_Acl_Role('guest'));
$acl->addRole(new Zend_Acl_Role('member'), 'guest');
$acl->addRole(new Zend_Acl_Role('admin'), 'member');
$acl->addRole(new Zend_Acl_Role('special'), 'member');

$acl->add(new Zend_Acl_Resource('guest.foo'));
$acl->add(new Zend_Acl_Resource('member.foo'));
$acl->add(new Zend_Acl_Resource('member.bar'), 'member.foo');
$acl->add(new Zend_Acl_Resource('member.baz'));
$acl->add(new Zend_Acl_Resource('admin.foo'));

$acl->allow('guest', 'guest.foo');
$acl->allow('member', 'member.foo');
$acl->allow('special', 'member.baz', 'read');
$acl->allow('admin', null);

// Set ACL/role in the proxy helper
$view->navigation()->setAcl($acl)->setRole('special');

// ...or set default ACL/role statically
Zym_View_Helper_Navigation_Abstract::setDefaultAcl($navAcl);
Zym_View_Helper_Navigation_Abstract::setDefaultRole('special');