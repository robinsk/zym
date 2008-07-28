<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian_Crowd_Entity
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Service_Atlassian_Crowd_Entity
 */
require_once 'Zym/Service/Atlassian/Crowd/Entity.php';

/**
 * Zym Service Atlassian Crowd Entity Group
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian_Crowd_Entity
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Atlassian_Crowd_Entity_Role extends Zym_Service_Atlassian_Crowd_Entity
{
    /**
     * Members
     *
     * @var array
     */
    private $_members = array();

    /**
     * Get members
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->_members;
    }

    /**
     * Set members
     *
     * @param array $members
     * @return Zym_Service_Atlassian_Crowd_Entity_Role
     */
    public function setMembers(array $members)
    {
        $this->_members = $members;
        return $this;
    }


    /**
     * Set from array
     *
     * @param array $array
     * @return Zym_Service_Atlassian_Crowd_Entity_Role
     */
    public function setFromArray(array $array)
    {
        parent::setFromArray($array);

        if (isset($array['members']->string)) {
            $this->setMembers((array) $array['members']->string);
        } else if (isset($array['members'])) {
            $this->setMembers((array) $array['members']);
        }

        return $this;
    }

    /**
     * ToArray
     *
     * @return array
     */
    public function toArray()
    {
        $array = array(
            'members' => $this->getMembers()
        );

        // Remove null
        foreach ($array as $key => $item) {
            if ($item == null) {
                unset($array[$key]);
            }
        }

        return array_merge(parent::toArray(), $array);
    }
}