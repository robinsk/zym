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
 * @package    Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Acl
 */
require_once 'Zend/Acl.php';

/**
 * @see Zend_Auth
 */
require_once 'Zend/Auth.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Acl extends Zend_Acl
{
    /**
     * ACL registry key
     *
     */
    const REGISTRY_KEY = 'zym_acl';

    /**
     * Zend_Auth instance
     *
     * @var Zend_Auth
     */
    protected $_auth = null;
    
    /**
     * User identity
     *
     * @var string
     */
    protected $_identity = null;
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_auth = Zend_Auth::getInstance();

        $this->init();
    }

    /**
     * Get the acl instance from either the frontcontroller or the registry.
     *
     * @throws Zym_Acl_Exception
     * @return Zend_ACL
     */
    public static function getACL()
    {
        $frontParam = Zend_Controller_Front::getInstance()->getParam(self::REGISTRY_KEY);

        if ($frontParam instanceof Zend_Acl) {
            return $frontParam;
        } elseif (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            return Zend_Registry::get(self::REGISTRY_KEY);
        } else {
            throw new Zym_Acl_Exception('Cannot access the acl via the '
                                      . 'front controller or the registry.');
        }
    }

    /**
     * Shortens the signature for 'isAllowed'
     * 
     * The role will be retrieved from the built-in identity
     *
     * @param string $resource
     * @param string $privilege
     * @param string $role
     * @return bool
     */
    public function isAllowedRole($resource = null, $privilege = null)
    {
        $role = $this->getIdentityRole();

        return parent::isAllowed($role, $resource, $privilege);
    }

    /**
     * Allows the ACL tighter integration with the identity
     *
     * @return string
     */
    public function getIdentity()
    {
        if (null == $this->_identity && $this->_auth->hasIdentity()) {
            $this->_identity = $this->_auth->getIdentity();
        }
        
        return $this->_identity;
    }

    /**
     * Retrieves a role from the current identity
     *
     * @return null|string
     */
    public function getIdentityRole()
    {
        if (!$this->_auth->hasIdentity()) {
            return null;
        }
        
        $storage = $this->_auth->getStorage()->read();
        
        return $storage->role;
    }

    /**
     * Add business logic to the acl
     *
     */
    public function init()
    {
    }
}