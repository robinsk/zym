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
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once('Zym/App/Resource/Abstract.php');

/**
 * @see Zend_Loader
 */
require_once('Zend/Loader.php');

/**
 * Registers class autoloader
 * 
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App_Resource_Autoload extends Zym_App_Resource_Abstract
{
    /**
     * Config key for custom class
     *
     */
    const CONFIG_CLASS = 'class';
    
    /**
     * Priority
     *
     * @var integer
     */
    protected $_priority = self::PRIORITY_HIGH;
    
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        self::CONFIG_CLASS => 'Zend_Loader'
    );

    /**
     * Setup autoloader
     *
     */
    public function setup(Zend_Config $config)
    {
        // Use non-default autoload function?
        $class = $config->class;
        
        // Alow loading multiple loaders
        if ($class instanceof Zend_Config) {
            $classes = $class->toArray();
        } else {
            $classes = (array) $class;
        }
        
        // Register autoload
        foreach ($classes as $loader) {
            $loader = trim($loader);
            
            // Should we load autoloaders?
            if (!class_exists($loader)) {
                Zend_Loader::loadClass($loader);
            }

            Zend_Loader::registerAutoload($loader);
        }
    }
}