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
 * @package Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Session_Namespace
 */
require_once 'Zend/Session/Namespace.php';

/**
 * Session Namespace helper
 * 
 * Simplifies creation of a session namespace for a certain controller,
 * module and or action.
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_Session extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Session Namespace
     *
     * @var array Array of Zend_Session_Namespace
     */
    protected $_namespace = array();
    
    /**
     * Namespace class
     *
     * @var string
     */
    protected $_namespaceClass = 'Zend_Session_Namespace';
    
    /**
     * Namespaces prefix
     *
     * @var string
     */
    protected $_prefix = 'Zend_Controller_Action | ';

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return Zym_Controller_Action_Helper_Session
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        
        return $this;
    }
    
    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
    
    /**
     * Set namespace class
     *
     * @param string $class
     * @return Zym_Controller_Action_Helper_Session
     */
    public function setNamespaceClass($class)
    {
        $this->_namespaceClass = (string) $class;
        
        return $this;
    }
    
    /**
     * Get the namespace class
     *
     * @return string
     */
    public function getNamespaceClass()
    {
        return $this->_namespaceClass;
    }
    
    /**
     * Set a namespace
     *
     * @todo Remove hack when Zend_Session_Namespace::getNamespace() exists
     * 
     * @param Zend_Session_Namespace $namespace
     * @return Zym_Controller_Action_Helper_Session
     */
    public function setNamespace(Zend_Session_Namespace $namespace)
    {
        if (!method_exists($namespace, 'getNamespace')) {
            // Hack!!!
            $namespaceArray = (array) $namespace;
            $name           = $namespaceArray['\0*\0_namespace'];
        } else {
            $name = $namespace->getNamespace();
        }
        
        $this->_namespace[$name] = $namespace;
        
        return $this;
    }
    
    /**
     * Get a namespace
     *
     * @param string $name
     * @param boolean $singleInstance
     * @return Zend_Session_Namespace
     */
    public function getNamespace($name = null, $singleInstance = null)
    {
        if (!$this->hasNamespace($name)) {
            $class = $this->getNamespaceClass();
            Zend_Loader::loadClass($class);
            
            $namespace = new $class($name, $singleInstance);
            
            // Do not store instance if we only allow one
            if ($singleInstance === true) {
                return $namespace; 
            }
            
            $this->setNamespace($namespace);
        }
        
        return $this->_namespace[$name];
    }
    
    /**
     * Check if a namespace instance exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasNamespace($name)
    {
        return isset($this->_namespace[$name]);
    }
    
    /**
     * Get the session namespace specific for the current module
     *
     * @param boolean $singleInstance
     * @param string  $module
     * 
     * @return Zend_Session_Namespace
     */
    public function getModuleNamespace($singleInstance = null, $module = null)
    {
        $name = $this->getModuleNamespaceName($module);
        
        return $this->getNamespace($name, $singleInstance);
    }
    
    /**
     * Get the session namespce specific for the controller
     *
     * @param boolean $singleInstance
     * @param string  $controller
     * @param string  $module
     * 
     * @return Zend_Session_Namespace
     */
    public function getControllerNamespace($singleInstance = null, $controller = null, $module = null)
    {
        $name = $this->getControllerNamespaceName($controller, $module);
        
        return $this->getNamespace($name, $singleInstance);
    }
    
    /**
     * Get the session namespace for an action
     *
     * @param boolean $singleInstance
     * @param string  $action
     * @param string  $controller
     * @param string  $module
     * 
     * @return Zend_Session_Namespace
     */
    public function getActionNamespace($singleInstance = null, $action = null, $controller = null, $module = null)
    {
        $name = $this->getActionNamespaceName($action, $controller, $module);
        
        return $this->getNamespace($name, $singleInstance);
    }
    
    /**
     * Get module namespace name
     *
     * @param string $module
     * @return string
     */
    public function getModuleNamespaceName($module = null)
    {
        if ($module !== null) {
            $request = $this->getRequest();
            $module  = $request->getModuleName(); 
        }
        
        return $this->getPrefix() . $module;
    }
    
    /**
     * Get controller namespace name
     *
     * @param string $controller
     * @param string $module
     * @return string
     */
    public function getControllerNamespaceName($controller = null, $module = null)
    {
        $request = $this->getRequest();
        
        if ($controller !== null) {
            $controller = $request->getControllerName();
        }
        
        return $this->getModuleNamespaceName($module) . '_' . $controller;
    }
    
    /**
     * Get action namespace name
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return string
     */
    public function getActionNamespaceName($action = null, $controller = null, $module = null)
    {
        $request = $this->getRequest();
        
        if ($action !== null) {
            $action = $request->getActionName();
        }
        
        return $this->getControllerNamespaceName($controller, $module) . '_' . $action;
    }
    
    /**
     * Get the session namespce specific for the controller
     *
     * @param boolean $singleInstance
     * @param string  $controller
     * @param string  $module
     * 
     * @return Zend_Session_Namespace
     */
    public function direct($singleInstance = null, $controller = null, $module = null)
    {
        return $this->getControllerNamespace($singleInstance, $controller, $module);
    }
}