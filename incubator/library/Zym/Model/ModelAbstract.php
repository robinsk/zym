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
require_once 'Zym/Model/IArrayModel.php';

/**
 * @see Zym_Model_IAttributeModel
 */
require_once 'Zym/Model/IAttributeModel.php';

/**
 * @see Zym_Model_IReadOnlyModel
 */
require_once 'Zym/Model/IReadOnlyModel.php';

/**
 * @see Zym_Model_IModifiable
 */
require_once 'Zym/Model/IModifiable.php';

/**
 * @see Zym_Model_IRelationModel
 */
require_once 'Zym/Model/IRelationModel.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Model_ModelAbstract implements Zym_Model_IArrayModel, Zym_Model_IAttributeModel,
                                                  Zym_Model_IReadOnlyModel, Zym_Model_IModifiable,
                                                  Zym_Model_IRelationModel
{

    /**
     * The data for each column in the model (column_name => value).
     * The keys must match the physical names of columns in the
     * table for which this model is defined.
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
     * A model is marked read only if it contains columns that are not physically represented within
     * the database schema (e.g. evaluated columns/Zend_Db_Expr columns). This can also be passed
     * as a run-time config options as a means of protecting model data.
     *
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - table       = class name or object of type Zend_Db_Table_Abstract
     * - data        = values of columns in this model.
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
     * Retrieve model field value
     *
     * @param  string $key The user-specified column name.
     * @return string             The corresponding column value.
     * @throws Zym_Model_Exception if the $key is not a column in the model.
     */
    public function __get($key)
    {
        $key = $this->_transformKey($key);

        if (!array_key_exists($key, $this->_data)) {
            $this->_throwException(sprintf('Specified column "%s" is not in the model', $key));
        }

        return $this->_data[$key];
    }

    /**
     * Set model field value
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
            $this->_throwException(sprintf('Specified column "%s" is not in the model', $key));
        }
        
        $data = $this->_data;
        $modified = $this->_modifiedFields;

        $data[$key] = $value;
        $modified[$key] = true;
        
        $this->_data = $data;
        $this->_modifiedFields = $modified;
    }

    /**
     * Test existence of model field
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
     * Unsets the attribute
     *
     * @param  string $key The user-specified attribute name.
     */
    public function __unset($key)
    {
        if ($this->__isset[$key]) {
            // Work around that nasty PHP 5.2.0 "feature"
            $data = $this->_data;
            $modified = $this->_modifiedFields;
            
            unset($data[$key]);
            unset($modified[$key]);
            
            $this->_data = $data;
            $this->_modifiedFields = $modified;
        }
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
     * Test the read-only status of the model.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * Set the read-only status of the model.
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
         * Reset all fields to null to indicate that the model is not there
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
     * Sets all data in the model from an array.
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
     * hasOne
     *
     */
    public function hasOne()
    {
        // TODO: Implement this
    }
    
    /**
     * hasMany
     *
     */
    public function hasMany()
    {
        // TODO: Implement this
    }
    
    /**
     * belongsTo
     *
     */
    public function belongsTo()
    {
        // TODO: Implement this
    }
    
    /**
     * hasAndBelongsToMany
     *
     */
    public function hasAndBelongsToMany()
    {
        // TODO: Implement this
    }
}