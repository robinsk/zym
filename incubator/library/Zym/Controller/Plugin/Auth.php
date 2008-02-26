<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * Auth instance
     *
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * ACL instance
     *
     * @var Zend_ACL
     */
    protected $_acl;

    /**
     * ACL route
     *
     * @var string
     */
    protected $_aclRoute = '%s.%s';

    /**
     * Location to go to if the user isn't authenticated
     *
     * @var array
     */
    protected $_noAuth   = array('module'     => 'default',
                                 'controller' => 'auth',
                                 'action'     => 'login');

    /**
     * Location to go to if the user isn't authorized
     *
     * @var array
     */
    protected $_noAcl    = array('module'     => 'default',
                                 'controller' => 'error',
                                 'action'     => 'privileges');

    /**
     * Append the return url when displaying the login screen
     *
     * @var boolean
     */
    protected $_addReturnURL = true;

    /**
     * The key for the return url
     *
     * @var string
     */
    protected $_returnURLKey = 'return';

    /**
     * Set whether the return url needs to be included in the redirect or not
     *
     * @param boolean $bool
     * @return Zym_Controller_Plugin_Auth
     */
    public function setAddReturnURL($bool)
    {
        $this->_addReturnURL = $bool;

        return $this;
    }

    /**
     * Get the the addReturnURL value
     *
     * @return boolean
     */
    public function getAddReturnURL()
    {
        return $this->_addReturnURL;
    }

    /**
     * Set the return URL key
     *
     * @param string $key
     * @return Zym_Controller_Plugin_Auth
     */
    public function setReturnURLKey($key)
    {
        $this->_returnURLKey = $key;

        return $this;
    }

    /**
     * Get the return URL key
     *
     * @return string
     */
    public function getReturnURLKey()
    {
        return $this->_returnURLKey;
    }

    /**
     * Set the location to go to if the use isn't authenticated
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function setNoAuth($action, $controller = null, $module = null)
    {
        if ($module !== null) {
            $this->_noAuth['module'] = $module;
        }

        if ($controller !== null) {
            $this->_noAuth['controller'] = $controller;
        }

        $this->_noAuth['action'] = $action;
    }

    /**
     * Set the location to go to if the use has no permission
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function setNoAcl($action, $controller = null, $module = null)
    {
        if ($module !== null) {
            $this->_noAcl['module'] = $module;
        }

        if ($controller !== null) {
            $this->_noAcl['controller'] = $controller;
        }

        $this->_noAcl['action'] = $action;
    }

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * Checks if the current action is allowed.
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        $module     = $request->getModuleName();
        $resource   = sprintf($this->_aclRoute, $module, $controller);

        if (!$this->_getAcl()->has($resource)) {
            $resource = null;
        }

        if (!$this->_getAcl()->isAllowed($resource, $action)) {
            if (!$this->_getAuth()->hasIdentity()) {
                $module = $this->_noAuth['module'];
                $controller = $this->_noAuth['controller'];
                $action = $this->_noAuth['action'];
            } else {
                $module = $this->_noAcl['module'];
                $controller = $this->_noAcl['controller'];
                $action = $this->_noAcl['action'];
            }

            $returnUrl = urlencode(serialize($request->getParams()));
            $request->setParam('return', $returnUrl);
        }

        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);
    }

    /**
     * Get the acl
     *
     * @throws Zym_ACL_Exception
     * @return Zym_ACL_Abstract
     */
    protected function _getAcl()
    {
        if ($this->_acl === null) {
            $this->_acl = Zym_Acl::getACL();
        }

        return $this->_acl;
    }
    /*
     * @todo discuss this with spotty
     * Idea: have an acl class per module in :module/resources/Acl.php class: :module_Acl extends Zym_Acl
     * Problem: duplicate role setup.
     * Possible solution: have an Acl_Role center per app that sets up roles for the module-specific acl.
    protected function _loadAclFromModule($module)
    {
        $aclFileName = $module . '/resoures/Acl.php';

        if (!file_exists($aclFileName)) {
            return $this->_getAcl();
        }

        $aclClassName = $module . '_Acl';

        return new $aclClassName();
    }
    */
    /**
     * Get the auth instance
     *
     * @return Zend_Auth
     */
    protected function _getAuth()
    {
        if ($this->_auth === null) {
            $this->_auth = Zend_Auth::getInstance();
        }

        return $this->_auth;
    }
}