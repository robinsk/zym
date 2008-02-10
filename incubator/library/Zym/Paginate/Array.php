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
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zym_Paginate_Collection
 */
require_once 'Zym/Paginate/Collection.php';

/**
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Paginate_Array extends Zym_Paginate_Collection
{
    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages()
    {
        if (empty($this->_pages)) {
            if ($this->_isAssocArray($this->_dataSet)) {
                $this->_pages = $this->_paginateAssoc();
            } else {
                $this->_pages = $this->_paginateNumeric();
            }
        }

        return $this->_pages;
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
        $this->_pages = array_chunk($this->_dataSet, $this->_rowLimit);
        $this->_pageCount = count($this->_pages);
        $this->_rowCount = count($this->_dataSet);
    }

    /**
     * Paginate an associative array
     */
    protected function _paginateAssoc()
    {
        $this->_rowCount = count($this->_dataSet);

        $page = 0;
        $items = 0;

        foreach ($this->_dataSet as $key => $value) {
            $items++;

            $this->_pages[$page][$key] = $value;

            if ($items % $this->_rowLimit === 0) {
                $page++;
            }
        }

        $this->_pageCount = $page + 1;
    }
}