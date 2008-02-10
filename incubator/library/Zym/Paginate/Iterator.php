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
class Zym_Paginate_Iterator extends Zym_Paginate_Collection
{
    /**
     * Get all pages
     *
     * @return array
     */
    public function getAllPages()
    {
        if (empty($this->_pages)) {
            $this->_pages = $this->_paginate();
        }

        return $this->_pages;
    }

    /**
     * Paginate the data set
     */
    protected function _paginate()
    {
        $this->_rowCount = count($this->_dataSet);

        $page = 0;
        $items = 0;

        foreach ($this->_dataSet as $value) {
            $items++;

            $this->_pages[$page][] = $value;

            if ($items % $this->_rowLimit === 0) {
                $page++;
            }
        }

        $this->_pageCount = $page + 1;
    }
}