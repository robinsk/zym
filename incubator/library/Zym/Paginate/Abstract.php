<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurri�n Stutterheim
 * @category   Zym
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurri�n Stutterheim
 * @category   Zym
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
abstract class Zym_Paginate_Abstract
{
    /**
     * Total number of pages
     *
     * @var int
     */
    protected $_pageCount = 0;

    /**
     * Total number of items
     *
     * @var int
     */
    protected $_rowCount = 0;

    /**
     * Amount of items per page
     *
     * @var int
     */
    protected $_rowLimit = 10;

    /**
     * The current page nr
     *
     * @var int
     */
    protected $_currentPage = 1;

    /**
     * Check if there are pages available
     *
     * @return boolean
     */
    public function hasPages()
    {
        return ($this->_pageCount > 0);
    }

    /**
     * Check if there is a next page
     *
     * @return boolean
     */
    public function hasNext()
    {
        return ($this->_currentPage < $this->_pageCount);
    }

    /**
     * Check if there is a previous page
     *
     * @return boolean
     */
    public function hasPrevious()
    {
        return ($this->_currentPage > 1);
    }

    /**
     * Get the total amount of pages
     *
     * @return int
     */
    public function getPageCount()
    {
        if ($this->_pageCount < 1) {
            $rowCount = $this->getRowCount();

            $floor = floor($rowCount / $this->_rowLimit);
            $rest = $rowCount % $this->_rowLimit;

            $this->_pageCount = $floor + ($rest > 0 ? 1 : 0);
        }

        return $this->_pageCount;
    }

    /**
     * Get the amount of rows
     *
     * @return int
     */
    public function getRowCount()
    {
        if ($this->_rowCount < 1) {
            /**
             * @see Zym_Paginate_Exception_NoRows
             */
            require_once 'Zym/Paginate/Exception/NoRows.php';

            throw new Zym_Paginate_Exception_NoRows('No rows');
        }

        return $this->_rowCount;
    }

    /**
     * Get amount of items per page
     *
     * @return int
     */
    public function getRowLimit()
    {
        return $this->_rowLimit;
    }

    /**
     * Set the amount of items per page
     *
     * @param int $limit;
     * @return Zym_Paginate_Abstract
     */
    public function setRowLimit($limit)
    {
        $this->_rowLimit = (int) $limit;

        return $this;
    }

    /**
     * Set current page nr
     *
     * @param int $page
     * @return Zym_Paginate_Abstract
     */
    public function setCurrentPageNr($page)
    {
        $this->_currentPage = $page;

        return $this;
    }

    /**
     * Get the current page nr
     *
     * @return int
     */
    public function getCurrentPageNr()
    {
        return $this->_currentPage;
    }

    /**
     * Get the current page
     *
     * @return Zend_Db_Table_Rowset_Abstract|array
     */
    public function getCurrentPage()
    {
        return $this->getPage($this->getCurrentPageNr());
    }

    /**
     * Get the next page
     *
     * @throws Zym_Paginate_Exception_NoNextPage
     * @return Zend_Db_Table_Rowset_Abstract|array
     */
    public function getNextPage()
    {
        if (!$this->hasNext()) {
            /**
             * @see Zym_Paginate_Exception_NoNextPage
             */
            require_once 'Zym/Paginate/Exception/NoNextPage.php';

            throw new Zym_Paginate_Exception_NoNextPage('No next page');
        }

        return $this->getPage($this->getNextPageNr());
    }

    /**
     * Get the next page number
     *
     * @return int
     */
    public function getNextPageNr()
    {
        return $this->getCurrentPageNr() + 1;
    }

    /**
     * Get the previous page
     *
     * @throws Zym_Paginate_Exception_NoPreviousPage
     * @return Zend_Db_Table_Rowset_Abstract|array
     */
    public function getPreviousPage()
    {
        if (!$this->hasPrevious()) {
            /**
             * @see Zym_Paginate_Exception_NoPreviousPage
             */
            require_once 'Zym/Paginate/Exception/NoPreviousPage.php';

            throw new Zym_Paginate_Exception_NoPreviousPage('No previous page');
        }

        return $this->getPage($this->getPreviousPageNr());
    }

    /**
     * Get previous page nr
     *
     * @return int
     */
    public function getPreviousPageNr()
    {
        return $this->getCurrentPageNr() - 1;
    }

    /**
     * Get a page
     *
     * @var int $page
     */
    abstract public function getPage($page);
}