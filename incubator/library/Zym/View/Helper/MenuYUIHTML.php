<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    View_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    View_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_View_Helper_MenuYUIHTML
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
    public function MenuYUIHTML(Zym_Menu $menu)
    {
        return $this->_renderMenu($menu, true);
    }

    /**
     * Render the menu
     *
     * @param Zym_Menu_Abstract $menu
     * @param boolean $firstRun
     * @return string
     */
    protected function _renderMenu(Zym_Menu_Abstract $menu, $firstRun = false)
    {
        $xhtml = sprintf('<div id="Menu%s" class="yuimenu">', $menu->getId());
        $xhtml .= '<div class="bd"><ul';

        if ($firstRun) {
            $xhtml .= ' class="first-of-type"';
        }

        $xhtml .= '>';

        if ($menu->hasMenuItems()) {
            $menuItems = $menu->getMenuItems();

            foreach ($menuItems as $menuItem) {
                $xhtml .= $this->_renderMenuItem($menuItem);
            }
        }

        $xhtml .= '</ul></div></div>';

        return $xhtml;
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

        $xhtml .= sprintf('<li id="%s" class="yuimenuitem', $item->getId());

        if ($item->isSelected()) {
            $xhtml .= ' activeMenuItem';
        }

        $xhtml .= '">';

        if (!empty($link)) {
            $xhtml .= sprintf('<a class="yuimenuitemlabel" href="%s">%s</a>', $link, $item->getLabel());
        } else {
            $xhtml .= $item->getLabel();
        }

        if ($item->hasMenuItems()) {
            $xhtml .= $this->_renderMenu($item);
        }

        $xhtml .= '</li>';

        return $xhtml;
    }
}