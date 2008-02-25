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
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Abstract controller setup process
 * 
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App_Resource_Controller extends Zym_App_Resource_Abstract
{
    /**
     * Front Controller
     *
     * @var Zend_Controller_Front
     */
    protected $_frontController;
    
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEVELOPMENT => array(
            'throw_exceptions' => true
        ),
    
        Zym_App::ENV_DEFAULT     => array(
            'class' => 'Zend_Controller_Front',
            'throw_exceptions' => false,
        
            'module' => array(
                'directory'       => array(),
                'controller_name' => null
            ),
            
            'controller' => array(
                'directory' => array()
            ),
            
            'base_url' => null,
            
            'plugin' => array(
                'Zym_App_Resource_Controller_Plugin_ErrorHandler' => array(
                    'plugin_index' => 105
                )
            ),
        
            'default' => array(
                'action'          => null,
                'controller_name' => null,
                'module'          => null
            ),
            
            'params' => array(
                'prefixDefaultModule' => true
            ),
            
            'request'    => null,
            'response'   => 'Zym_Controller_Response_Http',
            'dispatcher' => 'Zym_Controller_Dispatcher_Standard',
            'router'     => null
        )
    );
    
    /**
     * PreSetup
     *
     * @param Zend_Config $config
     */
    public function preSetup(Zend_Config $config)
    {
        // Load front controller
        $controller = call_user_func_array(array($config->class, 'getInstance'), array());
        $this->setFrontController($controller);
    }
    
    /**
     * Setup
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {        
        // Get front controller
        $frontController = $this->getFrontController();
        
        // Throw dispatch exceptions
        $this->_setThrowExceptions($config->throw_exceptions);
        
        // Set baseUrl
        $this->_setBaseUrl($config->base_url);
        
        // Add controller and module directories    
        $this->_addControllerAndModuleDirectories($config);
        
        // Set module custom controller name
        $this->_setModuleControllerDirectoryName($config->module->controller_name);
        
        // Handle defaults (module, controller and action names)
        $this->_setDefaultController($config->default);
        
        // Set controller params
        $this->_setParams($config->params);
        
        // Handle router, request, response
        $this->_setCustomClasses($config);
        
        // Handle controller plugins
        $this->_loadPlugins($config->plugin);
    }
    
    /**
     * Get front controller
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }
    
    /**
     * Set front controller
     *
     * @param Zend_Controller_Front $controller
     * @return Zym_App_Resource_Controller
     */
    public function setFrontController(Zend_Controller_Front $controller)
    {
        $this->_frontController = $controller;
        return $this;
    }
    
    /**
     * Set throwing of exceptions by the FC
     *
     * @param boolean|integer $throw
     */
    protected function _setThrowExceptions($throw) 
    {
        // Throw dispatch exceptions
        $this->getFrontController()->throwExceptions((bool) $throw);
    }
    
    /**
     * Set baseUrl
     * 
     * @todo Discuss setting from environment vars
     * @param string $baseUrl
     */
    protected function _setBaseUrl($baseUrl)
    {
        if (!empty($baseUrl)) {
            $this->getFrontController()->setBaseUrl($baseUrl);
        }
    }
    
    /**
     * Add FC controller and module directories
     *
     * @param Zend_Config $config
     */
    protected function _addControllerAndModuleDirectories(Zend_Config $config)
    {
        $moduleAndControllerMap = array(
            'module' => $config->module->directory,
            'controller' => $config->controller->directory
        );
        
        foreach ($moduleAndControllerMap as $name => $dirObj) {
            $dirArray = ($dirObj instanceof Zend_Config) ? $dirObj->toArray() : (array) $dirObj;
            
            // Add a module or a controller directory
            foreach ($dirArray as $key => $dir) {
                // addControllerDirectory(), addModuleDirectory()
                $addDirectoryFunc = 'add' . ucfirst($name) . 'Directory';
                call_user_func_array(array($this->getFrontController(), $addDirectoryFunc), array($dir, $key));
            }
        }
    }
    
    /**
     * Set module controller directory name
     *
     * @param string $name
     */
    protected function _setModuleControllerDirectoryName($name)
    {
        if (!empty($name)) {
            $this->getFrontController()->setModuleControllerDirectoryName($name);
        }
    }
    
    /**
     * Set default controller/module names
     *
     * @param Zend_Config $config
     */
    protected function _setDefaultController(Zend_Config $config)
    {
        $frontController = $this->getFrontController();
        
        foreach ($config as $key => $value) {
            if (!empty($value)) {
                // Convert to camelCase (setDefaultController, setDefaultModule...)
                $setDefaultFunc = 'setDefault' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                call_user_func_array(array($frontController, $setDefaultFunc), array($value));
            }
        }
    }
    
    /**
     * Set invokeArgs
     *
     * @param unknown_type $params
     */
    protected function _setParams($params)
    {
        if ($params instanceof Zend_Config) {
            $this->getFrontController()->setParams($params->toArray());
        }
    }
    
    /**
     * Set classes such as router, request, dispatcher etc..
     *
     * @param Zend_Config $config
     */
    protected function _setCustomClasses(Zend_Config $config)
    {
        $customClassMap = array(
            'router' => $config->router, 
            'request' => $config->request, 
            'response' => $config->response,
            'dispatcher' => $config->dispatcher
        );
        
        foreach ($customClassMap as $key => $value) {
            if (!empty($value)) {
                // Load class
                if (is_string($value)) {
                    Zend_Loader::loadClass($value);
                    $value = new $value();
                }
                
                $func = 'set' . ucfirst($key);
                call_user_func_array(array($this->getFrontController(), $func), array($value));
            }
        }
    }
    
    /**
     * Load controller plugins
     * 
     * Zend_Config type enforcement is not a bug, it was left in in order
     * to prevent <plugin /> overriding any defaults
     * 
     * <plugin>
     *     <Zym_Controller_Plugin_ErrorHandler />
     * </plugin>
     *
     * @param Zend_Config $config
     */
    protected function _loadPlugins(Zend_Config $config) 
    {        
        $fc = $this->getFrontController();
        foreach ($config as $key => $name) {
            // Handle index
            $index = null;
            if ($name instanceof Zend_Config && isset($name->plugin_index)) {
                $index = (int) $name->plugin_index;
            }
            
            // Load class
            Zend_Loader::loadClass($key);
            $pluginInterface = new $key();
            
            // Talk about nested if... *sigh*
            if ($pluginInterface instanceof Zym_App_Resource_Controller_Plugin_Interface) {    
                if ($name instanceof Zend_Config) {
                    $pluginConfig = $name;
                } else {
                    $pluginConfig = null;
                }
                
                $plugin = $pluginInterface->getPlugin($pluginConfig);
            } else if ($pluginInterface instanceof Zend_Controller_Plugin_Abstract) {
                $plugin = $pluginInterface;
            } else {
                throw new Zym_App_Resource_Controller_Exception(
                    'Controller plugin "' . get_class($pluginInterface) 
                        . '" is not an instance of Zym_App_Resource_Controller_Interface or '
                        . 'Zend_Controller_Plugin_Abstract'
                );
            }
            
            $fc->registerPlugin($plugin, $index);
        }
    }
}