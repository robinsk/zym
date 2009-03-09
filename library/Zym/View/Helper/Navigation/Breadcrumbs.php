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
 * Helper for printing breadcrumbs
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Navigation_Breadcrumbs
    extends Zym_View_Helper_Navigation_Abstract
{
    /**
     * Breadcrumbs separator string
     *
     * @var string
     */
    protected $_separator = ' &gt; ';

    /**
     * Minimum depth required to render breadcrumbs
     *
     * @var int
     */
    protected $_minDepth = 1;

    /**
     * Whether last page in breadcrumb should be hyperlinked
     *
     * @var bool
     */
    protected $_linkLast = false;

    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zym_Navigation_Container $container     [optional] container to
     *                                                 operate on
     * @return Zym_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                 returns self
     */
    public function breadcrumbs(Zym_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }

    // Accessors:

    /**
     * Sets breadcrumb separator
     *
     * @param  string $separator                       separator string
     * @return Zym_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                 returns self
     */
    public function setSeparator($separator)
    {
        if (is_string($separator)) {
            $this->_separator = $separator;
        }

        return $this;
    }

    /**
     * Returns breadcrumb separator
     *
     * @return string  breadcrumb separator
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Sets minimum depth of active page that is required to render breadcrumbs
     *
     * @param  int $minDepth                           minimum depth of active
     *                                                 page that is required to
     *                                                 render breadcrumbs
     * @return Zym_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                 returns self
     */
    public function setMinDepth($minDepth)
    {
        $this->_minDepth = (int) $minDepth;
        return $this;
    }

    /**
     * Returns minimum depth of active page that is required to render
     * breadcrumbs
     *
     * @return int minimum depth of active page that is required to render
     *         breadcrumbs
     */
    public function getMinDepth()
    {
        return $this->_minDepth;
    }

    /**
     * Sets whether last page in breadcrumbs should be hyperlinked
     *
     * @param  bool $linkLast                          whether last page should
     *                                                 be hyperlinked
     * @return Zym_View_Helper_Navigation_Breadcrumbs  fluent interface,
     *                                                 returns self
     */
    public function setLinkLast($linkLast)
    {
        $this->_linkLast = (bool) $linkLast;
        return $this;
    }

    /**
     * Returns whether last page in breadcrumbs should be hyperlinked
     *
     * @return bool  whether last page in breadcrumbs should be hyperlinked
     */
    public function getLinkLast()
    {
        return $this->_linkLast;
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
     */
    public function render(Zym_Navigation_Container $container = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        // init html
        $html = '';

        // stuff to use in the two steps below
        $found = false;
        $depth = -1;
        $iterator = new RecursiveIteratorIterator($container,
                RecursiveIteratorIterator::CHILD_FIRST);

        // step 1: find the deepest active page
        foreach ($iterator as $page) {
            if (!$this->accept($page)) {
                // page is not accepted
                continue;
            }

            if ($page->isActive() && $iterator->getDepth() > $depth) {
                // found an active page at a deeper level than before
                $found = $page;
                $depth = $iterator->getDepth();
            }
        }

        // step 2: walk back to root
        if ($depth >= $this->getMinDepth()) {
            // put the current page last
            if ($this->getLinkLast()) {
                $html = $this->htmlify($found);
            } else {
                $html = $found->getLabel();

                // translate if possible
                if ($this->getUseTranslator() && $t = $this->getTranslator()) {
                    $html = $t->translate($html);
                }
            }

            // loop parents and prepend crumb for each
            while ($parent = $found->getParent()) {
                if ($parent instanceof Zym_Navigation_Page) {
                    $html = $this->htmlify($parent)
                          . $this->getSeparator()
                          . $html;
                }

                if ($parent === $container) {
                    // break if at the root of the given container
                    break;
                }

                $found = $parent;
            }
        }

        return strlen($html) ? $this->getIndent() . $html : '';
    }
}