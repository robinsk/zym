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
 * @package    Zym_Js
 * @subpackage Minifier
 * @author     Geoffrey Tran
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * Zym_Js_Minifier Iterator
 *
 * @category   Zym
 * @package    Zym_Js
 * @author     Geoffrey Tran
 * @subpackage Minifier
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Js_Minifier_Iterator implements Iterator, Countable
{
    /**
     * Count
     *
     * @var integer
     */
    protected $_count;
    
    /**
     * String data
     *
     * @var string
     */
    protected $_data;
    
    /**
     * Position
     *
     * @var integer
     */
    protected $_key = 0;
    
    /**
     * Peek
     *
     * @var string
     */
    protected $_peek;
    
    /**
     * Construct
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->_data = (string) $string;
    }
    
    /**
     * Get Count
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_count === null) {
            $this->_count = strlen($this->_data);
        }
        
        return $this->_count;
    }
    
    /**
     * Get current()
     *
     * @return string
     */
    public function current()
    {
        return $this->_data{$this->key()};
    }
    
    /**
     * Get key
     *
     * @return integer
     */
    public function key()
    {
        return $this->_key;
    }
    
    /**
     * Get next char
     *
     * @param boolean $noCommentProcess
     * @return string
     */
    public function next($noCommentProcess = false)
    {
        // Next char
        $char = $this->_nextChar();
        if ($noCommentProcess === true) {
            return $char;
        }
        
        if ($char === '/') {

        switch ($this->peek()) {
            case '/':
                while (true){
                    $char = $this->_nextChar();
                    
                    if (ord($char) <= Zym_Js_Minifier::ORD_LF) {
                        return $char;
                    }
                }
            
            case '*':
                $this->_nextChar();
                
                while (true) {
                    switch ($this->_nextChar()) {
                        case '*':
                            if ($this->peek() === '/') {
                                $this->_nextChar();
                                return ' ';
                            }
                            break;
                        
                        case null:
                            /**
                             * @see Zym_Js_Minifier_Exception_UnterminatedComment
                             */
                            require_once 'Zym/Js/Minifier/Exception/UnterminatedComment.php';
                            throw new Zym_Js_Minifier_Exception_UnterminatedComment(sprintf(
                             'Unterminated Comment at %s characters in', $this->key()
                            ));
                            break;
                    }
                }
            
            default :
                return $char;
        }         
        }
        return $char;
    }
    
    /**
     * Peek at the next char
     *
     * @return string
     */
    public function peek()
    {
        $this->_peek = $this->_nextChar();
        
        return $this->_peek;
    }
    
    /**
     * Rewind the iterator
     *
     * @return void
     */
    public function rewind()
    {
        $this->_key = 0;
    }
    
    /**
     * Valid
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->_data{$this->key()});
    }
    
    /**
     * Get next char in data
     *
     * @return string
     */
    protected function _nextChar()
    {
        $key   = $this->key();
        $count = $this->count();
        
        $char = $this->_peek;
        $this->_peek = null;
        
        if ($char === null) {
            if ($key < $count) {
                $char = $this->_data{$key};
                $this->_key++;
            } else {
                $char = null;
            }
        }
        
        if ($char === "\r") {
            return "\n";
        }
        
        if ($char === null || $char === "\n" || ord($char) >= Zym_Js_Minifier::ORD_SPACE) {
            return $char;
        }
        
        return ' ';
    }
}
