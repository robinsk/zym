<?php
/**
* @see Zend_Loader
*/
require_once 'Zend/Loader.php';

// Register Zend's autoloader
Zend_Loader::registerAutoload();

// Register Zym Doctrine autoloader
Zend_Loader::registerAutoload('Zym_Loader_Autoload_Doctrine');

// Unregister Zym Doctrine autoloader
Zend_Loader::registerAutoload('Zym_Loader_Autoload_Doctrine', false);