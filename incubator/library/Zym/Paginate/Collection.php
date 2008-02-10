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
abstract class Zym_Paginate_Collection
{
    /**
     * The paginated dataset
     *
     * @var array
     */
    protected $_pages = null;

    /**
     * The dataset
     *
     * @var array
     */
    protected $_dataSet = null;

    /**
     * Constructor
     *
     * @var array $dataSet
     */
    public function __construct($dataSet)
    {
        $this->_dataSet = $dataSet;
    }

    /**
     * Get a page
     *
     * @var int $page
     */
    public function getPage($page)
    {
        $pages = $this->getAllPages();

        $key = $page - 1;

        if (!array_key_exists($key, $pages)) {
            throw new Exception('Page not found');
        }

        return $pages[$key];
    }

    /**
     * Get all pages
     *
     * @return array
     */
    public abstract function getAllPages();
}