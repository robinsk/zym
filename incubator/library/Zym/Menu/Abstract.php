<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Menu
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @category   Zym
 * @package    Menu
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
abstract class Zym_Menu_Abstract
{
    /**
     * (Sub)menu items.
     *
     * @var array
     */
    protected $_menuItems = array();

    /**
     * ID for this element.
     *
     * @var string
     */
    protected $_id = null;

    /**
     * Add a menu item to this menu
     *
     * @param Zym_Menu_Item $menuItem
     * @param int $index
     * @return Zym_Menu_Abstract
     */
    public function addMenuItem(Zym_Menu_Item $menuItem, $index = null)
    {
        if (empty($index) || array_key_exists($index, $this->_menuItems)) {
            $this->_menuItems[] = $menuItem;
        } else {
            $this->_menuItems[$index] = $menuItem;
        }

        return $this;
    }

    /**
     * Set multiple menu items.
     *
     * @param array $items
     * @return Zym_Menu_Abstract
     */
    public function setMenuItems(array $items)
    {
        foreach ($items as $item) {
        	$this->addMenuItem($item);
        }

        return $this;
    }

    /**
     * Set the item id
     *
     * @param string $id
     * @return Zym_Menu_Abstract
     */
    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Get the item id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get the menu items
     *
     * @return array
     */
    public function getMenuItems()
    {
        return $this->_menuItems;
    }

    /**
     * Check if this item has child menus
     *
     * @return boolean
     */
    public function hasMenuItems()
    {
        return (!empty($this->_menuItems));
    }

    /**
     * Returns the entire menu as an array.
     * This can be useful for e.g. serializing it as JSON.
     *
     * @return array
     */
    abstract public function toArray();
}