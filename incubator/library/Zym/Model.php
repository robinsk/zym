<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Model
{
    /**
     * Model registry
     *
     * @var Zend_Registry
     */
    protected static $_models = null;
    
    /**
     * Get a model
     *
     * @param string $modelName
     * @param array $params
     * @return mixed
     */
    public static function factory($modelName, array $params = array())
    {
        if (null === self::$_models) {
            self::$_models = new Zend_Registry();
        }
        
        $registry = self::$_models;
        
        if ($registry->offsetExists($modelName)) {
            return $registry->offsetGet($modelName);
        }
        
        Zend_Loader::loadClass($modelName);
        
        $reflectionClass = new ReflectionClass($modelName);
        
        if (false === $reflectionClass->implementsInterface('Zym_Model_Interface')) {
            /**
             * @see Zym_Model_Exception
             */
            require_once 'Zym/Model/Exception.php';
            
        	throw new Zym_Model_Exception('Model does not implement Zym_Model_Interface.');
        }
        
        if (empty($params)) {
            $modelInstance = $reflectionClass->newInstance();
        } else {
            $modelInstance = $reflectionClass->newInstanceArgs($params);
        }
        
        $registry->offsetSet($modelName, $modelInstance);
        
        return $modelInstance;
    }
}