<?php
class Zym_Paginate_Array extends Zym_Paginate_Collection
{
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
     *
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    protected function _paginateNumeric()
    {
        $this->_pages = array_chunk($this->_dataSet, $this->_rowLimit);
        $this->_pageCount = count($this->_pages);
        $this->_rowCount = count($this->_dataSet);
    }

    /**
     * Paginate an associative array
     *
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
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