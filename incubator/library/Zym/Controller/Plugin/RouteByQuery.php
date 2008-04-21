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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Controller plugin to map get params to routes
 *
 * So http://localhost/?module=test&controller=some&action=foo would work
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Plugin_RouteByQuery extends Zend_Controller_Plugin_Abstract
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
        $frontController = Zend_Controller_Front::getInstance();

        // Request keys
        $moduleKey     = $request->getModuleKey();
        $controllerKey = $request->getControllerKey();
        $actionKey     = $request->getActionKey();

        // Defaults
        $moduleName     = $frontController->getDefaultModule();
        $controllerName = $frontController->getDefaultControllerName();
        $actionName     = $frontController->getDefaultAction();

        // Set a url path
        $module     = $request->getQuery($moduleKey, $moduleName);
        $controller = $request->getQuery($controllerKey, $controllerName);
        $action     = $request->getQuery($actionKey, $actionName);

        // Assemble
        if($request->getPathInfo() == '/') {
            $modulePart     = ($module == $moduleName) ? $module : '/' . $module ;
            $controllerPart = ($controller == $controllerName && $action == $actionName) 
                                ? '' : '/' .$controller;
            $actionPart     = ($action == $actionName && $controller) ? '' : '/' . $action;

            $request->setPathInfo($modulePart . $controllerPart . $actionPart);
        }
    }

}