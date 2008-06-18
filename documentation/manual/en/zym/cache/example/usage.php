<?php
// Create a Zend_Cache_Core_Function cache object
$cache = Zym_Cache::factory('Function');

// Create a Zend_Cache_Core cache object
// Specifying 'Core' is optional since its
// the default param
$cache = Zym_Cache::factory();

// Create a custom core object with custom
// backend
// In this instance, any keys here override the default
// config settings applied via Zym_Cache::setConfig();
$fOptions = array('cache_id_prefix' => 'bar_');
$bOptions = array('cache_dir' => '../cache');
$cache = Zym_Cache::factory('Core', 'File', $fOptions, $bOptions);