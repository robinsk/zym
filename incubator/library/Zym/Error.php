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
 * Error Class
 *
 * Common error object.
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_Error
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Error
{
    /**
     * Error code
     *
     * @var integer
     */
    protected $_code;
    
    /**
     * Error message
     *
     * @var string
     */
    protected $_message;
    
    /**
     * File that the error occurred in
     *
     * @var string
     */
    protected $_file;
    
    /**
     * Error line
     *
     * @var integer
     */
    protected $_line;
    
    /**
     * Error context
     * 
     * An array of every variable that existed in the scope the 
     * error was triggered in.
     *
     * @var array
     */
    protected $_context = array();

    /**
     * Debug backtrace
     *
     * @var array
     */
    protected $_trace = array();
    
    /**
     * Error Construct
     *
     * @param integer $code
     * @param string $message
     * @param string $file
     * @param integer $line
     * @param array $context
     */
    public function __construct($code, $message, $file = null, $line = null, array $context = array())
    {
        $this->_code    = (int)    $code;
        $this->_message = (string) $message;
        $this->_file    = (string) $file;
        $this->_line    = (int)    $line;
        $this->_context = (array)  $context;
    }

    /**
     * Returns error code.
     *
     * @returns integer error code
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Returns error message.
     *
     * @returns string error message
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Returns error file.
     *
     * @returns string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Returns line number where error occurred.
     *
     * @returns integer line number
     */
    public function getLine()
    {
        return $this->_line;
    }

    /**
     * Returns error context.
     *
     * @returns array error context
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Returns error trace.
     *
     * @returns array error trace.
     */
    public function getTrace()
    {
        if (!$this->_trace) {
            $this->_trace = debug_backtrace();
        }

        return $this->_trace;
    }
}