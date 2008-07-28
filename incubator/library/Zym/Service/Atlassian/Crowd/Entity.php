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
 * @subpackage Atlassian_Crowd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * Zym Service Atlassian Crowd Entity
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian_Crowd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_Service_Atlassian_Crowd_Entity
{
    /**
     * Id
     *
     * @var integer
     */
    private $_id;

    /**
     * Active flag
     *
     * @var boolean
     */
    private $_active;

    /**
     * Attributes
     *
     * @var array
     */
    private $_attributes = array();

    /**
     * Conception time in ISO 8601 xsd:DateTime compliant
     *
     * @var Zend_Date|string
     */
    private $_conception;

    /**
     * Description
     *
     * @var string
     */
    private $_description;

    /**
     * Directory id
     *
     * @var integer
     */
    private $_directoryId;

    /**
     * Last modified date in ISO 8601 xsd:dateTime compliant
     *
     * @var Zend_Date|string
     */
    private $_lastModified;

    /**
     * Name
     *
     * @var string
     */
    private $_name;

    /**
     * Get active flag
     *
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->_active;
    }

    /**
     * Set active flag
     *
     * @param boolean $active
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setActive($active)
    {
        $this->_active = $active;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return (array) $this->_attributes;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setAttributes(array $attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * Get conception time
     *
     * @return Zend_Date
     */
    public function getConception()
    {
        if (!$this->_conception instanceof Zend_Date) {
            $conception = new Zend_Date($this->_conception, Zend_Date::ISO_8601);
            $this->_conception = $conception;
        }

        return $this->_conception;
    }

    /**
     * Set conception time
     *
     * @param Zend_Date|string $conception
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setConception($conception)
    {
        $this->_conception = $conception;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Get directory id
     *
     * @return integer
     */
    public function getDirectoryId()
    {
        return (int) $this->_directoryId;
    }

    /**
     * Set directory id
     *
     * @param integer $directoryId
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setDirectoryId($directoryId)
    {
        $this->_directoryId = (int) $directoryId;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Get last modified time
     *
     * @return Zend_Date
     */
    public function getLastModified()
    {
        if (!$this->_lastModified instanceof Zend_Date) {
            $lastModified = new Zend_Date($this->_lastModified, Zend_Date::ISO_8601);
            $this->_lastModified = $lastModified;
        }

        return $this->_lastModified;
    }

    /**
     * Set last modified time
     *
     * @param Zend_Date|string $lastModified
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setLastModified($lastModified)
    {
        $this->_lastModified = $lastModified;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set username
     *
     * @param string $name
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Set from array
     *
     * @param array $array
     * @return Zym_Service_Atlassian_Crowd_Entity
     */
    public function setFromArray(array $array)
    {
        if (isset($array['ID'])) {
            $this->setId($array['ID']);
        }

        if (isset($array['active'])) {
            $this->setActive($array['active']);
        }

        if (isset($array['attributes'])) {
            $this->setAttributes($this->_processAttributes($array['attributes']));
        }

        if (isset($array['conception'])) {
            $this->setConception($array['conception']);
        }

        if (isset($array['description'])) {
            $this->setDescription($array['description']);
        }

        if (isset($array['directoryID'])) {
            $this->setDirectoryId($array['directoryID']);
        }

        if (isset($array['lastModified'])) {
            $this->setLastModified($array['lastModified']);
        }

        if (isset($array['name'])) {
            $this->setName($array['name']);
        }

        return $this;
    }


    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        $return = array(
            'ID'           => $this->getId(),
            'active'       => $this->isActive(),
            'attributes'   => $this->getAttributes(),
            'conception'   => $this->getConception()->toString('YYYY-MM-ddTHH:mm:ssZZZZ'),
            'description'  => $this->getDescription(),
            'directoryID'  => $this->getDirectoryId(),
            'lastModified' => $this->getLastModified()->toString('YYYY-MM-ddTHH:mm:ssZZZZ'),
            'name'         => $this->getName()
        );

        // Remove null
        foreach ($return as $key => $item) {
        	if ($item == null) {
        	    unset($return[$key]);
        	}
        }

        return $return;
    }

    /**
     * Object to array cast
     *
     * @param mixed $data
     * @return array
     */
    protected function _objectToArray($data)
    {
        if(is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->_objectToArray($value);
            }

            return $result;
        }

        return $data;
    }

    /**
     * Process attributes
     *
     * Handle stdClass of java string[] wierdness
     *
     * @param unknown_type $attributes
     * @return unknown
     */
    private function _processAttributes($attributes)
    {
        $attr = array();
        if (isset($attributes->SOAPAttribute)) {
            foreach ((array) $attributes->SOAPAttribute as $item) {
                $attr[] = array(
                   'name'   => $item->name,
                   'values' => array_values((array) ($item->values->string) ? $item->values->string : $item->values)
                );
            }
        } else if ($attributes instanceof stdClass) {
            foreach ($attributes as $item) {
                $attr[] = array(
                   'name'   => $item->name,
                   'values' => array_values((array) ($item->values->string) ? $item->values->string : $item->values)
                );
            }
        } else {
            $attr = (array) $attributes;
        }

        return $attr;
    }
}