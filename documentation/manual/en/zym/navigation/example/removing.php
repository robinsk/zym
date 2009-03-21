<?php
$container = new Zym_Navigation(array(
    array(
        'label'  => 'Page 1',
        'action' => 'page1'
    ),
    array(
        'label'  => 'Page 2',
        'action' => 'page2',
        'order'  => 200
    ),
    array(
        'label'  => 'Page 3',
        'action' => 'page3'
    )
));

// remove page by implicit page order
$container->removePage(0);      // removes Page 1

// remove page by instance
$page3 = $container->findOneByAction('Page 3');
$container->removePage($page3); // removes Page 3

// remove page by explicit page order
$container->removePage(200);    // removes Page 2

// remove all pages
$container->removePages();      // removes all pages