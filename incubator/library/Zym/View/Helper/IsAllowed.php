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
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_View_Helper_IsAllowed
{
    /**
     * ACL instance
     *
     * @var Zend_Acl
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

    // Matches signature of MyApp_Acl (we don't need to pass the $role)
    public function isAllowed($resource = null, $privilege = null, $role = null)
    {
        // Default business rule to return null instead of throwing exceptions for non-known resources
        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        return $this->_acl->isAllowed($resource, $privilege, $role);
    }
}
