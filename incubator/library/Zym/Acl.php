<?php
/**
 * Intermentis Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@intermentis.nl so we can send you a copy immediately.
 *
 * @category   Intermentis
 * @package    Acl
 * @copyright  Copyright (c) 2007 Intermentis. (http://www.intermentis.nl)
 * @license    http://www.intermentis.nl/license/new-bsd.txt    New BSD License
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
 * @category   Intermentis
 * @package    Acl
 * @copyright  Copyright (c) 2007 Intermentis. (http://www.intermentis.nl)
 * @license    http://www.intermentis.nl/license/new-bsd.txt    New BSD License
 */
class Mentis_Acl extends Zend_Acl
{
    /**
     * ACL registry key
     *
     */
    const REGISTRY_KEY = 'MACLRegistryKey';
    
    /**
     * User identity
     *
     * @var string
     */
    protected $_identity;
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_identity = Zend_Auth::getInstance()->getIdentity();
        }

        $this->init();
    }
    
    /**
     * Get the acl instance from either the frontcontroller or the registry.
     *
     * @throws Mentis_Acl_Exception
     * @return Zend_ACL
     */
    public static function getACL()
    {
        $front = Zend_Controller_Front::getInstance();
        
        if ($front->getParam(self::REGISTRY_KEY) instanceof Zend_Acl) {
            return $front->getParam(self::REGISTRY_KEY);
        } elseif (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            return Zend_Registry::get(self::REGISTRY_KEY);
        } else {
            throw new Mentis_Acl_Exception('Cannot access the acl via the front controller or the registry.');
        }
    }
    
    /**
     * Shortens the signature for 'isAllowed' - we will retrieve the role from the built-in identity
     *
     * @param string $resource
     * @param string $privilege
     * @param string $role
     * @return unknown
     */
    public function isAllowed($resource = null, $privilege = null, $role = null)
    {
        if (empty($role)) {
            $role = $this->getRole();
        }
        
        return parent::isAllowed($role, $resource, $privilege);
    }

    /**
     * Allows the ACL tighter integration with the identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Retrieves a role from the current identity
     *
     * @return null|string
     */
    public function getRole()
    {
        if (!isset($this->_identity)) {
            return null;
        }

        return $this->_identity->role;
    }

    /**
     * Add business logic to the acl
     *
     */
    public function init()
    {
    }
}