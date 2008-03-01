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
class Zym_Controller_Plugin_RouteByParams extends Zend_Controller_Plugin_Abstract
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
        $moduleKey = $request->getModuleKey();
        $controllerKey = $request->getControllerKey();
        $actionKey = $request->getActionKey();

        // Defaults
        $moduleName = $frontController->getDefaultModule();
        $controllerName = $frontController->getDefaultControllerName();
        $actionName = $frontController->getDefaultAction();

        // Set a url path
        if ($request->getPathInfo() == '') {
            $module = $request->getParam($moduleKey, $moduleName);
            $controller = $request->getParam($controllerKey, $controllerName);
            $action = $request->getParam($actionKey, $actionName);

            if($controller != '' ) {
                $url = ($module == $moduleName) ? $module : '/' . $module ;
                $url .= '/' . $controller;
                $url .= '/' . $action;
                $request->setPathInfo($url);
            }
        }
    }

}