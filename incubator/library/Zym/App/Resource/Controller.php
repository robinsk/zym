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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

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
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
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
                'directory'  => array(
                    'modules' // relative to PATH_APP
                ),

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

            'helper_broker' =>array(
                'paths' => array(
                    array(
                        'path'   => 'Zym/Controller/Action/Helper',
                        'prefix' => 'Zym_Controller_Action_Helper'
                    )
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

            'request'    => 'Zym_Controller_Request_Http',
            'response'   => 'Zym_Controller_Response_Http',
            'dispatcher' => null,
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
        $controller = call_user_func_array(array($config->get('class'), 'getInstance'), array());
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
        $this->_setThrowExceptions($config->get('throw_exceptions'));

        // Handle router, request, response
        $this->_setCustomClasses($config);

        // Set baseUrl
        $this->_setBaseUrl($config->get('base_url'));

        // Add controller and module directories
        $this->_addControllerAndModuleDirectories($config);

        // Set module custom controller name
        $this->_setModuleControllerDirectoryName($config->get('module')->get('controller_name'));

        // Handle defaults (module, controller and action names)
        $this->_setDefaultController($config->get('default'));

        // Set controller params
        $this->_setParams($config->get('params'));

        // Handle controller plugins
        $this->_loadPlugins($config->get('plugin'));

        // Set helperBroker paths
        $this->_setHelperBrokerPaths($config->get('helper_broker')->get('paths'));
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
            'module'     => $config->get('module')    ->get('directory'),
            'controller' => $config->get('controller')->get('directory')
        );

        foreach ($moduleAndControllerMap as $name => $dirObj) {
            $dirArray = ($dirObj instanceof Zend_Config) ? $dirObj->toArray() : (array) $dirObj;

            // Add a module or a controller directory
            foreach ($dirArray as $key => $dir) {
                // addControllerDirectory(), addModuleDirectory()
                $addDirectoryFunc = 'add' . ucfirst($name) . 'Directory';
                $dir              = $this->getApp()->getPath(Zym_App::PATH_APP, $dir);

                $this->getFrontController()->$addDirectoryFunc($dir, $key);
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
                $frontController->$setDefaultFunc($value);
            }
        }
    }

    /**
     * Set invokeArgs
     *
     * @param Zend_Config $params
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
            'router'     => $config->get('router'),
            'request'    => $config->get('request'),
            'response'   => $config->get('response'),
            'dispatcher' => $config->get('dispatcher')
        );

        foreach ($customClassMap as $key => $value) {
            if (!empty($value)) {
                // Load class
                if (is_string($value)) {
                    Zend_Loader::loadClass($value);
                    $value = new $value();
                }

                $func = 'set' . ucfirst($key);
                $this->getFrontController()->$func($value);
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
                $index = (int) $name->get('plugin_index');
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

    /**
     * Set helper broker paths
     *
     * @param Zend_Config $paths
     */
    protected function _setHelperBrokerPaths(Zend_Config $paths)
    {
        foreach ($paths as $pathConfig) {
            $path   = isset($pathConfig->path)  ? $pathConfig->get('path')  : null;
            $prefix = isset($pathConfig->prefix) ? $pathConfig->get('prefix') : null;

            if ($path === null) {
                continue;
            }

            if ($prefix === null) {
                Zend_Controller_Action_HelperBroker::addPath($path);
            } else {
                Zend_Controller_Action_HelperBroker::addPath($path, $prefix);
            }
        }
    }
}