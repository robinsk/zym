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
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_SimpleUrl
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
     * @todo consider copying code from the action helper to reduce loaded files
     * 
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array $params
     * @return string
     */
    public function simpleUrl($action, $controller = null, $module = null, array $params = null)
    {
        if (!$this->_actionHelper instanceof Zend_Controller_Action_Helper_Url) {
            $this->_actionHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
        }
        
        return $this->_actionHelper->simple($action, $controller, $module, $params);
    }
}