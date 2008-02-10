<?php
abstract class Zym_Paginate_Abstract
{
    protected $_pageCount = null;
    protected $_rowCount = null;
    protected $_rowLimit = 10;
    protected $_currentPage = 1;

    public function hasPages()
    {
        return ($this->_pageCount > 0);
    }

    public function hasNext()
    {
        return ($this->_currentPage < $this->_pageCount);
    }

    public function hasPrevious()
    {
        return ($this->_currentPage > 1);
    }

    public function getPageCount()
    {
        if (!$this->_pageCount) {
            $rowCount = $this->getRowCount();

            $floor = floor($rowCount / $this->_rowLimit);
            $rest = $rowCount % $this->_rowLimit;

            $this->_pageCount = $floor + ($rest > 0 ? 1 : 0);
        }

        return $this->_pageCount;
    }

    public function getRowCount()
    {
        if ($this->_rowCount == null) {
            throw new Exception('No rows');
        }

        return $this->_rowCount;
    }

    public function getRowLimit()
    {
        return $this->_rowLimit;
    }

    public function setRowLimit($limit)
    {
        $this->_rowLimit = $limit;

        return $this;
    }

    public function setCurrentPageNr($page)
    {
        $this->_currentPage = $page;

        return $this;
    }

    public function getCurrentPageNr()
    {
        return $this->_currentPage;
    }

    public function getCurrentPage()
    {
        return $this->getPage($this->getCurrentPageNr());
    }

    public function getNextPage()
    {
        if (!$this->hasNext()) {
            throw new Exception('No next page');
        }

        return $this->getPage($this->getNextPageNr());
    }

    public function getNextPageNr()
    {
        return $this->getCurrentPageNr() + 1;
    }

    public function getPreviousPage()
    {
        if (!$this->hasPrevious()) {
            throw new Exception('No previous page');
        }

        return $this->getPage($this->getPreviousPageNr());
    }

    public function getPreviousPageNr()
    {
        return $this->getCurrentPageNr() - 1;
    }

    abstract public function getPage($page);
}