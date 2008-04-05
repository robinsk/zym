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
 * @package Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Alternator class
 *
 * Alternates between set values such as in creating stripped table
 * rows.
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_Cycle implements Iterator
{
    /**
     * Counter
     *
     * @var integer
     */
    protected $_counter = -1;

    /**
     * Values to alternate from
     *
     * @var array
     */
    protected $_values = array();

    /**
     * Alternate between values
     *
     * @param array $values Shortcut to setValues()
     * @return Zym_View_Helper_Alternate
     */
    public function cycle(array $values = null)
    {
        if ($values !== null) {
            $this->setValues($values);
        }

        return $this;
    }

    /**
     * Set values to alternate with
     *
     * @param array $values
     * @return Zym_View_Helper_Alternate
     */
    public function setValues(array $values)
    {
        $this->_values = array_values($values);
        return $this;
    }

    /**
     * Get values used to alternate with
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Current
     *
     * @return mixed
     */
    public function current()
    {
        $key = $this->key();
        return $this->_values[$key];
    }

    /**
     * Key
     *
     * @return integer
     */
    public function key()
    {
        // Gracefully crap itself
        if (!count($this->_values)) {
            $this->_values = array(null);
        }

        return ($this->_counter % count($this->_values));
    }

    /**
     * Move next
     *
     * @return Zym_View_Helper_Alternate
     */
    public function next()
    {
        $this->_counter++;
        return $this;
    }

    /**
     * Rewind counter
     *
     * @return Zym_View_Helper_Alternate
     */
    public function rewind()
    {
        $this->_counter = -1;
        return $this;
    }

    /**
     * If current is valid
     *
     * @return boolean
     */
    public function valid()
    {
        $key = $this->key();
        return isset($this->_values[$key]);
    }

    /**
     * Get the next value
     *
     * @return
     */
    public function getValue()
    {
        $this->next();
        return $this->current();
    }

    /**
     * Alternate between values
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
