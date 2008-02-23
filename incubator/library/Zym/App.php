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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zym_App_Registry
 */
require_once 'Zym/App/Registry.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Bootstraping component
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App
{
    /**
     * Environment type development
     *
     */
    const ENV_DEVELOPMENT = 'development';
    
    /**
     * Environment type production
     *
     */
    const ENV_PRODUCTION  = 'production';
    
    /**
     * Environment type test
     *
     */
    const ENV_TEST        = 'test';
    
    /**
     * Environment type default
     */
    const ENV_DEFAULT     = 'default';
    
    /**
     * Cache directory
     *
     */
    const PATH_CACHE = 'cache';
    
    /**
     * Config directory
     *
     */
    const PATH_CONFIG = 'config';
    
    /**
     * Temp directory
     *
     */
    const PATH_TEMP = 'temp';
    const PATH_WEB = 'web';
    const PATH_MODULES = 'modules';
    const PATH_LIBRARIES = 'libraries';
    const PATH_LAYOUT = 'layout';
    const PATH_DATA = 'data';
    const PATH_SESSION = 'session';
    const PATH_TEST = 'test';
    const PATH_LOG = 'log';
    
    /**
     * Instance
     *
     * @var Zym_App
     */
    protected static $_instance;
    
    /**
     * Zend_Config
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        'home' => '../',
        
        'namespace' => array(
            'SpotSec' => 'Zym_App_Resource'
        ),
        
        'path' => array(
            self::PATH_CONFIG => 'config',
            self::PATH_TEMP => 'temp',
        ),
        
        'default_resource' => array(
            'disabled' => false,
            'config' => '%s.xml',
            'environment' => null,
            'namespace' => null,
            'priority' => null
        ),
        
        'resource' => array(),        
        'registry' => null
    );
    
    /**
     * Environment
     *
     * @var string
     */
    protected $_environment = self::ENV_PRODUCTION;
    
    /**
     * ExceptionHandler instance
     *
     * @var Zym_App_ExceptionHandler_Abstract
     */
    protected $_exceptionHandler;
    
    /**
     * Force default priority
     * 
     * @var integer
     */
    protected $_priority;
    
    /**
     * Internal Registry instance
     *
     * @var Zym_App_Registry
     */
    protected $_registry;
    
    /**
     * Array of resource instances
     *
     * @var array
     */
    protected $_resources = array();
    
    /**
     * Array of resource script paths
     *
     * @var array
     */
    protected $_scriptPaths = array(
        'SpotSec' => array(
            'dir'    => 'Zym/Application/',
            'prefix' => 'Zym_App_'
        )
    );
    
    /**
     * Construct
     * 
     * Protected to prevent instantiation
     */
    protected function __construct()
    {}
    
    /**
     * Clone
     * 
     * Enforce singleton
     */
    protected function __clone()
    {}
    
    /**
     * Get the application instance
     *
     * @return Zym_App
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    /**
     * Config
     *
     * @param Zend_Config|string
     * @return Zym_App
     */
    public function setConfig($config, $format = null)
    {
        $configObj = $this->_loadConfig($config, $this->getEnvironment(), $format);
        
        // Merge default config with user config
        $defaultConfig = $this->getDefaultConfig();
        $this->_config = $this->_mergeConfig($defaultConfig, $configObj);
                        
        return $this;
    }

    /**
     * Get Config
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        if (!$this->_config instanceof Zend_Config) {
            $this->setConfig($this->getDefaultConfig());
        }
        
        return $this->_config;
    }
    
    /**
     * Return a Zend_Config object populated with appropriate properties and
     * reasonable default values for this resource type.
     *
     * @return Zend_Config
     */
    public function getDefaultConfig()
    {
        return new Zend_Config($this->_defaultConfig);
    }
    
    /**
     * Get path
     *
     * @param string $key
     * @return Zend_Config
     */
    public function getPath($key)
    {
        $path = self::_normalizePath($this->getConfig()->home)
                . DIRECTORY_SEPARATOR
                . self::_normalizePath($this->getConfig()->path->{$key});
                
        return $path;
    }

    /**
     * Add a script path to the stack
     *
     * @param string $path
     * @param string $prefix
     * @return Zym_App
     */
    public function addResourcePath($id, $path, $prefix = 'Zym_App_Resource')
    {
        // Make sure it ends in a PATH_SEPARATOR
        if (substr($path, -1, 1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        // Make sure it ends in a PATH_SEPARATOR
        $prefix = rtrim($prefix, '_') . '_';

        $info['dir']    = $path;
        $info['prefix'] = $prefix;

        $this->_scriptPaths[strtolower($id)] = $info;
        return $this;
    }

    /**
     * Add repository of init scripts by prefix
     *
     * @param string $prefix
     * @return Zym_App
     */
    public function addResourcePrefix($id, $prefix)
    {
        $path = str_replace('_', DIRECTORY_SEPARATOR, $prefix);
        $this->addResourcePath($id, $path, $prefix);
        return $this;
    }
    
    /**
     * Append a resource script into the dispatch process
     *
     * @param Zym_App_Resource_Abstract $resource
     * @return Zym_App
     */
    public function appendResource(Zym_App_Resource_Abstract $resource, $name = null)
    {
        // Get the resource name (Zym_Foo -> Foo)
        if ($name === null) {
            $fullClassName = get_class($resource);
    
            if (strpos($fullClassName, '_') !== false) {
                $name = strrchr($fullClassName, '_');
                $name = ltrim($name, '_');
            } else {
                return $fullClassName;
            }
        }
        
        $this->_resources[$name] = $resource;
        
        return $this;
    }
    
    /**
     * Clear resource scripts
     *
     * @return Zym_App
     */
    public function clearResources()
    {
        $this->setResources(array());
        return $this;
    }

    /**
     * Clear and set init scripts
     *
     * @param array $scripts
     * @return Zym_App
     */
    public function setResources(array $scripts)
    {
        foreach ($scripts as $script) {
            if (!$script instanceof Zym_App_Resource_Abstract) {
                /**
                 * @see Zym_App_Exception
                 */
                require_once('Zym/Application/Exception.php');
                throw new Zym_App_Exception(
                    'The array of resource scripts provided has an invalid entry.'
                    . 'It should consist only of Zym_App_Resource_Abstract instances'
                );
            }
        }
        
        $this->_resources = $scripts;
        return $this;
    }

    /**
     * Get array of init script instances
     *
     * @return array
     */
    public function getResources()
    {
        return $this->_resources;
    }
    
    /**
     * Get script paths
     *
     * @return array
     */
    public function getResourcePaths()
    {
        return $this->_scriptPaths;
    }
    
    /**
     * Set the internal Application registry
     *
     * @param Zym_App_Registry $registry
     */
    public function setRegistry(Zym_App_Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * Get the internal Application registry
     *
     * @param string $index Shortcut to $this->getRegistry()->get($index)
     * @param mixed $class Assert the class type of the get
     * @return Zym_App_Registry
     */
    public function getRegistry($index = null, $class = null)
    {
        // Setup the registry
        if (!$this->_registry instanceof Zym_App_Registry) {
            $this->setRegistry(new Zym_App_Registry());
        }

        // Get shortcut
        if ($index !== null) {
            return $this->_registry->get($index, $class);
        }

        return $this->_registry;
    }
    
    /**
     * Set exception handler
     *
     * @param Zym_App_ExceptionHandler_Abstract $handler
     * @return Zym_App
     */
    public function setExceptionHandler(Zym_App_ExceptionHandler_Abstract $handler)
    {
        $handler->setApplication($this);
        $this->_exceptionHandler = $handler;
        return $this;
    }

    /**
     * Get the exeption handler
     *
     * @return Zym_App_ExceptionHandler_Abstract
     */
    public function getExceptionHandler()
    {
        if (!$this->_exceptionHandler instanceof Zym_App_ExceptionHandler_Abstract) {
            /**
             * @see Zym_App_ExceptionHandler_Standard
             */
            require_once('Zym/App/ExceptionHandler/Standard.php');
            $this->setExceptionHandler(new Zym_App_ExceptionHandler_Standard());
        }
        
        return $this->_exceptionHandler;
    }
    
    /**
     * Set environment
     *
     * @param string $env
     * @return Zym_App
     */
    public function setEnvironment($env)
    {
        $this->_environment = (string) $env;
        return $this;
    }
    
    /**
     * Get environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }
    
    /**
     * Dispatch the boot process
     *
     * @return void
     */
    public function dispatch()
    {
        try {
            // Get config
            $config = $this->getConfig();
            
            // Load namespaces
            $this->_parseNamespaces($config);

            // Load resources
            $this->_parseResources($config);
            
            // Sort dispatch order
            $scripts = $this->getResources();
            usort($scripts, array($this, '_dispatchSort'));
            $this->setResources($scripts);
            
            // Init script dispatch loop
            foreach ($scripts as $resource) {                
                // Dispatch
                $resource->dispatch();
            }
        } catch (Exception $e) {
            // Debug mode?
            if ($this->getConfig()->throw_exceptions) {
                throw $e;
            }
            
            // Let the exception handler deal with it
            $this->getExceptionHandler()->handle($e);
        }
    }
    
    /**
     * Run Application
     *
     * @return void
     */
    public static function run($config, $environment = null, $format = null)
    {
        $instance = self::getInstance();
        
        // Set environment
        if ($environment !== null) {
            $instance->setEnvironment($environment);
        }
        
        $instance->setConfig($config, $format)
                 ->dispatch();
    }

    /**
     * Parse the config for resources
     *
     * @param Zend_Config $config
     */
    protected function _parseResources(Zend_Config $config)
    {
        // Lets handle resources provided by config
        foreach ($config->resource as $name => $resource) {            
            // Get default resource config
            $defaultResConfig = $config->default_resource->toArray();
            
            // Convert placeholder to filename
            if (is_string($defaultResConfig['config'])) {
                $defaultResConfig['config'] = sprintf($defaultResConfig['config'], $name);
            }
            
            // Merge default config with actual config
            $resource = $this->_mergeConfig($defaultResConfig, $resource);

            // Run if enabled
            if ((isset($resource->disabled) && $resource->disabled === '') || $resource->disabled) {
                continue;
            }

            // Load resource config
            if (!$resource->config instanceof Zend_Config) {
                // Load a resource config from file specified
                $resConfigFile = $this->getPath(self::PATH_CONFIG) 
                                    . DIRECTORY_SEPARATOR 
                                    . $this->_normalizePath($resource->config);
                                    
                // Make sure it exists      
                if (file_exists($resConfigFile)) {
                    // Create config obj
                    $environment = $resource->environment ? $resource->environment : $this->getEnvironment();
                    $resConfig = $this->_loadConfig($resConfigFile, $environment);
                } else {
                    $resConfig = new Zend_Config(array());
                }
            } else {
                // Use the config provided
                $resConfig = $resource->config;
            }
            
            // Load resource object
            $namespace = $resource->namespace ? trim($resource->namespace) : null;
            $loadedScript = $this->_loadResource($name, $namespace);
            
            $script = new $loadedScript($resConfig, $environment);
            $script->setApp($this);
            
            // Set custom priority
           // if ($resource->priority) {
                
           // }
            
            // Make sure that it's a valid script
            if (!$script instanceof Zym_App_Resource_Abstract) {
                throw new Zym_App_Exception(
                    "Resource script \"$name\" is not an instance of Zym_App_Resource_Abstract"
                );
            }

            // Add into dispatch stack
            $this->appendResource($script, $name);
        }
    }
    
    /**
     * Load an resource script in LIFO order
     *
     * @param string $name
     * @param string $namespace Force to use a specific namespace
     * @return Zym_App_Resource_Abstract
     */
    protected function _loadResource($name, $namespace = null)
    {
        $name = ucfirst($name);
        $file = $name . '.php';
        $scriptPaths = $this->getResourcePaths();
        
        // Handle custom namespaces
        if ($namespace !== null) {
            $ns = strtolower($namespace);
            if (!isset($scriptPaths[$ns])) {
                throw new Zym_App_Exception(
                    "Cannot use namespace '$namespace' for  script '$name' because it does not exist"
                );
            }
            
            $scriptPaths = $scriptPaths[$ns];
        }
        
        foreach (array_reverse($scriptPaths) as $info) {
            $dir    = $info['dir'];
            $prefix = $info['prefix'];
            $class  = $prefix . $name;
            
            if (class_exists($class, false)) { 
                return $class;
            } else if (Zend_Loader::isReadable($dir . $file)) {
                include_once($dir . $file);
                
                if (class_exists($class, false)) {
                    return $class;
                }
            }
        }

        throw new Zym_App_Exception(
            'Application resource script by name "' . $name . '" not found.'
        );
    }
    
    
    protected function _loadResourceDefaultEnv($name, $environment, $namespace = null)
    {
        if ($namespace === null) {
            $scriptPaths = $this->getResourcePaths();
            
            if (!isset($scriptPaths[$namespace])) {
                throw new Zym_App_Exception(
                    "Cannot use namespace '$namespace' for  script '$name' because it does not exist"
                );
            }
            
            $path = explode('_', $scriptPaths[$namespace]);
            $namespace = $path[0];
        }
        $file = '../data' . "/$namespace/config/$name.xml";
        
        if (file_exists($file)) {
            $config = new Zend_Config_Xml($file, $environment);
        }
        return $config;
    }
    
    /**
     * Load Namespaces
     *
     */
    protected function _parseNamespaces(Zend_Config $config)
    {
        // Load namespaces
        foreach ($config->namespace as $id => $namespace) {
            if ($namespace instanceof Zend_Config) {
                $this->addResourcePath($id, $namespace->path, $namespace->prefix);
            } else {
                // Allow setting namespaces using keys
                if (empty($namespace)) {
                    $namespace = $id;
                }
                
                $this->addResourcePrefix($id, $namespace);
            }
        }
    }
    
    /**
     * Normalize init script name for lookups
     *
     * @param  string $name
     * @return string
     */
    protected static function _normalizeScriptName($name)
    {
        if (strpos($name, '_') !== false) {
            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        }

        return ucfirst($name);
    }
    
    /**
     * Normalize a path
     *
     * Trims and removes and leading /\
     * 
     * @param string $path
     * @return string
     */
    protected static function _normalizePath($path)
    {
        return rtrim(trim($path), '/\\');
    }
    
    /**
     * Merge two arrays recursively, overwriting keys of the same name name
     * in $array1 with the value in $array2.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function _arrayMergeRecursiveOverwrite($array1, $array2)
    {
        if (is_array($array1) && is_array($array2)) {
            foreach ($array2 as $key => $value) {
                if (isset($array1[$key])) {
                    $array1[$key] = $this->_arrayMergeRecursiveOverwrite($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        } else {
            $array1 = $array2;
        }
        
        return $array1;
    }
    
    /**
     * Sort function used by (@see run())
     *
     * @param Zym_App_Resource_Abstract $scriptA
     * @param Zym_App_Resource_Abstract $scriptB
     * @return integer
     */
    protected function _dispatchSort(Zym_App_Resource_Abstract $scriptA, Zym_App_Resource_Abstract $scriptB)
    {
        // Get priority
        $a = $scriptA->getPriority();
        $b = $scriptB->getPriority();
        
        // If priority is the same then umm... you know, put it above
        if ($a == $b) {
            return 1;
        }
        
        return ($a < $b) ? -1 : 1;
    }
    
    
    /**
     * A simple way to merge config sections and get a Zend_Config object
     *
     * @param Zend_Config|array $configA
     * @param Zend_Config|array $configB
     * @return Zend_Config
     */
    protected function _mergeConfig($configA, $configB)
    {
        /* We can't do this because there is no way to make sure it's a writable config obj
        // Use Zend_Config's merge
        if ($configA instanceof Zend_Config && $configB instanceof Zend_Config) {
            $configA->merge($configB);
            return $configA;
        }
        */
        
        // Convert to array
        $configA = ($configA instanceof Zend_Config) ? $configA->toArray() : (array) $configA;
        $configB = ($configB instanceof Zend_Config) ? $configB->toArray() : (array) $configB;
        
        $newConfig = $this->_arrayMergeRecursiveOverwrite($configA, $configB);
        return new Zend_Config($newConfig);
    }
    
    /**
     * Load config file
     *
     * @param string $config
     * @param string $format
     * @return Zend_Config
     */
    protected function _loadConfig($config, $environment, $format = null)
    {        
        // Find format
        if ($format === null) {
            $format = pathinfo($config, PATHINFO_EXTENSION);
        }
        
        $configClass = 'Zend_Config_' . ucfirst(strtolower($format));
        Zend_Loader::loadClass($configClass);
        
        $configObj = new $configClass($config, $environment);
        
        return $configObj;
    }
}