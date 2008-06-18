<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Navigation
 * @subpackage Zym_Navigation_Page
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Navigation_Page
 */
require_once 'Zym/Navigation/Page.php';

/**
 * Used to assemble URLs
 * 
 * @see Zend_Controller_Action_Helper_Url
 */
require_once 'Zend/Controller/Action/Helper/Url.php';

/**
 * Used to check if page is active
 * 
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * Zym_Navigation_Page_Mvc
 * 
 * Represents a page that is defined using module, controller, action, route
 * name and route params to assemble the href  
 * 
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Navigation
 * @subpackage Zym_Navigation_Page
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Navigation_Page_Mvc extends Zym_Navigation_Page
{
    /**
     * Action name
     *
     * @var string
     */
    protected $_action;
    
    /**
     * Controller name
     *
     * @var string
     */
    protected $_controller;
    
    /**
     * Module name
     *
     * @var string
     */
    protected $_module = 'default';
    
    /**
     * Params to use when assembling URL
     *
     * @see getHref()
     * @var array
     */
    protected $_params = array();
    
    /**
     * Route name
     * 
     * Used when assembling URL.
     *
     * @see getHref()
     * @var string
     */
    protected $_route = 'default';
    
    /**
     * Whether params should be reset when assembling URL
     *
     * @see getHref()
     * @var bool
     */
    protected $_resetParams = true;
    
    /**
     * Action helper for assembling URLs
     *
     * @see getHref()
     * @var Zend_Controller_Action_Helper_Url
     */
    protected static $_urlHelper;
    
    /**
     * Checks if the page is valid (has required properties)
     *
     * @return void
     * @throws Zym_Navigation_Page_InvalidException  if page is invalid
     */
    protected function _validate()
    {
        if (!isset($this->_controller)) {
            $msg = 'Page controller is not set';
        } elseif (!isset($this->_action)) {
            $msg = 'Page action is not set';
        }
        
        if (isset($msg)) {
            require_once 'Zym/Navigation/Page/InvalidException.php';
            throw new Zym_Navigation_Page_InvalidException($msg);
        } else {
            parent::_validate();
        }
    }
    
    // Accessors:
    
    /**
     * Returns bool value indicating whether page is active or not
     * 
     * If not set active, this method will compare the page the request object.
     *
     * @return bool
     */
    public function isActive()
    {
        if (!$this->_active) {  
            $reqParams = Zend_Controller_Front::getInstance()
                            ->getRequest()->getParams();
            
            $myParams = array_merge($this->_params, array(
                'module'     => $this->_module,
                'controller' => $this->_controller,
                'action'     => $this->_action
            ));
            
            // TODO: verify this
            if (count(array_intersect_assoc($reqParams, $myParams)) ==
                count($myParams)) {
                $this->_active = true;
            }
        }
        
        return $this->_active;
    }
    
    /**
     * Returns href for this page
     *
     * @return string
     */
    public function getHref()
    {
        if (null === self::$_urlHelper) {
            self::$_urlHelper =
                Zend_Controller_Action_HelperBroker::getStaticHelper('url');
        }
        
        return self::$_urlHelper->url(array_merge($this->_params, array(
            'module' => $this->_module,
            'controller' => $this->_controller,
            'action' => $this->_action
        )), $this->_route, $this->_resetParams);
    }
    
    /**
     * Sets action name for this page
     *
     * @param  string $action
     * @throws InvalidArgumentException  if invalid $action is given
     */
    public function setAction($action)
    {
        if (!is_string($action) || empty($action)) {
            $msg = '$action must be a non-empty string';
            throw new InvalidArgumentException($msg);
        }
        
        $this->_action = $action;
    }
    
    /**
     * Returns action name for this page
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * Sets controller name for this page
     *
     * @param  string $controller
     * @throws InvalidArgumentException  if invalid $controller is given
     */
    public function setController($controller)
    {
        if (!is_string($controller) || empty($controller)) {
            $msg = '$controller must be a non-empty string';
            throw new InvalidArgumentException($msg);
        }
        
        $this->_controller = $controller;
    }
    
    /**
     * Returns controller name for this page
     *
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }
    
    /**
     * Sets module name for this page
     *
     * @param  string $module
     * @throws InvalidArgumentException  if invalid $module is given
     */
    public function setModule($module)
    {
        if (!is_string($module) || empty($module)) {
            $msg = '$module must be a non-empty string';
            throw new InvalidArgumentException($msg);
        }
        
        $this->_module = $module;
    }
    
    /**
     * Returns module name for this page
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }
    
    /**
     * Sets params for this page
     * 
     * Params are used when assembling URL.
     *
     * @param array|null $params  [optional] if null is given, params will
     *                            be cleared
     * @return Zym_Navigation_Page_Abstract
     */
    public function setParams(array $params = null)
    {
        if (null === $params) {
            $this->_params = array();
        } else {
            // TODO: do this more intelligently?
            $this->_params = $params;
        }
        
        return $this;
    }
    
    /**
     * Returns params for this page
     * 
     * Params are used when assembling URL.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
    
    /**
     * Sets route name for this page
     *
     * @param  string $route
     * @throws InvalidArgumentException  if invalid $route is given
     */
    public function setRoute($route)
    {
        if (!is_string($route) || empty($route)) {
            $msg = '$route must be a non-empty string';
            throw new InvalidArgumentException($msg);
        }
        
        $this->_route = $route;
    }
    
    /**
     * Returns route name for this page
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->_route;
    }
    
    /**
     * Sets whether params should be reset when assembling URL
     *
     * @param bool $resetParams
     */
    public function setResetParams($resetParams)
    {
        $this->_resetParams = (bool)$resetParams;
    }
    
    /**
     * Returns whether params should be reset when assembling URL
     *
     * @return bool
     */
    public function isResetParams()
    {
        return $this->_resetParams;
    }
    
    /**
     * Sets action helper for assembling URLs
     *
     * @param Zend_Controller_Action_Helper_Url $uh
     */
    public static function setUrlHelper(Zend_Controller_Action_Helper_Url $uh)
    {
        self::$_urlHelper = $uh;
    }
    
    // Public methods:
    
    /**
     * Returns an array representation of the page
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), array(
            'action'       => $this->getAction(),
            'controller'   => $this->getController(),
            'module'       => $this->getModule(),
            'params'       => $this->getParams(),
            'route'        => $this->getRoute(),
            'reset_params' => $this->isResetParams()
        )); 
    }
}