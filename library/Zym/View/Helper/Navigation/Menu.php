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
 * @see Zym_View_Helper_Navigation_Abstract
 */
require_once 'Zym/View/Helper/Navigation/Abstract.php';

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
class Zym_View_Helper_Navigation_Menu
    extends Zym_View_Helper_Navigation_Abstract
{
    /**
     * CSS class to use for the ul element
     *
     * @var string
     */
    protected $_ulClass = 'navigation';
    
    /**
     * Whether a parent page should be active if a child is active
     *
     * @var bool
     */
    protected $_parentActive = true;

    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              operate on
     * @return Zym_View_Helper_Navigation_Menu      fluent interface,
     *                                              returns self
     */
    public function menu(Zym_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }
    
    // Accessors:

    /**
     * Sets CSS class to use for the first 'ul' element when rendering
     *
     * @param  string $ulClass       CSS class to set
     * @return Zym_View_Helper_Menu  fluent interface, returns self
     */
    public function setUlClass($ulClass)
    {
        if (is_string($ulClass)) {
            $this->_ulClass = $ulClass;
        }

        return $this;
    }

    /**
     * Returns CSS class to use for the first 'ul' element when rendering
     *
     * @return string  CSS class
     */
    public function getUlClass()
    {
        return $this->_ulClass;
    }
    
    /**
     * Sets a flag indicating whether a parent page should be rendered as
     * active if a child page is active
     *
     * @param  bool $flag            whether a parent page should be rendered
     *                               as active if a child is active
     * @return Zym_View_Helper_Menu  fluent interface, returns self
     */
    public function setParentActive($flag)
    {
        $this->_parentActive = (bool) $flag;
        return $this;
    }
    
    /**
     * Returns a flag indicating whether a parent page should be rendered as
     * active if a child is active
     *
     * @return bool  whether a parent page should be rendered as active if a
     *               child is active
     */
    public function getParentActive()
    {
        return $this->_parentActive;
    }
    
    // Public methods:
    
    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     * 
     * Overrides {@link Zym_View_Helper_Navigation_Abstract::htmlify()}.
     *
     * @param  Zym_Navigation_Page $page  page to generate HTML for
     * @return string                     HTML string for the given page
     */
    public function htmlify(Zym_Navigation_Page $page)
    {
        // get view instance
        $view = $this->getView();
        
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();
    
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }
        
        // get attribs for anchor element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );

        $href = $page->getHref();

        if ($href) {
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
            $element = 'a';
        } else {
            $element = 'span';
        }

        return '<' . $element . ' ' . $this->_htmlAttribs($attribs) . '>'
             . $view->escape($label)
             . '</' . $element . '>';
    }

    /**
     * Renders helper
     * 
     * Renders a HTML 'ul' for the given $container. If $container is not given,
     * the container registered in the helper will be used.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to create
     *                                              menu from
     * @param  string|int               $indent     [optional] indentation
     * @param  bool                     $first      [optional] whether this
     *                                              container should be
     *                                              considered the first that is
     *                                              rendered in a series of
     *                                              chained calls. The ul class
     *                                              will only be applied to the
     *                                              first container. Default is
     *                                              true.
     * @return string
     */
    public function renderMenu(Zym_Navigation_Container $container = null,
                               $indent = null,
                               $first = true)
    {
        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();

        if (null === $container) {
            $container = $this->getContainer();
        }

        // init html
        $html = '';

        // iterate container
        foreach ($container as $page) {
            if (!$this->accept($page, false)) {
                // page is not accepted
                continue;
            }

            // create li element for page
            $liCss = $page->isActive($this->getParentActive())
                   ? ' class="active"'
                   : '';
            $html .= "$indent    <li$liCss>" . PHP_EOL;

            // create html element for page
            $html .= "$indent        {$this->htmlify($page)}" . PHP_EOL;

            // render sub pages, if any
            if ($page->hasPages()) {
                $html .= $this->renderMenu($page, "$indent        ", false);
            }

            // end li element
            $html .= "$indent    </li>" . PHP_EOL;
        }

        // wrap items in a ul element
        // this is done so an empty list will not be created if
        // every (sub) page is invisible
        if (strlen($html)) {
            if ($first && strlen($this->_ulClass)) {
                $ulClass = " class=\"{$this->_ulClass}\"";
            } else {
                $ulClass = '';
            }
            
            $html = "$indent<ul$ulClass>\n$html$indent</ul>" . PHP_EOL;
        }

        return $html;
    }

    /**
     * Renders the inner-most sub menu for the active page in the $container
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is to render
     *                                              the container registered in
     *                                              the helper.
     * @param  string|int               $indent     [optional] indentation as
     *                                              a string or number of 
     *                                              spaces. Default is null,
     *                                              which will use the indent
     *                                              registered in the helper.
     * @return string                               rendered content
     */
    public function renderSubMenu(Zym_Navigation_Container $container = null,
                                  $indent = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }
        
        // stuff to use in the two steps below
        $found = false;
        $depth = -1;
        $iterator = new RecursiveIteratorIterator($container,
            RecursiveIteratorIterator::CHILD_FIRST);
        
        // find the deepest active page
        foreach ($iterator as $page) {
            if (!$this->accept($page)) {
                // page is not accepted
                continue;
            }
            if ($page->isActive() && $iterator->getDepth() > $depth) {
                $found = $page;
                $depth = $iterator->getDepth();
            }
        }
        
        if ($found) {
            if (count($found)) {
                return $this->renderMenu($found, $indent, false);
            }
            
            $parent = $found->getParent();
            if ($parent instanceof Zym_Navigation_Page) {
                return $this->renderMenu($parent, $indent, false);
            }
        }
        
        return '';
    }
    
    // Zym_View_Helper_Navigation_Abstract:

    /**
     * Renders helper
     * 
     * Implements {@link Zym_View_Helper_Navigation_Abstract::render()}.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is to render
     *                                              the container registered in
     *                                              the helper.
     * @param  string|int               $indent     [optional] indentation as
     *                                              a string or number of 
     *                                              spaces. Default is null,
     *                                              which will use the indent
     *                                              registered in the helper.
     * @return string                               helper output
     */
    public function render(Zym_Navigation_Container $container = null,
                           $indent = null)
    {
        return rtrim($this->renderMenu($container, $indent, true), PHP_EOL);
    }
}
