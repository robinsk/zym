<?php
/*
 * Create a container from an array
 */
$container = new Zym_Navigation(array(
    array(
        'label' => 'Page 1',
        'uri'   => '#'
    ),
    array(
        'label' => 'Page 2',
        'uri'   => '#',
        'pages' => array(
            array(
                'label' => 'Page 2.1',
                'uri'   => '#'
            ),
            array(
                'label' => 'Page 2.2',
                'uri'   => '#'
            )
        )
    )
    array(
        'label' => 'Page 3',
        'uri'   => '#'
    )
));

// Iterate flat using regular foreach:
// Output: Page 1, Page 2, Page 3
foreach ($container as $page) {
    echo $page->label;
}

// Iterate recursively using RecursiveIteratorIterator
$it = new RecursiveIteratorIterator(
        $container, RecursiveIteratorIterator::SELF_FIRST);

// Output: Page 1, Page 2, Page 2.1, Page 2.2, Page 3
foreach ($it as $page) {
    echo $page->label;
}