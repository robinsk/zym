<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_Error
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * Uncaught errors queue.
 *
 * All uncaught errors are queued in this singleton class.
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_Error
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Error_Stack implements Countable, Iterator 
{
    /**
     * Instance
     * 
     * @var Zym_Error_Stack
     */
    protected static $_instance = null;

    /**
     * Error stack array
     * 
     * @var array
     */
    protected $_errors = array();
    
    /**
     * Iteration index
     *
     * @var integer
     */
    protected $_index = 0;
    
    /**
     * Number of elements in configuration data
     *
     * @var integer
     */
    protected $_count;

    /**
     * Construct
     * 
     */
    protected function __construct()
    {}

    /**
     * Clone
     * 
     * Do not allow attempts to clone our singleton.
     */
    protected function __clone()
    {}

    /**
     * Returns an Error Queue instance.
     *
     * @return Zym_Error_Stack
     */
    public static function getInstance()
    {
        // Create an instance if one does not exist
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Add error to error queue.
     *
     * @param Zym_Error $error error object
     * @return void
     */
    public function push(Zym_Error $error)
    {
        self::$_errors[] = $error;
    }

    /**
     * Get error count
     * 
     * Implements Countable
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_count === null) {
            $this->_count = count($this->_errors);
        }
        
        return $this->_count;
    }
    
    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->_errors);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_errors);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function next()
    {
        next($this->_errors);
        $this->_index++;
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind()
    {
        reset($this->_errors);
        $this->_index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_index < $this->_count;
    }
}