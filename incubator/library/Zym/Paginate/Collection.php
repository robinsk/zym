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
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zym_Paginate_Abstract
 */
require_once 'Zym/Paginate/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
abstract class Zym_Paginate_Collection extends Zym_Paginate_Abstract
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
     * @var Iterator|array
     */
    protected $_dataSet = null;

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
            /**
             * @see Zym_Paginate_Exception_PageNotFound
             */
            require_once 'Zym/Paginate/Exception/PageNotFound.php';

            throw new Zym_Paginate_Exception_PageNotFound(sprintf('Page "%s" not found', $page));
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