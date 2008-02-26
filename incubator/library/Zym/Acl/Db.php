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
 * @todo Rewrite this bitch. Add default tables and rows with the right interfaces.
 * @todo See if the db design needs some love and attention.
 * @todo group inheritance
 */

/**
 * @see Zym_Acl_Abstract
 */
require_once 'Zym/Acl.php';

/**
 * @see Zend_Acl_Resource
 */
require_once 'Zend/Acl/Resource.php';

/**
 * @see Zend_Acl_Role
 */
require_once 'Zend/Acl/Role.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Acl_Db extends Zym_Acl
{
    /**
     * ACL roles
     *
     * @var Zym_Set
     */
    protected $_roles;

    /**
     * ACL rules
     *
     * @var Zym_Set
     */
    protected $_rules;

    /**
     * ACL resources
     *
     * @var Zym_Set
     */
    protected $_resources;

    /**
     * Role table
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_roleTable;

    /**
     * Rule table
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_ruleTable;

    /**
     * Resource table
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_resourceTable;

    /**
     * Add business logic to the acl
     *
     */
    public function setup(Zend_Db_Table_Abstract $roleTable,
                          Zend_Db_Table_Abstract $ruleTable,
                          Zend_Db_Table_Abstract $resourceTable,
                          $fieldSetup = array())
    {
        $this->_roleTable = $roleTable;
        $this->_ruleTable = $ruleTable;
        $this->_resourceTable = $resourceTable;

        $this->_loadAcl();
    }

    /**
     * Load the ACL roles
     *
     * @param Zym_Acl_Role $currentRole
     */
    protected function _loadRole(Zym_Acl_Role $currentRole)
    {
        $roleString = $currentRole->role;
        $parentString = $currentRole->parent;
        // @todo: Make it possbile to add multiple parents
        if ($parentString != null && !$this->hasRole($parentString)) {
            foreach ($this->_roles as $tmpRole) {
                if ($parentString == $tmpRole->role) {
                    $this->_loadRole($tmpRole);
                    break;
                }
            }
        }

        if (!$this->hasRole($roleString)) {
            $this->addRole(new Zend_Acl_Role($roleString), $parentString);
        }
    }

    /**
     * Load ACL rules from DB and load them in a Zend_Acl instance.
     *
     * @return Zend_Acl
     */
    protected function _loadAcl()
    {
        $this->_roles = $this->_roleTable->fetchAll();
        $this->_rules = $this->_ruleTable->fetchAll();
        $this->_resources = $this->_resourceTable->fetchAll();

        foreach ($this->_roles as $role) {
            $this->_loadRole($role);
        }

        foreach ($this->_resources as $resource) {
            $resourceID = null;
            $module = $resource->module;
            $controller = $resource->controller;

            if ($module != null && $controller != null) {
                $resourceID = $module . '.' . $controller;
            }

            if ($resourceID != null && !$this->has($resourceID)) {
                $this->add(new Zend_Acl_Resource($resourceID));
            }
        }

        foreach ($this->_rules as $rule) {
            $resourceID = null;
            $module = $rule->module;
            $controller = $rule->controller;

            if ($module != null && $controller != null) {
                $resourceID = $module . '.' . $controller;
            }

            if ((bool) $rule->allow) {
                $this->allow($rule->role, $resourceID, $rule->action);
            } else {
                $this->deny($rule->role, $resourceID, $rule->action);
            }
        }

        return $this;
    }
}