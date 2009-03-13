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
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_View_Helper_Navigation_Abstract
 */
require_once 'Zym/View/Helper/Navigation/Abstract.php';

/**
 * Helper for rendering menus from {@link Zym_Navigation}
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper_Navigation
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
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
             . $this->view->escape($label)
             . '</' . $element . '>';
    }

    /**
     * Renders helper
     *
     * Renders a HTML 'ul' for the given $container. If $container is not given,
     * the container registered in the helper will be used.
     *
     * @param  Zym_Navigation_Container $container   [optional] container to
     *                                               create menu from. Default
     *                                               is to use the container
     *                                               retrieved from
     *                                               {@link getContainer()}.
     * @param  string|int               $indent      [optional] indentation as
     *                                               a string or number of
     *                                               spaces. Default is null,
     *                                               which will use the indent
     *                                               registered in the helper.
     * @param  bool                     $useUlClass  [optional] whether the 'ul'
     *                                               class returned by
     *                                               {@link getUlClass()} should
     *                                               be applied to the 'ul' that
     *                                               is rendered by the method.
     *                                               Default is true.
     * @return string
     */
    public function renderMenu(Zym_Navigation_Container $container = null,
                               $indent = null,
                               $useUlClass = true)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        // indentation
        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();

        // init html
        $html = '';
        $recursive = $this->getParentActive();

        // iterate container
        foreach ($container as $page) {
            if (!$this->accept($page, false)) {
                // page is not accepted
                continue;
            }

            // create li element for page
            $liCss = $page->isActive($recursive)
                   ? ' class="active"'
                   : '';
            $html .= "$indent    <li$liCss>" . self::EOL;

            // create html element for page itself
            $html .= "$indent        {$this->htmlify($page)}" . self::EOL;

            // render sub pages, if any
            if ($page->hasPages()) {
                $html .= $this->renderMenu($page, "$indent        ", false);
            }

            // end li element for page
            $html .= "$indent    </li>" . self::EOL;
        }

        // wrap items in a ul element
        // this is done so an empty list will not be created if
        // every (sub) page is invisible
        if (strlen($html)) {
            if ($useUlClass && $css = $this->getUlClass()) {
                $ulClass = " class=\"$css\"";
            } else {
                $ulClass = '';
            }

            $html = "$indent<ul$ulClass>\n$html$indent</ul>" . self::EOL;
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

        // find deepest active page
        if (!$active = $this->findActive($container)) {
            return '';
        }

        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();

        if ($active['page']->hasPages()) {
            // the found page has children itself; render children
            return $this->renderMenu($active['page'], $indent, false);
        }

        $parent = $active['page']->getParent();
        if ($parent instanceof Zym_Navigation_Page) {
            // the found page is a leaf node with a parent; render parent
            return $this->renderMenu($parent, $indent, false);
        }

        return '';
    }

    // Zym_View_Helper_Navigation_Interface:

    /**
     * Renders helper
     *
     * Implements {@link Zym_View_Helper_Navigation_Interface::render()}.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is to render
     *                                              the container registered in
     *                                              the helper.
     * @return string                               helper output
     */
    public function render(Zym_Navigation_Container $container = null)
    {
        return rtrim($this->renderMenu($container, null, true), self::EOL);
    }
}