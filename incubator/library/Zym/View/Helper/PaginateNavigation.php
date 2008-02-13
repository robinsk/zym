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

    protected $_markupListStart = '<div id="MVHPContainer"><ul id="MVHPList">';
    protected $_markupListEnd = '</ul></div>';
    protected $_markupListItem = '<li><a href="%s">%s</a></li>';
    protected $_markupListItemActive = '<li id="MVHPActiveItem"><a href="%s" id="MVHPCurrent">%s</a></li>';
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
    public function paginateNavigation(Zym_Paginate_Abstract $paginate, $targetLocation,
                                       $currentPageAttribute = 'page',
                                       $translations = array(self::L10N_KEY_FIRST    => '&lt;&lt; First',
                                                             self::L10N_KEY_PREVIOUS => '&lt; Previous',
                                                             self::L10N_KEY_NEXT     => 'Next &gt;',
                                                             self::L10N_KEY_LAST     => 'Last &gt;&gt;'))
    {
        $xhtml = $this->_renderPaginationStart();

        $xhtml .= $this->_renderPreviousNavigation($paginate, $targetLocation,
                                                   $currentPageAttribute, $translations);

        $xhtml .= $this->_renderPages($paginate, $targetLocation, $currentPageAttribute);

        $xhtml .= $this->_renderNextNavigation($paginate, $targetLocation,
                                               $currentPageAttribute, $translations);

        $xhtml .= $this->_renderPaginationEnd();

        return $xhtml;
    }

    protected function _renderPaginationStart()
    {
        return $this->_markupListStart;
    }

    protected function _renderPaginationEnd()
    {
        return $this->_markupListEnd;
    }

    protected function _renderPreviousNavigation(Zym_Paginate_Abstract $paginate,
                                                 $targetLocation, $currentPageAttribute, $translations)
    {
        if ($paginate->hasPrevious()) {
            $firstPageLocation = array_merge($targetLocation,
                                             array($currentPageAttribute => 1));

            $previousPageLocation = array_merge($targetLocation,
                                                array($currentPageAttribute => $paginate->getPreviousPageNr()));

            $xhtml .= sprintf($this->_markupListItem,
                              $this->_view->url($firstPageLocation, null, true),
                              $translations[self::L10N_KEY_FIRST]);

            $xhtml .= sprintf($this->_markupListItem,
                              $this->_view->url($previousPageLocation, null, true),
                              $translations[self::L10N_KEY_PREVIOUS]);
        }
    }

    protected function _renderPages(Zym_Paginate_Abstract $paginate, $targetLocation,
                                    $currentPageAttribute)
    {
        foreach ($paginate as $pageNumber) {
            $pageLocation = array_merge($targetLocation, array($currentPageAttribute => $pageNumber));

            if ($paginate->isCurrentPageNr($pageNumber)) {
                $xhtml .= sprintf($this->_markupListItemActive,
                                  $this->_view->url($pageLocation, null, true),
                                  $pageNumber);
            } else {
                $xhtml .= sprintf($this->_markupListItem,
                                  $this->_view->url($pageLocation, null, true),
                                  $pageNumber);
            }
        }
    }

    protected function _renderNextNavigation(Zym_Paginate_Abstract $paginate,
                                             $targetLocation, $currentPageAttribute, $translations)
    {
        if ($paginate->hasNext()) {
            $lastPageLocation = array_merge($targetLocation,
                                            array($currentPageAttribute => $paginate->getLastPageNr()));

            $nextPageLocation = array_merge($targetLocation,
                                            array($currentPageAttribute => $paginate->getNextPageNr()));

            $xhtml .= sprintf($this->_markupListItem,
                              $this->_view->url($nextPageLocation, null, true),
                              $translations[self::L10N_KEY_NEXT]);
            $xhtml .= sprintf($this->_markupListItem,
                              $this->_view->url($lastPageLocation, null, true),
                              $translations[self::L10N_KEY_LAST]);
        }
    }
}