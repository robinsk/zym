<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_Error
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zym_Error_Handler_Interface
 */
require_once 'Zym/Error/Handler/Interface.php';

/**
 * Global Error Handler
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Error
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Error_Handler implements Zym_Error_Handler_Interface
{
    /**
     * Set a php error handler
     *
     * @param string  $class
     * @param integer $errorTypes Can be used to mask the triggering of the error_handler function just like the error_reporting ini setting controls which errors are shown.
     * @return mixed Returns a string containing the previously defined error handler (if any), or NULL on error. If the previous handler was a class method, this function will return an indexed array with the class and the method name.
     */
    public static function set($class = 'Zym_Error_Handler', $errorTypes = null)
    {
        // Load class
        Zend_Loader::loadClass($class);

        // Validate
        $methods = get_class_methods($class);
        if (!in_array('handle', (array) $methods)) {
            /**
             * @see Zym_Error_Exception
             */
            require_once 'Zym/Error/Exception.php';
            throw new Zym_Error_Exception(
                "{$class} does not implement Zym_Error_Handler_Interface" .
                ' or does not have a handle() method'
            );
        }

        // Assume it implements Zym_Error_Handler_Interface
        return set_error_handler(array($class, 'handle'), $errorTypes);
    }

    /**
     * Restore the php error handler
     *
     * @return boolean
     */
    public static function restore()
    {
        return restore_error_handler();
    }

    /**
     * PHP Error handler
     *
     * @param integer $code
     * @param string $message
     * @param string $file
     * @param integer $line
     * @param array $context
     */
    public static function handle($code, $message,
                                  $file = null, $line = null, array $context = array())
    {
        /**
         * @see Zym_Error
         */
        require_once 'Zym/Error.php';

        // Create error object
        $error = new Zym_Error($code, $message, $file, $line, $context);

        /**
         * @see Zym_Error_Stack
         */
        require_once 'Zym/Error/Stack.php';

        // Store error
        Zym_Error_Stack::getInstance()->push($error);
    }
}