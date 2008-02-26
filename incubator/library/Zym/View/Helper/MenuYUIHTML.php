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
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
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
    public function MenuYUIHTML(Zym_Menu $menu, $menuClass = 'yuimenubar')
    {
        return $this->_renderMenu($menu, $menuClass, true);
    }

    /**
     * Render the menu
     *
     * @param Zym_Menu_Abstract $menu
     * @param string $menuClass
     * @param boolean $firstRun
     * @return string
     */
    protected function _renderMenu(Zym_Menu_Abstract $menu, $menuClass, $firstOfType = false)
    {
        $xhtml = sprintf('<div id="Menu%s" class="%s">',
                         $menu->getId(), $menuClass);
        $xhtml .= '<div class="bd"><ul';

        if ($firstOfType) {
            $xhtml .= ' class="first-of-type"';
        }

        $xhtml .= '>';

        if ($menu->hasMenuItems()) {
            $menuItems = $menu->getMenuItems();

            foreach ($menuItems as $menuItem) {
                $xhtml .= $this->_renderMenuItem($menuItem, $menuClass);
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
    protected function _renderMenuItem(Zym_Menu_Item $item, $menuClass)
    {
        $link = null;
        $target = $item->getTarget();

        if (!empty($target)) {
            if (is_array($target)) {
                $link = (string) $this->_view->url($target, null, true);
            } else {
                $link = (string) $target;
            }
        }

        $xhtml .= sprintf('<li id="%s" class="%sitem',
                          $item->getId(), $menuClass);

        if ($item->isSelected()) {
            $xhtml .= ' activeMenuItem';
        }

        $xhtml .= '">';

        if (!empty($link)) {
            $xhtml .= sprintf('<a class="%sitemlabel" href="%s">%s</a>',
                              $menuClass, $link, $item->getLabel());
        } else {
            $xhtml .= $item->getLabel();
        }

        if ($item->hasMenuItems()) {
            $xhtml .= $this->_renderMenu($item, $menuClass);
        }

        $xhtml .= '</li>';

        return $xhtml;
    }
}