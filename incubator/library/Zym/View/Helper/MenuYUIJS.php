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
 * @package    View_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    View_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_View_Helper_MenuYUIJS
{
    /**
     * @var Zend_View_Interface
     */
    protected $_view;

    /**
     * Set the view object
     *
     * @param Zend_View_Interface $view
     * @return void
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
    }

    /**
     * Automatically generate a menu
     *
     * @param Zym_Menu_Abstract $menu
     * @return string
     */
    public function MenuYUIJS(Zym_Menu $menu)
    {
        return $this->_renderMenu($menu);
    }

    /**
     * Render the menu
     *
     * @param Zym_Menu_Abstract $menu
     * @param boolean $firstRun
     * @return string
     */
    protected function _renderMenu(Zym_Menu_Abstract $menu)
    {
        $js = '[';

        if ($menu->hasMenuItems()) {
            $menuItems = $menu->getMenuItems();
            $itemCount = count($menuItems);

            $i = 0;
            foreach ($menuItems as $menuItem) {
                $js .= $this->_renderMenuItem($menuItem);

                if ($i < $itemCount - 1) {
                    $js .= ',';
                }

                $i++;
            }
        }

        $js .= ']';

        return $js;
    }

    /**
     * Render a menu item
     *
     * @param Zym_Menu_Item $item
     * @return string
     */
    protected function _renderMenuItem(Zym_Menu_Item $item)
    {
        $link = null;
        $target = $item->getTarget();

        if (!empty($target)) {
            if (is_array($target)) {
                $link = $this->_view->url($target, null, true);
            } else {
                $link = (string) $target;
            }
        }

        $js .= sprintf('{text:"%s"', $item->getLabel());

        if ($item->isSelected()) {
            $js .= ',selected:true';
        }

        if (!empty($link)) {
            $js .= sprintf(',url:"%s"', $link);
        }

        if ($item->hasMenuItems()) {
            $js .= sprintf(',submenu:{id:"%s",itemdata:%s}', $item->getId(), $this->_renderMenu($item));
        }

        $js .= '}';

        return $js;
    }
}