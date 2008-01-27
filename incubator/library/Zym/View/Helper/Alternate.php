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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * Alternator class (Ripped from naneau ;) )
 *
 * Alternates between set values
 *
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_View_Helper_Alternate implements Iterator {
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
    public function alternate(array $values = null) {
        if (count($values)) {
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
        return $this->_values[$this->key()];
    }

    /**
     * Key
     *
     * @return integer
     */
    public function key()
    {
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
        return isset($this->_values[$this->key()]);
    }

    /**
     * Get the next value
     *
     * @return
     */
    public function getNext()
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
        return (string) $this->getNext();
    }
}