<?php
class Zym_Paginate_DbTable extends Zym_Paginate_Abstract
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_table = null;

    /**
     * @var Zend_Db_Table_Select
     */
    protected $_select = null;

    public function __construct(Zend_Db_Table_Abstract $table, Zend_Db_Table_Select $select)
    {
        $this->_table = $table;
        $this->_select = $select;

        $countSelect = clone $select;
        $countSelect->from($table, new Zend_Db_Expr('COUNT(*) AS rowCount'));
        $result = $table->fetchRow($countSelect);

        $this->_rowCount = $result->rowCount;
    }

    public function getPage($page)
    {
        $this->_select->limitPage($page, $this->getRowLimit());

        return $this->_table->fetchAll($this->_select);
    }

    public function getAllPages()
    {
        return $this->_table->fetchAll($this->_select);
    }
}