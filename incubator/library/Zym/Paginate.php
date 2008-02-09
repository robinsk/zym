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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Paginate
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Paginate
{
    /**
     * Constants
     *
     */
    const ITEMS = 'items';
    const PAGES = 'pages';
    const COUNT = 'count';
    const DEFAULT_ITEMS_PER_PAGE = 10;

    /**
     * Instance
     *
     * @var Zym_Paginate
     */
    protected static $_instance = null;

    /**
     * Get the paginate instance
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Paginate a collection.
     *
     * @param array $data
     * @param int $itemsPerPage
     * @return array
     */
    public static function paginate($data, $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE)
    {
        $paginator = self::getInstance();

        if ($data instanceof Zend_Db_Table_Rowset_Abstract) {
            $data = $paginator->paginateRowset($data, $itemsPerPage);
        } elseif (is_array($data)) {
        	if ($paginator->isAssocArray($data)) {
                $data = $paginator->paginateAssoc($data, $itemsPerPage);
        	} else {
        		$data = $paginator->paginateNumeric($data, $itemsPerPage);
        	}
        } else {
            $data = array(self::ITEMS => $data,
                          self::PAGES => 1,
                          self::COUNT => count($data));
        }

        return $data;
    }

    /**
     * Check if the array is associative
     *
     * @param array $data
     * @return boolean
     */
    public function isAssocArray(array $data)
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
    public function paginateNumeric($data, $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE)
    {
        $itemsPerPage = $this->_getItemsPerPage($itemsPerPage);

        $data = (array) $data;
        $itemsPerPage = (int)$itemsPerPage;

        $paginatedItems = array_chunk($data, $itemsPerPage);
        $pages = count($paginatedItems);
        $itemCount = count($data);

        return array(self::ITEMS => $paginatedItems,
                     self::PAGES => $pages,
                     self::COUNT => $itemCount);
    }

    /**
     * Paginate an associative array
     *
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function paginateAssoc($data, $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE)
    {
        $itemsPerPage = $this->_getItemsPerPage($itemsPerPage);

        $paginatedItems = array();
        $itemCount = count($data);

        $page = 0;
        $items = 0;

        foreach ($data as $key => $value) {
        	$items++;

        	$paginatedItems[$page][$key] = $value;

        	if ($items % $itemsPerPage === 0) {
                $page++;
        	}
        }

        return array(self::ITEMS => $paginatedItems,
                     self::PAGES => ($page + 1),
                     self::COUNT => $itemCount);
    }

    /**
     * Proxy to paginateAssoc.
     * Pagination for rowsets is best done through Zend_Db_Table_Abstract
     *
     * @param Zend_Db_Table_Rowset_Abstract $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function paginateRowset(Zend_Db_Table_Rowset_Abstract $data, $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE)
    {
        return $this->paginateAssoc($data, $itemsPerPage);
    }

    /**
     * Make sure the items per page number is an int greater than 0
     *
     * @param int $itemsPerPage
     * @return int
     */
    protected function _getItemsPerPage($itemsPerPage)
    {
        $itemsPerPage = (int) $itemsPerPage;

        if ($itemsPerPage < 1) {
            $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE;
        }

        return $itemsPerPage;
    }
}