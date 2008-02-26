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
 * @subpackage ExceptionHandler
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zym_App_ExceptionHandler_Abstract
 */
require_once('Zym/App/ExceptionHandler/Abstract.php');

/**
 * @see Zend_Controller_Front
 */
require_once('Zend/Controller/Front.php');

/**
 * @see Zend_Controller_Request_Http
 */
require_once('Zend/Controller/Request/Http.php');

/**
 * @see Zend_Controller_Request_Http
 */
require_once('Zend/Controller/Response/Http.php');

/**
 * @see Zend_Controller_Plugin_ErrorHandler
 */
require_once('Zend/Controller/Plugin/ErrorHandler.php');

/**
 * Init style bootstrap forwards all errors to the ErrorController, else 
 * give a generic http 500 error
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage ExceptionHandler
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_ExceptionHandler_Standard extends Zym_App_ExceptionHandler_Abstract
{
    /**
     * ErrorHandler bootstrap exception identifier
     *
     */
    const EXCEPTION_BOOTSTRAP = 'bootstrap';
    
    /**
     * Module
     *
     * @var string
     */
    protected $_module = 'default';

    /**
     * Controller
     *
     * @var string
     */
    protected $_controller = 'error';

    /**
     * Action
     *
     * @var string
     */
    protected $_action = 'error';
    
    /**
     * Request Params
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Constuct
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function __construct($action = null,  $controller = null, $module = null, array $params = array())
    {
        $this->_module = (string) $module;
        $this->_controller = (string) $controller;
        $this->_action = (string) $action;
        $this->_params = $params;
    }

    /**
     * Handle Bootstrap exceptions
     *
     * @todo Generic ErrorController loading must be done, needs some logging 
     * if available
     * 
     * @param Exception $e
     */
    public function handle(Exception $e)
    {
        // An exception has occured during setup, dispatch the SetupError Action of the Error Handling Controller.    
        try {
            // Exception data
            $exceptionData = array(
                'exception' => $e,
                'type' => Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER
            );
            
            // Build request
            $request = new Zend_Controller_Request_Http();
            $request->setActionName($this->_action)
                    ->setControllerName($this->_controller)
                    ->setModuleName($this->_module)
                    ->setParams($this->_params)
                    ->setParam('error_handler', new ArrayObject($exceptionData, ArrayObject::ARRAY_AS_PROPS));
                    
            // Build response
            $response = new Zend_Controller_Response_Http();
            $response->setException($e);
    
            // Setup for dispatching error (TODO: Fix this and dispatch the controller manually)
            $frontController = Zend_Controller_Front::getInstance();
            $frontController->addModuleDirectory($this->getApp()->getConfig()->home)
                            ->setRequest($request)
                            ->setResponse($response)
                            ->throwExceptions(true);
                            
            // Dispatch
            $frontController->dispatch();
        } catch(Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            // TODO: Load default message
        }
    }
}