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
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Paginate_DbTable extends Zym_Paginate_Abstract
{
    /**
     * The name for the rowcount column
     */
    const ROWCOUNT_COLUMN = 'zymPaginateRowCount';

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

        $countSelect = clone $select;
        $countSelect->from($table, new Zend_Db_Expr('COUNT(*) AS ' . self::ROWCOUNT_COLUMN));
        $result = $table->fetchRow($countSelect);

        $this->_rowCount = $result->{self::ROWCOUNT_COLUMN};
    }

    /**
     * Get a page
     *
     * @var int $page
     */
    public function getPage($page)
    {
        $this->_select->limitPage((int) $page, $this->getRowLimit());

        return $this->_table->fetchAll($this->_select);
    }
}