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
 * Zym Service Atlassian Crowd Entity Nestable Group
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian_Crowd_Entity
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Atlassian_Crowd_Entity_NestableGroup extends Zym_Service_Atlassian_Crowd_Entity
{
    /**
     * Members
     *
     * @var array
     */
    private $_groupMembers = array();

    /**
     * Get members
     *
     * @return array
     */
    public function getGroupMembers()
    {
        return $this->_groupMembers;
    }

    /**
     * Set members
     *
     * @param array $members
     * @return Zym_Service_Atlassian_Crowd_Entity_Group
     */
    public function setGroupMembers(array $members)
    {
        $this->_groupMembers = $members;
        return $this;
    }

    /**
     * Set from array
     *
     * @param array $array
     * @return Zym_Service_Atlassian_Crowd_Entity_Group
     */
    public function setFromArray(array $array)
    {
        parent::setFromArray($array);

        if (isset($array['groupMembers'])) {
            $this->setGroupMembers((array) $array['groupMembers']->string);
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
            'groupMembers' => $this->getGroupMembers()
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