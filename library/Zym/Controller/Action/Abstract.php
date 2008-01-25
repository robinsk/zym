<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@intermentis.nl so we can send you a copy immediately.
 *
 * @category   Zym
 * @package    Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license    http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Zym
 * @package    Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license    http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
abstract class Zym_Controller_Action_Abstract extends Zend_Controller_Action
{
    /**
     * Get the view object
     * 
     * @return Zend_View
     */
    public function getView()
    {
        return $this->view;
    }
    
    /**
     * Get the view script suffix
     * 
     * @return string
     */
    public function getViewSuffix()
    {
        return $this->viewSuffix;
    }
    
    /**
     * Perform a redirect to an action/controller/module with params.
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array $params
     * @return void
     */
    protected function _goto($action, $controller = null, $module = null, array $params = array(), $exit = true)
    {
        if ($exit) {
            $this->_helper->redirector->gotoAndExit($action, $controller, $module, $params);
        } else {
            $this->_helper->redirector->goto($action, $controller, $module, $params);
        }
    }
}