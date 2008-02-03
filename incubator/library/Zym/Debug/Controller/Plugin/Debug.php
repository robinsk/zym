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
 * @package Zym_Debug_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @see Zym_Debug
 */
require_once 'Zym/Debug.php';

/**
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_Debug_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Debug_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // Time routing process
        Zym_Debug::getTimer('Router', 'Dispatch')->start();
    }

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        // Time routing process
        Zym_Debug::getTimer('Router', 'Dispatch')->stop();
        
        $router = Zend_Controller_Front::getInstance()->getRouter();
        if ($router instanceof Zend_Controller_Router_Rewrite) {
            Zym_Debug::log("Using route \"{$router->getCurrentRouteName()}\"");
        }
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Time dispatch loop process
        Zym_Debug::getTimer('Dispatch Loop', 'Dispatch')->start();
    }

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Time dispatch process
        Zym_Debug::getTimer('Dispatch', 'Dispatch Loop')->start();
    }

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Time dispatch process
        Zym_Debug::getTimer('Dispatch', 'Dispatch Loop')->stop();
        
        // Dispatched action
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        // Log
        Zym_Debug::log(
            "Dispatched {module: \"$module\", controller: \"$controller\", action: \"$action\"}"
        );
        
    }

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        // Time dispatch loop process
        Zym_Debug::getTimer('DispatchLoop', 'Dispatch')->stop();
    }
}