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
 * @category   Zym_Dto
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zym_ArrayData_Interface
 */
require_once 'Zym/ArrayData/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_Dto
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Dto implements Zym_ArrayData_Interface, ArrayAccess, Iterator, Serializable
{
    /**
     * The data for the Dto
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Keys for the data so we can implement Iterator easilly
     *
     * @var array
     */
    protected $_keys = array();

    /**
     * Current item for iterator
     *
     * @var int
     */
    protected $_current = 0;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setFromArray($data);
    }

    /**
     * Get a value
     *
     * @param string $key
     */
    public function __get($key)
    {
        return $this->getValue($key);
    }

    /**
     * Set a value
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->setValue($key, $value);
    }

    /**
     * Remove a value from the DTO
     *
     * @param string $key
     * @return Zym_Dto
     */
    public function __unset($key)
    {
        return $this->removeValue($key);
    }

    /**
     * Returns the serialized DTO when it's echoed
     *
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * Check of a value is set for the given key
     *
     * @param string $key
     * @return boolean
     */
    public function hasValue($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Get a value from the DTO
     *
     * @throws Zym_Dto_Exception_KeyNotFound
     * @param string $key
     * @return mixed
     */
    public function getValue($key)
    {
        if (!$this->hasValue($key)) {
            $message = sprintf('The value "%s" was not found.');

            throw new Zym_Dto_Exception_KeyNotFound($message);
        }

        return $this->_data[$key];
    }

    /**
     * Set a value
     *
     * @param string $key
     * @param mixed $value
     * @return Zym_Dto
     */
    public function setValue($key, $value)
    {
        $this->_data[$key] = $value;
        $this->_keys[] = $key;

        return $this;
    }

    /**
     * Remove a value from the DTO
     *
     * @param string $key
     * @return Zym_Dto
     */
    public function removeValue($key)
    {
        if ($this->hasValue($key)) {
            $keys = array_flip($this->_keys);
            $index = $keys[$key];

            unset($this->_data[$key]);
            unset($this->_keys[$index]);
        }

        return $this;
    }

    /**
     * Returns the DTO data as an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Set the DTO data from an array
     *
     * @param array $data
     * @return Zym_Dto
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
        	$this->setValue($key, $value);
        }

        return $this;
    }

    /**
     * Check if the key is set
     *
     * @param string $key
     */
    public function offsetExists($key)
    {
        return $this->hasValue($key);
    }

    /**
     * Get a value
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->getValue($key);
    }

    /**
     * Set a value
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->setValue($key, $value);
    }

    /**
     * Unset a value
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->removeValue($key);
    }

    /**
     * Serializable method. Returns the _data array as a serialized string.
     *
     * @return array
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserialize the DTO
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->setFromArray(unserialize($serialized));
    }

    /**
     * Get the current value
     *
     * @return mixed
     */
    public function current()
    {
        return $this->_data[$this->key()];
    }

    /**
     * Get the current key
     *
     * @return string
     */
    public function key()
    {
        return $this->_keys[$this->_current];
    }

    /**
     * Up the counter
     */
    public function next()
    {
        $this->_current++;
    }

    /**
     * Rewind the counter
     */
    public function rewind()
    {
        $this->_current = 0;
    }

    /**
     * Is the iterator still valid?
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_current < count($this->_keys);
    }
}