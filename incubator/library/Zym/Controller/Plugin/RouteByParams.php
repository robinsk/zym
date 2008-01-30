<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category Zym
 * @package Zym_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @link http://spotsec.com
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
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
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