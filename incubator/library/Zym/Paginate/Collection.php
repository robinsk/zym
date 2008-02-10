<?php
abstract class Zym_Paginate_Collection
{
    protected $_pages = null;

    protected $_dataSet = null;

    public function __construct($dataSet)
    {
        $this->_dataSet = $dataSet;
    }

    public function getPage($page)
    {
        $pages = $this->getAllPages();

        $key = $page - 1;

        if (!array_key_exists($key, $pages)) {
            throw new Exception('Page not found');
        }

        return $pages[$key];
    }
}