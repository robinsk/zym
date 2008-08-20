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
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_IsAllowed
{
    /**
     * ACL instance
     *
     * @var Zym_Acl
     */
    protected $_acl;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_acl = Zym_ACL::getACL();
    }
    
    /**
     * Check if the use is allowed to view the resource
     *
     * @param string $resource
     * @param string $privilege
     * @param string $role
     * @return boolean
     */
    public function isAllowed($resource = null, $privilege = null, $role = null)
    {
        // Default business rule to return null instead of throwing exceptions for non-known resources
        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        if (null === $role) {
            return $this->_acl->isAllowedRole($resource, $privilege); // Attempt to automatically fetch the role
        } else {
            return $this->_acl->isAllowed($resource, $privilege, $role);
        }
    }
}
