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
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_View_Helper_Html_Navigation
 */
require_once 'Zym/View/Helper/Html/Navigation.php';

/**
 * Helper for printing menus as 'ul' HTML elements
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */ 
class Zym_View_Helper_Menu extends Zym_View_Helper_Html_Navigation
{
    /**
     * CSS class to use for the ul element
     *
     * @var string
     */
    protected $_ulClass = 'Zym_Navigation';
    
    /**
     * Retrieves helper and optionally sets container to operate on
     * 
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              operate on
     * @return Zym_View_Helper_Menu
     */
    public function menu(Zym_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setNavigation($container);
        }
        
        return $this;
    }
    
    /**
     * Sets CSS class to use for ul elements
     *
     * @param  string $ulClass  class to set
     * @return Zym_View_Helper_Menu
     */
    public function setUlClass($ulClass)
    {
        if (is_string($ulClass)) {
            $this->_ulClass = $ulClass;
        }
        
        return $this;
    }
    
    /**
     * Returns CSS class to use for ul elements
     *
     * @return string
     */
    public function getUlClass()
    {
        return $this->_ulClass;
    }
    
    /**
     * Renders ul list menu for the given container
     *
     * @param  Zym_Navigation_Container $container  container to create
     *                                              menu from
     * @param  string|int               $indent     [optional] indentation
     * @param  bool                     $first      [optional] whether this
     *                                              container should be
     *                                              considered the first that is
     *                                              rendered, defaults to true
     * @return string
     */
    public function renderMenu(Zym_Navigation_Container $container,
                               $indent = null, $first = true)
    {
        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();
        
        // init html
        $html = '';
        
        // loop pages
        foreach ($container as $page) {
            if (!$this->_accept($page)) {
                // page is not accepted
                continue;
            }
            
            // create li element for page
            $liCss = $page->isActive(true) ? ' class="active"' : '';
            $html .= "$indent    <li$liCss>\n";
            
            // create anchor element
            $html .= "$indent        {$this->getPageAnchor($page)}\n";
            
            // render sub pages, if any
            if ($page->hasPages()) {
                $html .= $this->renderMenu($page, "$indent        ", false);
            }
            
            // end li element
            $html .= "$indent    </li>\n";
        }
        
        // wrap items in a ul element
        // this is done so an empty list will not be created if
        // every (sub) page is invisible
        if (strlen($html)) {
            if ((bool)$first && strlen($this->_ulClass)) {
                $ulClass = " class=\"{$this->_ulClass}\"";
            } else {
                $ulClass = '';
            }
            $html = "$indent<ul$ulClass>\n$html$indent</ul>\n";
        }
        
        return $html;
    }
    
    /**
     * Renders the registered container as a ul list
     * 
     * @param string|int $indent  [optional]
     * @return string
     */
    public function toString($indent = null)
    {
        return $this->renderMenu($this->getNavigation(), $indent);
    }
}
