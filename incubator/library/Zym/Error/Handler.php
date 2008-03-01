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
     * @param string $class
     * @param integer $errorTypes
     */
    public static function set($class = 'Zym_Error_Handler', $errorTypes = null)
    {
        // Load class
        Zend_Loader::loadClass($class);
        
        // Validate
        if ($class instanceof Zym_Error_Handler_Interface) {
            /**
             * @see Zym_Error_Exception
             */
            require_once 'Zym/Error/Exception.php';
            throw new Zym_Error_Exception(
                "{$class} is not an instance of Zym_Error_Handler_Interface"
            );
        }
        
        set_error_handler(array($class, 'handle'), $errorTypes);
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