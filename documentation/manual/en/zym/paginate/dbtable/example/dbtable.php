<?php
class Foo_Table extends Zend_Db_Table_Abstract
{
    /**
     * Get cool stuff paginator
     *
     * @param integer $perpage
     * @return Zym_Paginate_DbTable
     */
    public function getCoolStuffPaginator($perpage = 10)
    {
        $select = $this->select()->where('status=?', 'cool');

        $paginate = new Zym_Paginate_DbTable($this, $select);
        $paginate->setRowLimit($perpage);

        return $paginate;
    }
}