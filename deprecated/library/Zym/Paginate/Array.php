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
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Paginate_Abstract
 */
require_once 'Zym/Paginate/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Paginate_Array extends Zym_Paginate_Abstract
{
    /**
     * The paginated dataset
     *
     * @var array
     */
    protected $_pages = null;

    /**
     * The dataset
     *
     * @var array
     */
    protected $_dataSet = null;

    /**
     * Constructor
     *
     * @var array $dataSet
     */
    public function __construct(array $dataSet)
    {
        $this->_dataSet = $dataSet;

        $this->_paginateDataSet();
    }

    /**
     * Get a page
     *
     * @throws Zym_Paginate_Exception_PageNotFound
     * @var int $page
     * @return array
     */
    public function getPage($page)
    {
        $pages = $this->getAllPages();

        if (!$this->hasPageNumber($page)) {
            /**
             * @see Zym_Paginate_Exception_PageNotFound
             */
            require_once 'Zym/Paginate/Exception/PageNotFound.php';

            throw new Zym_Paginate_Exception_PageNotFound(sprintf('Page "%s" not found', $page));
        }

        return $pages[$page - 1];
    }

    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages()
    {
        return $this->_pages;
    }

    /**
     * Set the amount of items per page
     *
     * @param int $limit;
     * @return Zym_Paginate_Abstract
     */
    public function setRowLimit($limit)
    {
        parent::setRowLimit($limit);

        $this->_paginateDataSet();

        return $this;
    }

    /**
     * Paginate the dataset
     */
    protected function _paginateDataSet()
    {
        if ($this->_isAssocArray($this->_dataSet)) {
            $this->_paginateAssoc();
        } else {
            $this->_paginateNumeric();
        }
    }

    /**
     * Check if the array is associative
     *
     * @param array $data
     * @return boolean
     */
    protected function _isAssocArray(array $data)
    {
        return count($data) !== array_reduce(array_keys($data), array($this, '_isAssocCallback'), 0);
    }

    /**
     * Check if the current index matches the predicted index.
     * If it does, it's a numeric array key.
     *
     * @param int $left
     * @param int $right
     * @return int
     */
    protected function _isAssocCallback($left, $right)
    {
        return $left === $right ? $left + 1 : 0;
    }

    /**
     * Paginate a numeric array
     */
    protected function _paginateNumeric()
    {
        $this->_pages     = array_chunk($this->_dataSet, $this->_rowLimit);
        $this->_pageCount = count($this->_pages);
        $this->_rowCount  = count($this->_dataSet);
    }

    /**
     * Paginate an associative array
     */
    protected function _paginateAssoc()
    {
        $this->_rowCount = count($this->_dataSet);
        $rowLimit = $this->getRowLimit();
        $pages = array();

        $pageCount = 0;
        $items = 0;

        foreach ($this->_dataSet as $key => $value) {
            $items++;

            $pages[$pageCount][$key] = $value;

            if ($items % $rowLimit === 0) {
                $pageCount++;
            }
        }

        $this->_pages = $pages;
        $this->_pageCount = count($pages);
    }
}