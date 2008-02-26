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
 * @package    Zym_Menu
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Menu_Abstract
 */
require_once 'Zym/Menu/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Menu
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Menu extends Zym_Menu_Abstract
{
    /**
     * Constructor
     *
     * @param string $id
     * @param array $menuItems
     */
    public function __construct($id = null, array $menuItems = array())
    {
        $this->setId($id);
        $this->setMenuItems($menuItems);

        $this->_init();
    }

    /**
     * Initalization code goes here when subclassing the menu.
     * You can for example setup the entire menu here.
     *
     */
    protected function _init()
    {
    }

    /**
     * Get the currently selected menu item.
     * Return false if none is selected.
     *
     * @return Zym_Menu_Item|boolean
     */
    public function getSelectedMenuItem()
    {
        foreach ($this->_menuItems as $menuItem) {
            if ($menuItem->isSelected()) {
                return $menuItem;
            }
        }

        return false;
    }

    /**
     * Returns the entire menu as an array.
     * This can be useful for e.g. serializing it as JSON.
     *
     * @return array
     */
    public function toArray()
    {
        $menuItems = array();

        foreach ($this->_menuItems as $menuItem) {
        	$menuItems[] = $menuItem->toArray();
        }

        return $menuItems;
    }
}