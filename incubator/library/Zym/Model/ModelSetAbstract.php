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
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Model_IModel
 */
require_once 'Zym/Model/IModel.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zend_Db_Table_Rowset_Abstract implements Zym_Model_IModelSet, Zym_Model_IReadOnlyModel,
                                                        Zym_Model_IArrayModel, SeekableIterator, Countable
{
    /**
     * The original data for each row.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Zend_Db_Table_Row_Abstract class name.
     *
     * @var string
     */
    protected $_modelClass = 'Zend_Db_Table_Row';

    /**
     * Iterator pointer.
     *
     * @var integer
     */
    protected $_pointer = 0;

    /**
     * How many data rows there are.
     *
     * @var integer
     */
    protected $_count;

    /**
     * Collection of instantiated Zend_Db_Table_Row objects.
     *
     * @var array
     */
    protected $_rows = array();

    /**
     * @var boolean
     */
    protected $_stored = false;

    /**
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Zend_Db_Table_Rowset_Abstract Fluent interface.
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Zend_Db_Table_Row_Abstract current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $this->_rows[$this->_pointer] = new $this->_modelClass(
                array(
                    'data'     => $this->_data[$this->_pointer],
                    'readOnly' => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function next()
    {
        ++$this->_pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->_pointer < $this->_count;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implements Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }
    
    /**
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @return Zend_Db_Table_Rowset_Abstract
     * @throws Zend_Db_Table_Rowset_Exception
     */
    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position > $this->_count) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new Zend_Db_Table_Rowset_Exception("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;        
    }

    /**
     * Returns a Zend_Db_Table_Row from a known position into the Iterator
     *
     * @param int $position the position of the row expected
     * @param bool $seek wether or not seek the iterator to that position after
     * @return Zend_Db_Table_Row
     * @throws Zend_Db_Table_Rowset_Exception
     */
    public function getRow($position, $seek = false)
    {
        $key = $this->key();
        try {
            $this->seek($position);
            $row = $this->current();
        } catch (Zend_Db_Table_Rowset_Exception $e) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new Zend_Db_Table_Rowset_Exception('No row could be found at position ' . (int) $position);
        }
        if ($seek == false) {
            $this->seek($key);
        }
        return $row;
    }

    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @return array
     */
    public function toArray()
    {
        // @todo This works only if we have iterated through
        // the result set once to instantiate the rows.
        foreach ($this->_rows as $i => $row) {
            $this->_data[$i] = $row->toArray();
        }
        return $this->_data;
    }
}