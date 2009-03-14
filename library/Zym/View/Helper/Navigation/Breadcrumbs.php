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
     * Partial view script to use for rendering menu
     *
     * @var string|array
     */
    protected $_partial = null;

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

    /**
     * Sets which partial view script to use for rendering menu
     *
     * @param  string|array $partial            partial view script or null. If
     *                                          an array is given, it is
     *                                          expected to contain two values;
     *                                          the partial view script to use,
     *                                          and the module where the script
     *                                          can be found.
     * @return Zym_View_Helper_Navigation_Menu  fluent interface, returns self
     */
    public function setPartial($partial)
    {
        if (null === $partial || is_string($partial) || is_array($partial)) {
            $this->_partial = $partial;
        }

        return $this;
    }

    /**
     * Returns partial view script to use for rendering menu
     *
     * @return string|array|null
     */
    public function getPartial()
    {
        return $this->_partial;
    }

    // Render methods:

    /**
     * Renders breadcrumbs by chaining 'a' elements with the separator
     * registered in the helper
     *
     * Implements {@link Zym_View_Helper_Navigation_Interface::render()}.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is to render
     *                                              the container registered in
     *                                              the helper.
     */
    public function renderStraight(Zym_Navigation_Container $container = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        if (!$active = $this->findActive($container)) {
            return '';
        }

        // init html
        $html = '';

        // step 2: walk back to root
        if ($active['depth'] >= $this->getMinDepth()) {
            $found = $active['page'];

            // put the actve page last
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

    /**
     * Renders the given $container by invoking the partial view helper
     *
     * The container will simply be passed on as a model to the view script,
     * so in the script it will be available in <code>$this->container</code>.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              pass to view script. Default
     *                                              is to use the container
     *                                              registered in the helper.
     * @param  string|array             $partial    [optional] partial view
     *                                              script to use. Default is to
     *                                              use the partial registered
     *                                              in the helper. If an array
     *                                              is given, it is expected to
     *                                              contain two values; the
     *                                              partial view script to use,
     *                                              and the module where the
     *                                              script can be found.
     * @return string                               helper output
     */
    public function renderPartial(Zym_Navigation_Container $container = null,
                                  $partial = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        if (null === $partial) {
            $partial = $this->getPartial();
        }

        if (empty($partial)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(
                    'Unable to render menu: No partial view script provided');
        }

        // put breadcrumb pages in model
        $model = array('pages' => array());
        if ($active = $this->findActive($container)) {
            $active = $active['page'];
            $model['pages'][] = $active;
            while ($parent = $active->getParent()) {
                if ($parent instanceof Zym_Navigation_Page) {
                    $model['pages'][] = $parent;
                } else {
                    break;
                }

                if ($parent === $container) {
                    // break if at the root of the given container
                    break;
                }

                $active = $parent;
            }
            $model['pages'] = array_reverse($model['pages']);
        }

        if (is_array($partial)) {
            if (count($partial) != 2) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(
                        'Unable to render menu: A view partial supplied as ' .
                        'an array must contain two values: partial view ' .
                        'script and module where script can be found');
            }

            return $this->view->partial($partial[0], $partial[1], $model);
        }

        return $this->view->partial($partial, null, $model);
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
        if ($partial = $this->getPartial()) {
            return $this->renderPartial($container, $partial);
        } else {
            return $this->renderStraight($container);
        }
    }
}