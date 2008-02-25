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
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once('Zym/App/Resource/Abstract.php');

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once('Zend/Controller/Action/HelperBroker.php');

/**
 * @see Zend_View
 */
require_once('Zend/View.php');

/**
 * Setup view
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
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
                'encoding' => null,
                'escape'   => null,
        
                'path' => array(
                    'base' => array(),
        
                    'filter' => array(
                        'SpotSec' => array(
                            'prefix' => 'Zym_View_Filter',
                            'path'   => 'Zym/View/Filter'
                        )
                    ),
                    
                    'helper' => array(
                        'SpotSec' => array(
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
                    'basePath'               => null,
                    'scriptPath'             => null,
                    'scriptPathNoController' => null
                ),
                
                'flag' => array(
                    'neverController' => null,
                    'neverRender'     => null,
                    'noController'    => null,
                    'noRender'        => null
                )
            )
        )
    );

    /**
     * Setup View
     *
     */
    public function setup(Zend_Config $config)
    {
        // Get view renderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        
        // Use view from view renderer if possible
        if ($viewRenderer->view instanceof Zend_View_Abstract) {
            $view = $viewRenderer->view;
        } else {
            $view = new Zend_View();
            
            // Pass view renderer the view
            $viewRenderer->setView($view);
        }
        
        // Do setup
        $this->_setupView($view, $config->view);
        $this->_setupViewRenderer($viewRenderer, $config->view_renderer);
    }
    
    /**
     * Setup the view
     *
     * @param Zend_View_Abstract $view
     * @return Zym_App_Resource_View
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
        
        return $this;
    }
    
    /**
     * Setup view renderer
     *
     * @param Zend_Controller_Action_Helper_ViewRenderer $viewRenderer
     * @return Zym_App_Resource_View
     */
    protected function _setupViewRenderer(Zend_Controller_Action_Helper_ViewRenderer $viewRenderer, Zend_Config $viewRendererConfig) 
    {
        // Add base, filter, helper and script paths
        $specKeys = array('basePath', 'scriptPath', 'scriptPathNoController');
        foreach($specKeys as $key) {
            // No setting set, continue
            if ($viewRendererConfig->spec->{$key} === null) {
                continue;
            }
            
            $method = 'setView' . ucfirst($key) . 'Spec';
            call_user_func_array(array($viewRenderer, $method), array($viewRendererConfig->spec->{$key}));
        }
        
        // Add the run features
        $flagKeys = array('neverRender', 'neverController', 'noController', 'noRender');
        foreach($flagKeys as $key) {
            // No setting set, continue
            if ($viewRendererConfig->flag->{$key} === null) {
                continue;
            }
            
            $method = 'set' . ucfirst($key);
            call_user_func_array(array($viewRenderer, $method), array((bool) $viewRendererConfig->flag->{$key}));
        }
        
        // Set suffix
        if ($viewRendererConfig->suffix !== null) {
            $viewRendererConfig->setViewSuffix($viewRendererConfig->suffix);
        }
        
        return $this;
    }
}