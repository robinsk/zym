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
class Zym_View_Helper_PaginateNavigation
{
    /**
     * Default label for First button
     *
     * @var string
     */
    protected $_labelFirst = '&lt;&lt; First';

    /**
     * Default label for Last button
     *
     * @var string
     */
    protected $_labelLast = 'Last &gt;&gt;';

    /**
     * Default label for Previous button
     *
     * @var string
     */
    protected $_labelPrevious = '&lt; Previous';

    /**
     * Default label for Next button
     *
     * @var string
     */
    protected $_labelNext = 'Next &gt;';

    /**
     * Default translation key for First button
     *
     * @var string
     */
    protected $_labelFirstKey = 'PaginateFirst';

    /**
     * Default translation key for Last button
     *
     * @var string
     */
    protected $_labelLastKey = 'PaginateLast';

    /**
     * Default translation key for Previous button
     *
     * @var string
     */
    protected $_labelPreviousKey = 'PaginatePrevious';

    /**
     * Default translation key for Next button
     *
     * @var string
     */
    protected $_labelNextKey = 'PaginateNext';

    /**
     * The target location
     *
     * @var array
     */
    protected $_targetLocation = array();

    /**
     * The url attribute for the current page
     *
     * @var string
     */
    protected $_currentPageAttrib = 'page';

    /**
     * The number of page items to display
     *
     * @var int
     */
    protected $_pageLimit = 11;

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
     * @param Zym_Paginate_Abstract $paginate
     * @param array $targetLocation
     * @param int $limit
     * @param string $currentPageAttribute
     * @return string
     */
    public function paginateNavigation(Zym_Paginate_Abstract $paginate,
                                       array $targetLocation, $limit = 11,
                                       $currentPageAttribute = 'page')
    {
        $translateHelper = $this->_view->translate();

        if ($translateHelper->getTranslator() instanceof Zend_Translate_Adapter) {
            $this->_labelFirst = $translateHelper->translate($this->_labelFirstKey);
            $this->_labelLast = $translateHelper->translate($this->_labelLastKey);
            $this->_labelNext = $translateHelper->translate($this->_labelNextKey);
            $this->_labelPrevious = $translateHelper->translate($this->_labelPreviousKey);
        }

        $this->_targetLocation = $targetLocation;
        $this->_currentPageAttrib = $currentPageAttribute;
        $this->_pageLimit = $limit;

        $xhtml = $this->_renderPaginationStart();

        $xhtml .= $this->_renderPreviousNavigation($paginate);

        $xhtml .= $this->_renderPages($paginate);

        $xhtml .= $this->_renderNextNavigation($paginate);

        $xhtml .= $this->_renderPaginationEnd();

        return $xhtml;
    }

    /**
     * Render the opening tags for the pagination
     *
     * @return string
     */
    protected function _renderPaginationStart()
    {
        return '<div id="ZVHPContainer"><ul id="ZVHPList">';
    }

    /**
     * Render the closing tags for the pagination
     *
     * @return string
     */
    protected function _renderPaginationEnd()
    {
        return '</ul></div>';
    }

    /**
     * Render a normal list item
     *
     * @param array $location
     * @param string $text
     * @return string
     */
    protected function _renderListItem(array $location, $text)
    {
        return sprintf('<li><a href="%s">%s</a></li>',
                       $this->_view->url($location, null, true),
                       $text);
    }

    /**
     * Render the active list item
     *
     * @param array $location
     * @param string $text
     * @return string
     */
    protected function _renderActiveListItem(array $location, $text)
    {
        return sprintf('<li id="ZVHPActiveItem"><a href="%s" id="ZVHPCurrent">%s</a></li>',
                       $this->_view->url($location, null, true),
                       $text);
    }

    /**
     * Render the naviagtion to get to previous pages
     *
     * @param Zym_Paginate_Abstract $paginate
     * @return string
     */
    protected function _renderPreviousNavigation(Zym_Paginate_Abstract $paginate)
    {
        $xhtml = '';

        if ($paginate->hasPrevious()) {
            $firstPageLocation = array_merge($this->_targetLocation,
                                             array($this->_currentPageAttrib => 1));

            $previousPageLocation = array_merge($this->_targetLocation,
                                                array($this->_currentPageAttrib => $paginate->getPreviousPageNumber()));

            $xhtml .= $this->_renderListItem($firstPageLocation, $this->_labelFirst);

            $xhtml .= $this->_renderListItem($previousPageLocation, $this->_labelPrevious);
        }

        return $xhtml;
    }

    /**
     * Render the page navigation
     *
     * @param Zym_Paginate_Abstract $paginate
     * @param int $limit
     * @return string
     */
    protected function _renderPages(Zym_Paginate_Abstract $paginate)
    {
        $xhtml = '';

        $currentPageNumber = $paginate->getCurrentPageNumber();
        $centerOffset = floor($this->_pageLimit / 2);
        $lastPageNumber = $paginate->getPageCount();

        if ($currentPageNumber <= $centerOffset) {
            $startNumber = 1;
            $endNumber = $this->_pageLimit;
        } else if ($currentPageNumber >= $lastPageNumber - $centerOffset) {
            $startNumber = $lastPageNumber - $this->_pageLimit + 1;
            $endNumber = $lastPageNumber;
        } else {
            $startNumber = $currentPageNumber - $centerOffset;
            $endNumber = $currentPageNumber + $centerOffset;
        }

        for ($i = $startNumber; $i <= $endNumber; $i++) {
            $pageLocation = array_merge($this->_targetLocation, array($this->_currentPageAttrib => $i));

            if ($i == $currentPageNumber) {
                $xhtml .= $this->_renderActiveListItem($pageLocation, $i);
            } else {
                $xhtml .= $this->_renderListItem($pageLocation, $i);
            }
        }

        return $xhtml;
    }

    /**
     * Render the naviagtion to get to next pages
     *
     * @param Zym_Paginate_Abstract $paginate
     * @return string
     */
    protected function _renderNextNavigation(Zym_Paginate_Abstract $paginate)
    {
        $xhtml = '';

        if ($paginate->hasNext()) {
            $lastPageLocation = array_merge($this->_targetLocation,
                                            array($this->_currentPageAttrib => $paginate->getPageCount()));

            $nextPageLocation = array_merge($this->_targetLocation,
                                            array($this->_currentPageAttrib => $paginate->getNextPageNumber()));

            $xhtml .= $this->_renderListItem($nextPageLocation, $this->_labelNext);

            $xhtml .= $this->_renderListItem($lastPageLocation, $this->_labelLast);
        }

        return $xhtml;
    }
}