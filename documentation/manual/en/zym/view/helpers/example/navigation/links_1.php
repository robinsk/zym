<?php
$container = new Zym_Navigation(array(
    array(
        'label' => 'Relations using strings',
        'rel'   => array(
            'alternate' => 'http://www.example.org/'
        ),
        'rev'   => array(
            'alternate' => 'http://www.example.net/'
        )
    ),
    array(
        'label' => 'Relations using arrays',
        'rel'   => array(
            'alternate' => array(
                'label' => 'Example.org',
                'uri'   => 'http://www.example.org/'
            )
        )
    ),
    array(
        'label' => 'Relations using configs',
        'rel'   => array(
            'alternate' => new Zend_Config(array(
                'label' => 'Example.org',
                'uri'   => 'http://www.example.org/'
            ))
        )
    ),
    array(
        'label' => 'Relations using pages instance',
        'rel'   => array(
            'alternate' => Zym_Navigation_Page::factory(array(
                'label' => 'Example.org',
                'uri'   => 'http://www.example.org/'
            ))
        )
    )
));