<?php
/**
* @see Zym_Loader_Autoload_Interface
*/
require_once 'Zym/Loader/Autoload/Interface.php';

class App_Loader_Autoload_Foo
{
    /**
     * spl_autoload() suitable implementation for supporting class autoloading.
     *
     * Attach to spl_autoload() using the following:
     * <code>
     * spl_autoload_register(array('Zend_Loader', 'autoload'));
     * </code>
     *
     * @param string $class
     * @return string|false Class name on success; false on failure
     */
    public function autoload($class)
    {
        // Logic here
        // Return classname on success
        return $class;
    
        // Return false when unable to load
        return false;
    }
}