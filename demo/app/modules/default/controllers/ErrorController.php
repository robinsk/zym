<?php
/**
 * Zym Framework Demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Controller_Action_Error
 */
require_once 'Zym/Controller/Action/Error.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Default_ErrorController extends Zym_Controller_Action_Error
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        // $this->addErrorHandler(Zym_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER, 'not-found');
        // $this->addErrorHandler(Zym_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION, 'not-found');
        // $this->addErrorHandler(Zym_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER, 'internal');

        // Error Handling map
        $this->setErrorHandlers(array(
            Zym_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER => 'not-found',
            Zym_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION     => 'not-found',

            Zym_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER         => array(
                self::ACTION     => 'internal',
                self::CONTROLLER => 'error',
                self::MODULE     => 'default'
            )
        ));
    }

    /**
     * Unauthorized access
     *
     * HTTP 401
     *
     * @return void
     */
    public function unauthorizedAction()
    {}

    /**
     * Forbidden access
     *
     * HTTP 403
     *
     * @return void
     */
    public function forbiddenAction()
    {}

    /**
     * Not-Found
     *
     * Action used when an action or controller could not be dispatched.
     * This is commonly referred to as an HTTP 404
     *
     *
     * @return void
     */
    public function notFoundAction()
    {
        // Send 404 HTTP Error
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');

        // View
        $this->getView()->assign(array(
            'error' => $this->getError()
        ));
    }

    /**
     * Internal Error
     *
     * Action used when an internal server error occured.
     * Commonly referred to as an HTTP 500
     *
     */
    public function internalAction()
    {
        // Send 500 Error
        $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');

        // View
        $this->getView()->assign(array(
            'error' => $this->getError()
        ));
    }
}