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
 * @category   Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
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
 * @category   Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Acl_Config extends Zym_Acl
{
    /**
     * Setup the acl object using a config file.
     *
     * @param Zend_Config $config
     * @return Zym_Acl_Config
     */
    public function setup(Zend_Config $config)
    {
        $this->_setupRoles($config->roles);
        $this->_setupResources($config->resources);
        $this->_setupRules($config->rules);

        return $this;
    }

    /**
     * Setup acl roles
     *
     * @param Zend_Config $roles
     * @return Zym_Acl_Config
     */
    protected function _setupRoles(Zend_Config $roles)
    {
        foreach ($roles as $role) {
            $this->_loadRole($roles, $role);
        }

        return $this;
    }

    /**
     * Load a role
     *
     * @param Zend_Config $roles
     * @param Zend_Config $role
     */
    protected function _loadRole(Zend_Config $roles, Zend_Config $role = null)
    {
        $parents = null;

        if (isset($role->parents) && !empty($role->parents))  {
            $parents = array();

            foreach ($role->parents as $parent) {
                if (!$this->hasRole($parent->id)) {
                    $this->_loadRole($roles, $parent);
                }

                $parents[] = new Zend_Acl_Role($parent->id);
            }
        }

        if (!$this->hasRole($role->id)) {
            $this->addRole(new Zend_Acl_Role($role->id), $parents);
        }
    }

    /**
     * Setup acl rules
     *
     * @param Zend_Config $rules
     * @return Zym_Acl_Config
     */
    protected function _setupRules(Zend_Config $rules)
    {
        foreach ($rules as $rule) {
            $role = null;
            $resources = null;

            if (!empty($rule->role)) {
                $role = new Zend_Acl_Role($rule->role);
            }

            if (isset($rule->resources)) {
                $resources = array();

                foreach ($resources as $resource) {
                	$resources[] = new Zend_Acl_Resource($resource->id);
                }
            }

        	if (!isset($rule->action) || $rule->action == 'deny') {
        	    $this->deny($role, $resources);
        	} else {
        	    $this->allow($role, $resources);
        	}
        }

        return $this;
    }

    /**
     * Setup the acl resources
     *
     * @param Zend_Config $resources
     * @return Zym_Acl_Config
     */
    protected function _setupResources(Zend_Config $resources)
    {
        foreach ($resources as $resource) {
            $parent = null;

            if (!empty($resource->parent)) {
                 $parent = new Zend_Acl_Resourc($resource->parent->id);
            }

        	$this->add(new Zend_Acl_Resource($resource->id), $parent);
        }

        return $this;
    }
}