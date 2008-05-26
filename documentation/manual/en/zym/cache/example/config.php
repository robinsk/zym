<?php
$config = new Zend_Config(array(
    'default_backend' => 'Apc',
    
    'frontends' => array(
        'core' => array(
            'automatic_serialization' => true
        )
    ),
    
    'backends' => array(
        'apc' => array(
            // Apc has no configuration options
            // so this array is not required;
            // however, we specify it for example
        ),
        
        'file' => array(
            'cache_dir' => '../tmp'
        )
    )
));

Zym_Cache::setConfig($config);