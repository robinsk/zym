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
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Controller_Action_Abstract
 */
require_once 'Zym/Controller/Action/Abstract.php';

/**
 * @see Zym_Controller_Plugin_ErrorHandler
 */
require_once 'Zym/Controller/Plugin/ErrorHandler.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_Controller_Action_Error extends Zym_Controller_Action_Abstract 
{
    /**
     * Error handler action
     *
     */
    const ACTION     = 'action';
    
    /**
     * Error handler controller
     *
     */
    const CONTROLLER = 'controller';
    
    /**
     * Error handler module
     *
     */
    const MODULE     = 'module';
    
    /**
     * Error handler params
     *
     */
    const PARAMS     = 'params';
    
    /**
     * Exception Object
     *
     * $_error->type (Zend_Controller_Plugin_ErrorHandler constants)
     * $_error->exception (Exception object)
     *
     * @var Zym_Controller_Plugin_ErrorHandler_Data
     */
    protected $_error;
    
    /**
     * Error handler map
     *
     * @var array
     */
    protected $_errorHandlers = array();
    
    /**
     * Class constructor
     *
     * The request and response objects should be registered with the
     * controller, as should be any additional optional arguments; these will be
     * available via {@link getRequest()}, {@link getResponse()}, and
     * {@link getInvokeArgs()}, respectively.
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $response,
     * $invokeArgs).
     *
     * After the request, response, and invokeArgs are set, the
     * {@link $_helper helper broker} is initialized.
     *
     * Finally, {@link init()} is called as the final action of
     * instantiation, and may be safely overridden to perform initialization
     * tasks; as a general rule, override {@link init()} instead of the
     * constructor to customize an action controller's instantiation.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        
        // Set error
        $error = $request->getParam(Zym_Controller_Plugin_ErrorHandler::ERROR_PARAM);
        if ($error instanceof Zym_Controller_Plugin_ErrorHandler_Data) {
            $this->setError($error);
        }
    }
    
    /**
     * Error handler
     * 
     * This is the entrance to this controller used by the ErrorHandler
     * controller plugin (@see Zend_Controller_ErrorHandler)
     * 
     * This action cannot be called directly, if someone does, it will
     * show up as a 404 notFound
     *
     * @return void
     */
    public function errorAction()
    {
        $error = $this->getError();
        if (!$error instanceof Zym_Controller_Plugin_ErrorHandler_Data) {
            // Reserve this action only for the ErrorHandler plugin
            throw new Zend_Controller_Action_Exception(
                'This action cannot be called directly'
            );
        }

        $type = $error->getType();
        $errorHandlers = $this->getErrorHandlers();
        if (isset($errorHandlers[$type])) {
            call_user_func_array(array($this, '_forward'), $errorHandlers[$type]);
        }
        
        if (!$this->getInvokeArg('noViewRenderer') && $this->_helper->hasHelper('ViewRenderer')) {
            // Disable ViewRenderer
            $this->getHelper('ViewRenderer')->setNoRender();
        }
    }
    
    /**
     * Set error
     *
     * @param Zym_Controller_Plugin_ErrorHandler_Data $error
     * @return Zym_Controller_Action_Error
     */
    public function setError(Zym_Controller_Plugin_ErrorHandler_Data $error)
    {
        $this->_error = $error;
        return $this;
    }
    
    /**
     * Get error obj
     *
     * @return Zym_Controller_Plugin_ErrorHandler_Data
     */
    public function getError()
    {
        return $this->_error;
    }
    
    /**
     * Clear and set error handlers
     *
     * @param array $array
     * @return Zym_Controller_Action_Error
     */
    public function setErrorHandlers(array $array = array())
    {
        // Clear handlers
        $this->_errorHandlers = array();
        
        
        // Add them from array
        foreach ($array as $type => $options) {
            $action     = isset($options[self::ACTION]) 
                            ? $options[self::ACTION] : null;
                            
            $controller = isset($options[self::CONTROLLER]) 
                            ? $options[self::CONTROLLER] : null;
                            
            $module     = isset($options[self::MODULE])
                            ? $options[self::MODULE] : null;
             
            $params     = isset($options[self::PARAMS]) 
                            ? $options[self::PARAMS] : null;
                            
            $this->addErrorHandler($type, $action, $controller, $module, $params);
        }
        
        return $this;
    }
    
    /**
     * Add error handlers
     *
     * Error handlers are added in FIFO order
     * 
     * @param string $type
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return Zym_Controller_Action_Error
     */
    public function addErrorHandler($type, $action, $controller = null, $module = null, array $params = null)
    {
        $this->_errorHandlers[$type] = array(
            self::ACTION     => $action,
            self::CONTROLLER => $controller,
            self::MODULE     => $module,
            self::PARAMS     => $params
        );
        
        return $this;
    }
    
    /**
     * Get array of error handlers
     *
     * @return array
     */
    public function getErrorHandlers()
    {
        return $this->_errorHandlers;
    }
}