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
 * @see Zend_Acl_Assert_Interface
 */
require_once 'Zend/Acl/Assert/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Acl_Assert_Owner implements Zend_Acl_Assert_Interface
{
    /**
     * The column which specifies the creator of the resource
     *
     * @var string
     */
    protected $_creatorColumn = null;
    
    /**
     * The role of the administator, which will always have access to the resource.
     *
     * @var string
     */
    protected $_adminRole = null;
    
    /**
     * Constructor
     *
     * @param string $creatorColumn
     * @param string $adminRole
     */
    public function __construct($creatorColumn = 'creator', $adminRole = 'administrator')
    {
        $this->setCreatorColumn($creatorColumn);
        $this->setAdminRole($adminRole);
    }
    
    /**
     * Get the creator column name
     *
     * @return string
     */
    public function getCreatorColumn()
    {
        return $this->_creatorColumn;
    }
    
    /**
     * Set the creator column name
     *
     * @param string $creatorColumn
     * @return Zym_Acl_Assert_Owner
     */
    public function setCreatorColumn($creatorColumn)
    {
        $this->_creatorColumn = (string) $creatorColumn;
        
        return $this;
    }
    
    /**
     * Get the administrator role name
     *
     * @return string
     */
    public function getAdminRole()
    {
        return $this->_adminRole;
    }
    
    /**
     * Set the administrator role name
     *
     * @param string $adminRole
     * @return Zym_Acl_Assert_Owner
     */
    public function setAdminRole($adminRole)
    {
        $this->_adminRole = (string) $adminRole;
        
        return $this;
    }
    
    /**
     * Assert if the current user is either the owner of the resource, or an
     * administrator
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $role
     * @param Zend_Acl_Resource_Interface $resource
     * @param string $privilege
     * @return bool
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
        if ($acl->getRole() == $this->getAdminRole()) {
            return true;
        }
        
        $creatorColumn = $this->getCreatorColumn();
        
        // Very simple check to match identity credentials to database row
        // TODO: remove hardcoding for id?
        return $acl->getIdentity()->id == $resource->$creatorColumn;
    }
}