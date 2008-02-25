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
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/ErrorHandler.php';

/**
 * ErrorHandler that allows module-based error handling
 *
 * If an errorController is not found inside the current module, then
 * the error is forwarded to the default module's errorController
 *
 * All exceptions that occur during the dispatch of the module error controller
 * is thrown.
 *
 * Usage:
 * <pre>
 * Zend_Controller_Front::getInstance()->registerPlugin(new Zym_Controller_Plugin_ErrorHandler(), 98);
 * </pre>
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_ErrorHandler
{
    /**
     * Request error handler param
     *
     */
    const ERROR_PARAM = 'error_handler';

    /**
     * Flag to determine if the module errorHandler has been called
     *
     * @var boolean
     */
    protected $_isInsideModuleErrorHandler = false;

    /**
     * Enable module error handling
     *
     * @var boolean
     */
    protected $_moduleErrorHandling = true;

    /**
     * Custom module error controller map
     *
     * Array in form of:
     * <pre>
     * array(
     *     'moduleName' => array(
     *         'module' => 'moduleName',
     *         'controller' => 'error',
     *         'action' => 'error'
     *     )
     * );
     * </pre>
     *
     * @var array
     */
    protected $_moduleErrorHandlerMap = array();

    /**
     * Constructor
     *
     * Options may include:
     * - module
     * - controller
     * - action
     *
     * @param  Array $options
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->setErrorHandler($options);
    }

    /**
     * Set module error handling
     *
     * @param boolean $handle
     * @return Zym_Controller_Plugin_ErrorHandler
     */
    public function setModuleErrorHandling($handle = false)
    {
        $this->_moduleErrorHandling = (bool) $handle;
        return $this;
    }

    /**
     * Get module error handling flag
     *
     * @return boolean
     */
    public function getModuleErrorHandling()
    {
        return $this->_moduleErrorHandling;
    }

    /**
     * setErrorHandler() - setup the error handling options
     *
     * @param  array $options
     * @return Zym_Controller_Plugin_ErrorHandler
     */
    public function setErrorHandler(array $options = array())
    {
        if (isset($options['moduleErrorHandling'])) {
            $this->setModuleErrorHandling($options['moduleErrorHandling']);
        }

        if (isset($options['moduleErrorHandlerMap'])) {
            $this->setModuleErrorHandlerMap($options['moduleErrorHandlerMap']);
        }

        return parent::setErrorHandler($options);
    }

    /**
     * Add a error handler map for a module
     *
     * @param array $map {@see $this->_moduleErrorHandlerMap}
     * @return Zym_Controller_Plugin_ErrorHandler
     */
    public function addModuleErrorHandlerMap($module, array $map)
    {
        $this->_moduleErrorHandlerMap[$module] = $map;
        return $this;
    }

    /**
     * Set the module error controller map
     *
     * @param array $map {@see $this->_moduleErrorHandlerMap}
     * @return Zym_Controller_Plugin_ErrorHandler
     */
    public function setModuleErrorHandlerMap(array $map)
    {
        $this->_moduleErrorHandlerMap = $map;
        return $this;
    }

    /**
     * Get the module error controller map
     *
     * @return array {@see $this->_moduleErrorHandlerMap}
     */
    public function getModuleErrorHandlerMap()
    {
        return $this->_moduleErrorHandlerMap;
    }

    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @todo Remove when ZF addeds an ability to set a custom errorHandler
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();

        // Ensure this plugin is the only one (hackish, because FC does not allow custom ER setting)
        // We want to ensure compatibility with the 'noErrorHandler' param
        if ($frontController->hasPlugin('Zend_Controller_Plugin_ErrorHandler')) {
            $frontController->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        }
    }

    /**
     * postDispatch() plugin hook -- check for exceptions and dispatch error
     * handler if necessary
     *
     * If the 'noErrorHandler' front controller flag has been set,
     * returns early.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();

        // Disable error handling if 'noErrorHandler' is set
        if ($frontController->getParam('noErrorHandler')) {
            return;
        }

        // Get response and exceptions
        $response = $this->getResponse();
        $exceptions = $response->getException();


        // Error handling failed... just throw the exception
        if ($this->_isInsideErrorHandlerLoop && count($exceptions) > $this->_exceptionCountAtFirstEncounter) {
            // Exception thrown by error handler; tell the front controller to throw it
            $frontController->throwExceptions(true);
            throw array_pop($exceptions);
        }

        // Check for an exception AND allow the error handler controller the option to forward
        $notInLoop = (!$this->getModuleErrorHandling() && !$this->_isInsideErrorHandlerLoop);
        $inModuleErrorLoop = ($this->getModuleErrorHandling() && !$this->_isInsideModuleErrorHandler);
        if ($response->isException() && ($notInLoop || $inModuleErrorLoop)) {
            $this->_isInsideErrorHandlerLoop = true;

            // Get a count of the number of exceptions encountered
            $this->_exceptionCountAtFirstEncounter = count($exceptions);

            // Setup request
            $module = $this->getErrorHandlerModule();
            $controller = $this->getErrorHandlerController();
            $action = $this->getErrorHandlerAction();

            // Use module error controller if flag is set
            if ($this->getModuleErrorHandling() && !$this->_isInsideModuleErrorHandler) {
                $this->_isInsideModuleErrorHandler = true;
                $curModule = $request->getModuleName();

                $testModule = $curModule;
                $testController = $controller;
                $testAction = $action;

                // Module map set, use those settings
                $moduleMap = $this->getModuleErrorHandlerMap();
                if (array_key_exists($curModule, $moduleMap)) {
                    $curModuleMap = $moduleMap[$curModule];

                    if (!empty($curModuleMap['module'])) {
                        $testModule = $curModuleMap['module'];
                    }

                    if (!empty($curModuleMap['controller'])) {
                        $testController = $curModuleMap['controller'];
                    }

                    if (!empty($curModuleMap['action'])) {
                        $testAction = $curModuleMap['action'];
                    }
                }

                // Build validator request
                $requestValidator = clone $request;
                $requestValidator->setModuleName($testModule)
                                 ->setControllerName($testController)
                                 ->setActionName($testAction);

                // Make sure it's a valid request
                if ($frontController->getDispatcher()->isDispatchable($requestValidator)) {
                    $module = $testModule;
                    $controller = $testController;
                    $action = $testAction;
                }
            }

            // Forward to the error handler
            $request->setParam(self::ERROR_PARAM, $this->_getExceptionInfo($exceptions, $request))
                    ->setModuleName($module)
                    ->setControllerName($controller)
                    ->setActionName($action)
                    ->setDispatched(false);
        }
    }

    /**
     * Get exception information
     *
     * @param array $exceptions
     * @return ArrayObject
     */
    protected function _getExceptionInfo(array $exceptions, Zend_Controller_Request_Abstract $request)
    {
        // Get exception information
        $error            = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $exception        = $exceptions[0];
        $exceptionType    = get_class($exception);
        $error->exception = $exception;

        switch ($exceptionType) {
            case 'Zend_Controller_Dispatcher_Exception':
                $error->type = self::EXCEPTION_NO_CONTROLLER;
                break;

            case 'Zend_Controller_Action_Exception':
                $error->type = self::EXCEPTION_NO_ACTION;
                break;

            default:
                // Check if exception implements an action interface
                if ($exception instanceof Zym_Controller_Action_Exception_Interface) {
                    $error->type = self::EXCEPTION_NO_ACTION;
                } else {
                    $error->type = self::EXCEPTION_OTHER;
                }

                break;
        }
        
        // Keep a copy of the original request
        $error->request = clone $request;

        return $error;
    }
}