<?php
class Zym_Paginate_Array extends Zym_Paginate_Collection
{
    public function getAllPages()
    {
        if (empty($this->_pages)) {
            $this->_pages = $this->_paginate();
        }

        return $this->_pages;
    }

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