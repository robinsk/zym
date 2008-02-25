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
 * @package    Menu
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Menu_Abstract
 */
require_once 'Zym/Menu/Abstract.php';

/**
 * @see Zend_Filter_Alnum
 */
require_once 'Zend/Filter/Alnum.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Menu
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Menu_Item extends Zym_Menu_Abstract
{
    /**
     * The label for this menu item
     *
     * @var string
     */
    protected $_label = '';

    /**
     * The target location for this menu item.
     *
     * @var array|string
     */
    protected $_target = array();

    /**
     * Determine if the item is selected
     *
     * @var boolean
     */
    protected $_selected = false;

    /**
     * Constructor
     *
     * @param string $label
     * @param array|string $target
     * @param string $id
     * @param array $menuItems
     */
    public function __construct($label = '', $target = array(), $id = null, $menuItems = array())
    {
        $this->setLabel($label);
        $this->setTarget($target);
        $this->setId($id);
        $this->setMenuItems($menuItems);
    }

    /**
     * Get the item id
     *
     * @return string
     */
    public function getId()
    {
        if (empty($this->_id)) {
            $filter = new Zend_Filter_Alnum();
            $filteredLabel = $filter->filter($this->getLabel());
            $this->setId($filteredLabel);
        }

        return $this->_id;
    }

    /**
     * Set the label for this item
     *
     * @param string $label
     * @return Zym_Menu_Item
     */
    public function setLabel($label)
    {
        $this->_label = (string)$label;

        return $this;
    }

    /**
     * Set the target for this item
     *
     * @param array|string $target
     * @return Zym_Menu_Item
     */
    public function setTarget(array $target)
    {
        $this->_target = $target;

        return $this;
    }

    /**
     * Get the label for this item
     *
     * @return string
     */
    public function getLabel()
    {
        return (string)$this->_label;
    }

    /**
     * Get the target for this item
     *
     * @return array|string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Set this element as selected
     *
     * @param boolean $selected
     * @return Zym_Menu_Item
     */
    public function setSelected($selected = true)
    {
        $this->_selected = (bool)$selected;

        return $this;
    }

    /**
     * Check if this item is selected by comparing the target to the request
     *
     * @return boolean
     */
    public function isSelected()
    {
        if (!$this->_selected && is_array($this->_target)) {
            $request = Zend_Controller_Front::getInstance()->getRequest();

            if (count(array_intersect_assoc($request->getParams(), $this->_target)) == count($this->_target)) {
                $this->_selected = true;
            }
        }

        return (bool)$this->_selected;
    }

    /**
     * Returns the entire menu as an array.
     * This can be useful for e.g. serializing it as JSON.
     *
     * @return array
     */
    public function toArray()
    {
        return array('label'     => $this->getLabel(),
                     'target'    => $this->getTarget(),
                     'menuItems' => $this->getMenuItems());
    }
}