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
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Paginate_Abstract
 */
require_once 'Zym/Paginate/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Paginate_Iterator extends Zym_Paginate_Abstract
{
    /**
     * The iterator
     *
     * @var Iterator
     */
    protected $_iterator = null;

    /**
     * Constructor
     *
     * @var Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->_iterator = $iterator;
    }

    /**
     * Get a page
     *
     * @var int $page
     */
    public function getPage($page)
    {
        $offset = ((int) $page - 1) * $this->getRowLimit();

        return new LimitIterator($this->_iterator, $offset, $this->getRowLimit());
    }
}