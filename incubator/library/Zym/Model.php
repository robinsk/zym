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
abstract class Zym_Model implements Zym_Model_IModel
{

    /**
     * The data for each column in the row (column_name => value).
     * The keys must match the physical names of columns in the
     * table for which this row is defined.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * This is set to a copy of $_data when the data is fetched from
     * a database, specified as a new tuple in the constructor, or
     * when dirty data is posted to the database with save().
     *
     * @var array
     */
    protected $_cleanData = array();

    /**
     * Tracks columns where data has been updated. Allows more specific insert and
     * update operations.
     *
     * @var array
     */
    protected $_modifiedFields = array();

    /**
     * A row is marked read only if it contains columns that are not physically represented within
     * the database schema (e.g. evaluated columns/Zend_Db_Expr columns). This can also be passed
     * as a run-time config options as a means of protecting row data.
     *
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - table       = class name or object of type Zend_Db_Table_Abstract
     * - data        = values of columns in this row.
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @return void
     * @throws Zym_Model_Exception
     */
    public function __construct(array $config = array())
    {
        if (isset($config['data'])) {
            if (!is_array($config['data'])) {
                require_once 'Zym/Model/Exception.php';
                throw new Zym_Model_Exception('Data must be an array');
            }

            $this->_data = $config['data'];
        }

        if (isset($config['stored']) && $config['stored'] === true) {
            $this->_cleanData = $this->_data;
        }

        if (isset($config['readOnly']) && $config['readOnly'] === true) {
            $this->setReadOnly(true);
        }

        $this->init();
    }

    /**
     * Throw an exception with the specified message
     *
     * @param string $message
     * @throws Zym_Model_Exception
     */
    protected function _throwException($message)
    {
        /**
         * @see Zym_Model_Exception
         */
        require_once 'Zym/Model/Exception.php';

        throw new Zym_Model_Exception($message);
    }

    /**
     * Transform a column name from the user-specified form
     * to the physical form used in the database.
     * You can override this method in a custom Row class
     * to implement column name mappings, for example inflection.
     *
     * @param string $key Column name given.
     * @return string The column name after transformation applied (none by default).
     * @throws Zym_Model_Exception if the $key is not a string.
     */
    public function transformKey($key)
    {
        if (!is_string($key)) {
            $this->_throwException('Specified column is not a string');
        }

        // Perform no transformation by default
        return $key;
    }

    /**
     * Retrieve row field value
     *
     * @param  string $key The user-specified column name.
     * @return string             The corresponding column value.
     * @throws Zym_Model_Exception if the $key is not a column in the row.
     */
    public function __get($key)
    {
        $key = $this->_transformKey($key);

        if (!array_key_exists($key, $this->_data)) {
            $this->_throwException(sprintf('Specified column "%s" is not in the row', $key));
        }

        return $this->_data[$key];
    }

    /**
     * Set row field value
     *
     * @param  string $key The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     * @throws Zym_Model_Exception
     */
    public function __set($key, $value)
    {
        if ($this->isReadOnly()) {
            $this->_throwException('The model is read-only');
        }

        $key = $this->_transformKey($key);

        if (!array_key_exists($key, $this->_data)) {
            $this->_throwException(sprintf('Specified column "%s" is not in the row', $key));
        }

        $this->_data[$key] = $value;
        $this->_modifiedFields[$key] = true;
    }

    /**
     * Test existence of row field
     *
     * @param  string  $key   The column key.
     * @return boolean
     */
    public function __isset($key)
    {
        $key = $this->_transformKey($key);

        return array_key_exists($key, $this->_data);
    }

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Test the read-only status of the row.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * Set the read-only status of the row.
     *
     * @param boolean $flag
     * @return boolean
     */
    public function setReadOnly($flag)
    {
        $this->_readOnly = (bool) $flag;
    }

    /**
     * Get the changed keys
     *
     * @return array
     */
    public function getChangedKeys()
    {
        return array_intersect_key($this->_data, $this->_modifiedFields);
    }

    /**
     * Check if the model is modified
     *
     * @return bool
     */
    public function isModified()
    {
        return !empty($this->_modifiedFields);
    }

    /**
     * Check if the model is new and unaltered
     *
     * @return bool
     */
    public function isNew()
    {
        return empty($this->_cleanData);
    }

    /**
     * Reset the model data
     *
     * @return Zym_Model
     */
    public function reset()
    {
        /**
         * Reset all fields to null to indicate that the row is not there
         */
        $this->_data = array_combine(
            array_keys($this->_data),
            array_fill(0, count($this->_data), null)
        );
        
        return $this;
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Sets all data in the row from an array.
     *
     * @param  array $data
     * @return Zym_Model Provides a fluent interface
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function preInsert()
    {
    }

    /**
     * Allows post-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function postInsert()
    {
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function preUpdate()
    {
    }

    /**
     * Allows post-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function postUpdate()
    {
    }

    /**
     * Allows pre-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function preDelete()
    {
    }

    /**
     * Allows post-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function postDelete()
    {
    }
}
