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
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_Controller_Action_Helper_ViewRenderer
 */
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * Setup view
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_View extends Zym_App_Resource_Abstract
{   
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'view' => array(
                'class'    => 'Zym_View',
                'encoding' => null,
                'escape'   => null,
        
                'path' => array(
                    'base' => array(),
        
                    'filter' => array(
                        'Zym' => array(
                            'prefix' => 'Zym_View_Filter',
                            'path'   => 'Zym/View/Filter'
                        )
                    ),
                    
                    'helper' => array(
                        'Zym' => array(
                            'prefix' => 'Zym_View_Helper',
                            'path'   => 'Zym/View/Helper'
                        )
                    ),
                    
                    'script' => array()
                )
            ),
            
            'view_renderer' => array(
                'suffix' => null,
            
                'spec' => array(
                    'base_path'               => null,
                    'script_path'             => null,
                    'script_path_no_controller' => null
                ),

                'never_controller' => null,
                'never_render'     => null,
                'no_controller'    => null,
                'no_render'        => null
            )
        )
    );

    /**
     * Setup View
     *
     */
    public function setup(Zend_Config $config)
    {
        // Get view
        $view = $this->getView($config->view);
        
        $isUseViewRenderer = !Zend_Controller_Front::getInstance()->getParam('noViewRenderer');
        if ($isUseViewRenderer) {
            $viewRenderer = $this->getViewRenderer($config->view_renderer);
            $viewRenderer->setView($view);
            Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        }
    }
    
    /**
     * Get view
     *
     * @param Zend_Config $config
     * @return Zend_View_Interface
     */
    public function getView(Zend_Config $config)
    {
        if (!$view = $this->getCache('view')) {
            $isUseViewRenderer = !Zend_Controller_Front::getInstance()->getParam('noViewRenderer');
            $viewRenderer = ($isUseViewRenderer) 
                                ? Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')
                                : null;
                                
            // Use view from view renderer if possible
            if ($isUseViewRenderer && $viewRenderer->view instanceof Zend_View_Interface) {
                $view = $viewRenderer->view;
            } else {
                $viewClass = $config->get('class');
                Zend_Loader::loadClass($viewClass);
                
                $view = new $viewClass();
                
                // Validate object
                if (!$view instanceof Zend_View_Interface) {
                    /**
                     * @see Zym_App_Resource_View_Exception
                     */
                    require_once 'Zym/App/Resource/View/Exception.php';
                    throw new Zym_App_Resource_View_Exception(sprintf(
                        'View object must be an instance of Zend_View_Interface an object of %s', get_class($view)
                    ));
                }
            }
            
            // Setup
            $this->_setupView($view, $config);
            
            // Save
            $this->saveCache($view, 'view');
        }
        
        return $view;
    }
    
    /**
     * Get view renderer objec
     *
     * @param Zend_Config $config
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    public function getViewRenderer(Zend_Config $config)
    {
        // Use vr?
        $isUseViewRenderer = !Zend_Controller_Front::getInstance()->getParam('noViewRenderer');
        if ($isUseViewRenderer && !$viewRenderer = $this->getCache('viewRenderer')) {
            // Get view renderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            
            // Setup
            $this->_setupViewRenderer($viewRenderer, $config);
            
            // Save
            $this->saveCache($viewRenderer, 'viewRenderer');
        }
        
        return $viewRenderer;
    }
    
    /**
     * Setup the view
     *
     * @param Zend_View_Abstract $view
     * @param Zend_Config $config
     */
    protected function _setupView(Zend_View_Abstract $view, Zend_Config $viewConfig)
    {
        // Add base, filter, helper and script paths
        $keys = array('base', 'filter', 'helper', 'script');
        foreach($keys as $key) {
            foreach($viewConfig->path->{$key} as $objKey => $obj) {
                // Handle script paths
                if ($key == 'script') {
                    $view->addScriptPath(trim($obj));
                    continue;
                }
                
                // Handle base, filter and helper paths
                if ($obj instanceof Zend_Config || is_array($obj)) {
                    $path = $obj->path;
                    $prefix = $obj->prefix;
                } else {
                    // Allow setting namespaces using keys
                    if (empty($obj)) {
                        $obj = $objKey;
                    }
                    
                    $path = str_replace('_', '/', $obj);
                    $prefix = $obj;
                }
                
                $method = 'add' . ucfirst($key) . 'Path';
                call_user_func_array(array($view, $method), array(trim($path), $prefix));
            }
        }

        // Set encoding
        if ($viewConfig->encoding) {
            $view->setEncoding($viewConfig->encoding);
        }
        
        // Set escape
        if ($viewConfig->escape) {
            $view->setEscape($viewConfig->escape);
        }
    }
    
    /**
     * Setup view renderer
     *
     * @param Zend_Controller_Action_Helper_ViewRenderer $viewRenderer
     * @param Zend_Config $config
     */
    protected function _setupViewRenderer(Zend_Controller_Action_Helper_ViewRenderer $viewRenderer, Zend_Config $config) 
    {
        // Setup path spec
        $this->_viewRendererSpec($viewRenderer, $config);
        
        // Add the run features
        $this->_viewRendererFlags($viewRenderer, $config);
        
        // Set suffix
        $this->_viewRendererSuffix($viewRenderer, $config);
    }
    
    /**
     * Setup VR, base, filter, helper and script spec
     *
     * @param Zend_Controller_Action_Helper_ViewRenderer $viewRenderer
     * @param Zend_Config $config
     */
    protected function _viewRendererSpec(Zend_Controller_Action_Helper_ViewRenderer $viewRenderer, Zend_Config $config)
    {
        // Add base, filter, helper and script paths
        $specKeys = array('base_path'                 => 'setViewBasePathSpec', 
                          'script_path'               => 'setScriptPathSpec',
                          'script_path_no_controller' => 'setScriptPathNoController');
        foreach($specKeys as $method) {
            // No setting set, continue
            if ($config->spec->{$key} === null) {
                continue;
            }
            
            call_user_func_array(array($viewRenderer, $method), array($config->spec->{$key}));
        }
    }
    
    /**
     * Setup VR runtime flags
     *
     * @param Zend_Controller_Action_Helper_ViewRenderer $viewRenderer
     * @param Zend_Config $config
     */
    protected function _viewRendererFlags(Zend_Controller_Action_Helper_ViewRenderer $viewRenderer, Zend_Config $config)
    {
        $flagKeys = array('never_render'     => 'setNeverRender',
                          'never_controller' => 'setNeverController',
                          'no_controller'    => 'setNoController',
                          'no_render'        => 'setNoRender');
        foreach($flagKeys as $key => $method) {
            // No setting set, continue
            if ($config->{$key} === null) {
                continue;
            }
            
            call_user_func_array(array($viewRenderer, $method), array((bool) $config->{$key}));
        }
    }
    
    /**
     * Set VR view suffix
     *
     * @param Zend_Controller_Action_Helper_ViewRenderer $viewRenderer
     * @param Zend_Config $config
     */
    protected function _viewRendererSuffix(Zend_Controller_Action_Helper_ViewRenderer $viewRenderer, Zend_Config $config)
    {
        if ($config->suffix !== null) {
            $viewRenderer->setViewSuffix($config->suffix);
        }
    }
}