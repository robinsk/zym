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
class Zym_View_Helper_PaginateNavigation
{
    /**
     * Language constants
     *
     */
    const L10N_KEY_FIRST    = 'first';
    const L10N_KEY_LAST     = 'last';
    const L10N_KEY_PREVIOUS = 'previous';
    const L10N_KEY_NEXT     = 'next';

    /**
     * Style constants
     *
     */
    const STYLE_CONTAINER = 'container';
    const STYLE_LIST      = 'list';
    const STYLE_ACTIVE    = 'active';
    const STYLE_CURRENT   = 'current';

    /**
     * Markup constants
     *
     */
    const MARKUP_LIST_START  = 'listStart';
    const MARKUP_LIST_END    = 'listEnd';
    const MARKUP_LIST_ITEM   = 'listItem';
    const MARKUP_LIST_ACTIVE = 'activeListItem';

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
     * Make a navigation menu for paginated items
     *
     * @param int $pageCount
     * @param int $currentPage
     * @param array $targetLocation
     * @param string $currentPageAttribute
     * @param string $translations
     * @param string $styles
     * @return string
     */
    public function paginateNavigation($pageCount, $currentPage, $targetLocation,
                                       $currentPageAttribute = 'page',
                                       $translations = array(
                                           self::L10N_KEY_FIRST    => '&lt;&lt; First',
                                           self::L10N_KEY_PREVIOUS => '&lt; Previous',
                                           self::L10N_KEY_NEXT     => 'Next &gt;',
                                           self::L10N_KEY_LAST     => 'Last &gt;&gt;'
                                       ),
                                       $styles = array(
                                           self::STYLE_CONTAINER => 'MVHPContainer',
                                           self::STYLE_LIST      => 'MVHPList',
                                           self::STYLE_ACTIVE    => 'MVHPActiveItem',
                                           self::STYLE_CURRENT   => 'MVHPCurrent'
                                       ),
                                       $markup = array(
                                           self::MARKUP_LIST_START  => '<div id="%s"><ul id="%s">',
                                           self::MARKUP_LIST_END    => '</ul></div>',
                                           self::MARKUP_LIST_ITEM   => '<li><a href="%s">%s</a></li>',
                                           self::MARKUP_LIST_ACTIVE => '<li id="%s"><a href="%s" id="%s">%s</a></li>'
                                       ))
    {
        $xhtml = sprintf($markup[self::MARKUP_LIST_START],
                         $styles[self::STYLE_CONTAINER],
                         $styles[self::STYLE_LIST]);

        if ($currentPage > 1) {
            $firstPageLocation = array_merge($targetLocation,
                                             array($currentPageAttribute => 1));

            $previousPageLocation = array_merge($targetLocation,
                                                array($currentPageAttribute => ($currentPage - 1)));

            $xhtml .= sprintf($markup[self::MARKUP_LIST_ITEM],
                              $this->_view->url($firstPageLocation, null, true),
                              $translations[self::L10N_KEY_FIRST]);

            $xhtml .= sprintf($markup[self::MARKUP_LIST_ITEM],
                              $this->_view->url($previousPageLocation, null, true),
                              $translations[self::L10N_KEY_PREVIOUS]);
        }

        for ($i = 1; $i <= $pageCount; $i++) {
            $pageLocation = array_merge($targetLocation, array($currentPageAttribute => $i));

            if ($i == $currentPage) {
                $xhtml .= sprintf($markup[self::MARKUP_LIST_ACTIVE],
                                  $styles[self::STYLE_ACTIVE],
                                  $this->_view->url($pageLocation, null, true),
                                  $markup[self::STYLE_CURRENT], $i);
            } else {
                $xhtml .= sprintf($markup[self::MARKUP_LIST_ITEM],
                                  $this->_view->url($pageLocation, null, true), $i);
            }
        }

        if ($currentPage < $pageCount) {
            $lastPageLocation = array_merge($targetLocation,
                                            array($currentPageAttribute => $pageCount));

            $nextPageLocation = array_merge($targetLocation,
                                            array($currentPageAttribute => ($currentPage + 1)));

            $xhtml .= sprintf($markup[self::MARKUP_LIST_ITEM],
                              $this->_view->url($nextPageLocation, null, true),

                              $translations[self::L10N_KEY_NEXT]);
            $xhtml .= sprintf($markup[self::MARKUP_LIST_ITEM],
                              $this->_view->url($lastPageLocation, null, true),
                              $translations[self::L10N_KEY_LAST]);
        }

        $xhtml .= $markup[self::MARKUP_LIST_END];

        return $xhtml;
    }
}