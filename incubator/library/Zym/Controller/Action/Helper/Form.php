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
 * @see Zend_Loader_PluginLoader_Interface
 */
require_once 'Zend/Loader/PluginLoader/Interface.php';

/**
 * Form Helper
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_Form extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Word delimiters
     * 
     * @see _translateSpec()
     * @var array
     */
    protected $_delimiters;
    
    /**
     * Inflector
     * 
     * @var Zend_Filter_Inflector
     */
    protected $_inflector;

    /**
     * Inflector target
     * 
     * @var string
     */
    protected $_inflectorTarget = '';

    /**
     * Current module directory
     * 
     * @var string
     */
    protected $_moduleDir = '';
    
    /**
     * Spec for forms path
     * 
     * Valid specs:
     *  - :moduleDir
     *  - :module
     *  - :controller
     *  - :action
     *
     * @var string
     */
    protected $_pathSpec = ':moduleDir/forms/:action.php';
            
    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->init();
    }
    
    /**
     * Api for extending classes
     *
     * @return void
     */
    public function init()
    {}
    
    /**
     * Set path spec
     * 
     * {@see $this->_pathSpec} for spec format
     * 
     * @param string $spec
     * @return Zym_Controller_Action_Helper_Form
     */
    public function setPathSpec($spec)
    {
        $this->_pathSpec = (string) $spec;
        
        return $this;
    }
    
    /**
     * Get path spec
     *
     * @return string
     */
    public function getPathSpec()
    {
        return $this->_pathSpec;
    }
    
    /**
     * Get a form object
     * 
     * @param string            $name Form name
     * @param array|Zend_Config $options Form options
     */
    public function create($name = null, $options = null)
    {
        return $this->createBySpec($name, array(), $options);
    }
    
    /**
     * Get a form object by spec
     * 
     * @param string            $name     Form name
     * @param array             $specVars
     * @param array|Zend_Config $options  Form options
     * 
     * @return Zend_Form
     */
    public function createBySpec($name = null, array $specVars = array(), $options = null)
    {
        $class = $this->load($name, $specVars);
        $form  = new $class($options);
        
        return $form;
    }
    
    /**
     * Create form
     *
     * @param string $name
     * @param string $options
     * @return Zend_Form
     */
    public function direct($name, $options = null)
    {
        return $this->create($name, $options);
    }
    
    /**
     * Load a form class
     *
     * @param string $name
     * @param array  $specVars
     * 
     * @return void
     */
    public function load($name = null, array $specVars = array())
    {
        // Get a form if none specified
        if ($name === null) { 
            $request    = $this->getRequest();
            $dispatcher = $this->getFrontController()->getDispatcher();
            $controller = substr($dispatcher->formatControllerName($request->getControllerName()), 0, -10);
            $action     = substr($dispatcher->formatActionName($request->getActionName()), 0, -6);
            
            $name =  $controller . ucfirst($action);
        }
        
        $actionSpecVars           = array();
        $specVars['action']       = $name;
        $actionSpecVars['action'] = $name;
        
        // Create class name
        $this->_setInflectorTarget(':action');
        $action    = $this->_translateSpec($actionSpecVars);
        $className = $this->_generateDefaultPrefix() . '_' . str_replace('/', '_', $action);
        
        // Create file path
        if (!class_exists($className, false)) {
            $this->_setInflectorTarget($this->getPathSpec());
            $path = $this->_translateSpec($specVars);
            include_once $path;
        }
        
        if (!class_exists($className, false)) {
            /**
             * @see Zym_Controller_Action_Helper_Form_Exception_FormNotFound
             */
            require_once 'Zym/Controller/Action/Helper/Form/Exception/FormNotFound.php';
            throw new Zym_Controller_Action_Helper_Form_Exception_FormNotFound($className, $path);
        }
        
        return $className;
    }
    
    /**
     * Get inflector
     * 
     * @return Zend_Filter_Inflector
     */
    public function getInflector()
    {
        if (null === $this->_inflector) {
            /**
             * @see Zend_Filter_Inflector
             */
            require_once 'Zend/Filter/Inflector.php';
            
            /**
             * @see Zend_Filter_Word_UnderscoreToSeparator
             */
            require_once 'Zend/Filter/Word/UnderscoreToSeparator.php';
            
            /**
             * @see Zend_Filter_Word_SeparatorToCamelCase
             */
            require_once 'Zend/Filter/Word/SeparatorToCamelCase.php';
            
            $this->_inflector = new Zend_Filter_Inflector();
            // moduleDir must be specified before the less specific 'module'
            $this->_inflector->setStaticRuleReference('moduleDir', $this->_moduleDir) 
                             ->addRules(array(
                                 ':module'     => array(
                                    new Zend_Filter_Word_SeparatorToCamelCase('.'),
                                    new Zend_Filter_Word_SeparatorToCamelCase('-')
                                 ),
                             
                                 ':controller' => array(
                                    new Zend_Filter_Word_UnderscoreToSeparator('/'),
                                    new Zend_Filter_Word_SeparatorToCamelCase('.'),
                                    new Zend_Filter_Word_SeparatorToCamelCase('-')
                                 ),
                                 
                                 ':action'     => array(
                                    new Zend_Filter_Word_UnderscoreToSeparator('/'),
                                    new Zend_Filter_Word_SeparatorToCamelCase('.'),
                                    new Zend_Filter_Word_SeparatorToCamelCase('-')
                                ),
                             ))
                             ->setTargetReference($this->_inflectorTarget);
        }

        // Ensure that module directory is current
        $this->_getModuleDirectory();

        return $this->_inflector;
    }

    /**
     * Set inflector
     * 
     * @param  Zend_Filter_Inflector $inflector 
     * @param  boolean               $reference Whether the moduleDir, target, and suffix should be set as references to ViewRenderer properties
     * @return Zend_Controller_Action_Helper_ViewRenderer Provides a fluent interface
     */
    public function setInflector(Zend_Filter_Inflector $inflector, $reference = false)
    {
        $this->_inflector = $inflector;
        
        if ($reference) {
            $this->_inflector->setStaticRuleReference('moduleDir', $this->_moduleDir)
                             ->setTargetReference($this->_inflectorTarget);
        }
        
        return $this;
    }

    /**
     * Set inflector target
     * 
     * @param  string $target 
     * @return void
     */
    protected function _setInflectorTarget($target)
    {
        $this->_inflectorTarget = (string) $target;
    }
    
    /**
     * Generate a class prefix for form classes
     *
     * @return string
     */
    protected function _generateDefaultPrefix()
    {
        $actionController = $this->getActionController();
        if ((null === $actionController) || !strstr(get_class($actionController), '_')) {
            $prefix = 'Zend_Form';
        } else {
            $class = get_class($actionController);
            $prefix = substr($class, 0, strpos($class, '_')) . '_Form';
        }

        return $prefix;
    }
    
    /**
     * Get current module name
     * 
     * @return string
     */
    protected function _getModule()
    {
        $request = $this->getRequest();
        $module  = $request->getModuleName();
        
        if (null === $module) {
            $module = $this->getFrontController()->getDispatcher()->getDefaultModule();
        }

        return $module;
    }
    
    /**
     * Get module directory
     *
     * @throws Zend_Controller_Action_Exception
     * @return string
     */
    protected function _getModuleDirectory()
    {
        $module    = $this->_getModule();
        $moduleDir = $this->getFrontController()->getControllerDirectory($module);
        if ((null === $moduleDir) || is_array($moduleDir)) {
            /**
             * @see Zym_Controller_Action_Helper_Form_Exception_ModuleNotFound
             */
            require_once 'Zym/Controller/Action/Helper/Form/Exception/ModuleNotFound.php';
            throw new Zym_Controller_Action_Helper_Form_Exception_ModuleNotFound();
        }
        
        $this->_moduleDir = dirname($moduleDir);
        
        return $this->_moduleDir;
    }
    
    /**
     * Inflect based on provided vars
     *
     * Allowed variables are:
     * - :moduleDir - current module directory
     * - :module - current module name
     * - :controller - current controller name
     * - :action - current action name
     *
     * @param  array $vars
     * @return string
     */
    protected function _translateSpec(array $vars = array())
    {
        $inflector  = $this->getInflector();
        $request    = $this->getRequest();
        $dispatcher = $this->getFrontController()->getDispatcher();
        $module     = $dispatcher->formatModuleName($request->getModuleName());
        $controller = substr($dispatcher->formatControllerName($request->getControllerName()), 0, -10);
        $action     = substr($dispatcher->formatActionName($request->getActionName()), 0, -6);
        $name       = $action;
        
        $params     = compact('module', 'controller', 'action');
        foreach ($vars as $key => $value) {
            switch ($key) {
                case 'module'    :
                case 'controller':
                case 'action'    :
                case 'moduleDir' :
                    $params[$key] = (string) $value;
                    break;
                    
                default:
                    break;
            }
        }

        if (isset($moduleDir)) {
            $origModuleDir = $this->_getModuleDir();
            $this->_setModuleDir($params['moduleDir']);
        }

        $filtered = $inflector->filter($params);

        if (isset($moduleDir)) {
            $this->_setModuleDir($origModuleDir);
        }

        return $filtered;
    }
}