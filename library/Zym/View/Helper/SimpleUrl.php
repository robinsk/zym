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
 * @package Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_View_Helper_Url
 */
require_once 'Zend/View/Helper/Url.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_SimpleUrl extends Zym_View_Helper_Abstract
{
    /**
     * Url action helper
     *
     * @var Zend_Controller_Action_Helper_Url
     */
    protected $_actionHelper;

    /**
     * Create URL based on default route
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array $params
     * @return string
     */
    public function simpleUrl($action, $controller = null, $module = null, array $params = null, $encode = true)
    {
        $view = $this->getView();

        $request    = $view->getRequest();

        if ($module === null) {
            $module     = $view->getModuleName();
        }

        if ($controller === null) {
            $controller = $view->getControllerName();
        }

        $urlOptions = array_merge($params, array('module'     => $module,
                                                 'controller' => $controller,
                                                 'action'     => $action));
        $url  = $view->url($urlOptions, 'default', true, $encode);

        return $url;
    }
}