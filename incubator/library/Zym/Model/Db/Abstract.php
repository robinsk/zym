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
 * @see Zym_Model_Interface
 */
require_once 'Zym/Model/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Model_Db_Abstract implements Zym_Model_Interface
{
    /**
     * Row instance
     *
     * @var Zend_Db_Table_Row_Abstract
     */
    protected $_row = null;
    
    /**
     * Constructor
     *
     * @param Zend_Db_Table_Row_Abstract $row
     */
    public function __construct(Zend_Db_Table_Row_Abstract $row)
    {
        $this->_row = $row;
    }
    
    /**
     * Get a value from the row
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_row->$key;
    }
    
    /**
     * Set a value in the row
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->_row->$key = $value;
    }
    
    /**
     * Checks if the specified field is set
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->_row->$key);
    }
    
    /**
     * Save the row back to the database
     *
     * @return mixed
     */
    public function save()
    {
        return $this->_row->save();
    }
}