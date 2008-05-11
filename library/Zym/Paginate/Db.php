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
class Zym_Paginate_Db extends Zym_Paginate_Abstract
{
    /**
     * The name for the rowcount column
     */
    const ROW_COUNT_COLUMN = 'zym_paginate_row_count';

    /**
     * @var Zend_Db_Table_Select
     */
    protected $_select = null;

    /**
     * Constructor
     *
     * @param Zend_Db_Table_Select $select
     */
    public function __construct(Zend_Db_Select $select)
    {
        $this->_select = $select;

        // @TODO: Check how well this works when you have a query with multiple joined tables
        $countSelect = clone $select;

        if ($countSelect instanceof Zend_Db_Table_Select) {
            $matches = null;
            preg_match('/FROM `(.+)`/', (string) $select, $matches);
            $tableName = $matches[1];
        } else {
            $tableNames = array_keys($countSelect->getPart(Zend_Db_Select::FROM));
            $tableName = $tableNames[0];
        }

        $countSelect->from($tableName, new Zend_Db_Expr('COUNT(*) AS ' . self::ROW_COUNT_COLUMN));

        $result = $countSelect->query()->fetchAll();

        $this->_rowCount = (int) $result[0][self::ROW_COUNT_COLUMN];
    }

    /**
     * Get a page
     *
     * @var int $page
     * @return array
     */
    public function getPage($page)
    {
        $this->_select->limitPage((int) $page, $this->getRowLimit());

        return $this->_select->query()->fetchAll();
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