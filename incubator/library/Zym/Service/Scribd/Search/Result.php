<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * Zym Scribd Document Search Result API Implementation
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Scribd_Search_Result implements IteratorAggregate, Countable
{
    /**
     * Results
     *
     * @var array Array of Zym_Service_Scribd_Document
     */
    protected $_results = array();

    /**
     * Total Results
     *
     * @var integer
     */
    protected $_totalResults;

    /**
     * Result Position
     *
     * @var integer
     */
    protected $_resultPosition;

    /**
     * Construct
     *
     * @param array $results
     * @param integer $totalResults
     * @param integer $resultPosition
     */
    public function __construct(array $results, $totalResults, $resultPosition)
    {
        $this->_results        = $results;
        $this->_totalResults   = $totalResults;
        $this->_resultPosition = $resultPosition;
    }

    /**
     * Get Total Results
     *
     * @return integer
     */
    public function getTotalResults()
    {
        return $this->_totalResults;
    }

    /**
     * Get Result Position
     *
     * @return integer
     */
    public function getResultPosition()
    {
        return $this->_resultPosition;
    }

    /**
     * Results iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_results);
    }

    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_results);
    }

    /**
     * To Array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_results;
    }
}