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
class Zym_Paginate_DbTable extends Zym_Paginate_Abstract
{
    /**
     * The name for the rowcount column
     */
    const ROW_COUNT_COLUMN = 'zym_paginate_row_count';

    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_table = null;

    /**
     * @var Zend_Db_Table_Select
     */
    protected $_select = null;

    /**
     * Constructor
     *
     * @param Zend_Db_Table_Abstract $table
     * @param Zend_Db_Table_Select $select
     */
    public function __construct(Zend_Db_Table_Abstract $table, Zend_Db_Table_Select $select)
    {
        $this->_table = $table;
        $this->_select = $select;

        // @TODO: Check how well this works when you have a query with multiple joined tables
        $countSelect = clone $select;
        $countSelect->from($table, new Zend_Db_Expr('COUNT(*) AS ' . self::ROW_COUNT_COLUMN));
        $result = $table->fetchRow($countSelect);

        $this->_rowCount = $result->{self::ROW_COUNT_COLUMN};
    }

    /**
     * Get a page
     *
     * @var int $page
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getPage($page)
    {
        $this->_select->limitPage((int) $page, $this->getRowLimit());

        return $this->_table->fetchAll($this->_select);
    }

    /**
     * Get the total amount of pages
     *
     * @return int
     */
    public function getPageCount()
    {
        if ($this->_pageCount === 0 && $this->_rowCount > 0) {
            $rowCount = $this->getRowCount();

            $floor = floor($rowCount / $this->_rowLimit);
            $rest = $rowCount % $this->_rowLimit;

            $this->_pageCount = $floor + ($rest > 0 ? 1 : 0);
        }

        return (int) $this->_pageCount;
    }
}