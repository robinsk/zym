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
 * @package Zym_App
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Object registry for communication between resources
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Registry
{
    /**
     * Alias map to make init scripts more portable
     *
     * @var array
     */
    protected $_aliasMap = array();

    /**
     * Data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Construct
     *
     * @param array $data Default data
     * @param array $aliasMap Default alias
     */
    public function __construct(array $data = array(), array $aliasMap = array())
    {
        $this->_setData($data);
        $this->_setAliasMap($aliasMap);
    }

    /**
     * Get an index value
     *
     * @param string $index
     * @param mixed $assertClass
     * @return mixed
     */
    public function get($index, $assertClass = null)
    {
        // Check for alias
        if ($this->isAlias($index)) {
            $index = $this->getFromAlias($index);
        }

        // Get data
        $data = $this->_getData($index);

        // Validate data
        if ($this->_assertClass($assertClass, $data)) {
            if (is_object($assertClass)) {
                $assertClass = get_class($assertClass);
            } else if (is_array($assertClass)) {
                $assertClass = implode(' ', $assertClass);
            }

            $message = sprintf('The requested index "%s" does not contain an object of type(s) "%s"', $index, $assertClass);
            throw $this->_getException($message);
        }

        return $data;
    }

    /**
     * Get wheter an index exists
     *
     * @param string $index
     * @return boolean
     */
    public function has($index)
    {
        // Assume it exists if an alias is set
        if ($this->isAlias($index)) {
            return true;
        }

        return $this->_hasData($index);
    }

    /**
     * Removes a variable from the index
     *
     * This function will also remove any alias set to a data index if that
     * index is removed...
     *
     * @param string $index
     * @return Zym_App_Registry
     */
    public function remove($index)
    {
        if ($this->isAlias($index)) {
            $this->removeAlias($index);
        } else if ($this->_hasData($index)) {
            $this->_removeData($index);
        } else {
            throw $this->_getException(sprintf('Cannot remove index "%s" because it does not exist', $index));
        }

        return $this;
    }

    /**
     * Set a variable
     *
     * @param string $index
     * @param string $value
     * @return Zym_App_Registry
     */
    public function set($index, $value)
    {
        // Handle alias
        if ($this->isAlias($index)) {
            $index = $this->getFromAlias($index);
        }

        // Set data
        $this->_setDataItem($index, $value);
        return $this;
    }

    /**
     * Set an index alias
     *
     * @param string $index
     * @param string $alias
     * @return Zym_App_Registry
     */
    public function setAlias($index, $alias)
    {
        // Make sure index is an actual data index
        if (!$this->_hasData($index)) {
            throw $this->_getException(sprintf('The provided index "%s" is not an index of an existing data index', $index));
        }

        // Make sure the alias is not already set
        if ($this->aliasExists($alias)) {
            throw $this->_getException(sprintf('An alias of the name "%s" is already registered', $alias));
        }

        $normalizedIndex = $this->_normalizeIndex($index);
        $normalizedAliad = $this->_normalizeIndex($alias);

        $this->_alias[$normalizedIndex][$normalizedAliad] = $normalizedAliad;
        return $this;
    }

    /**
     * Check if a data index has an alias
     *
     * @param string $index
     * @return boolean
     */
    public function hasAlias($index)
    {
        $index = $this->_normalizeIndex($index);
        return (bool) count($this->_aliasMap[$index]);
    }

    /**
     * Check if an aliases of a name exists
     *
     * @param string $alias
     * @return boolean
     */
    public function aliasExists($alias)
    {
        $alias = $this->_normalizeIndex($alias);
        foreach ($this->_aliasMap as $aliases) {
        	if (isset($aliases[$alias])) {
        	    return true;
        	}
        }

        return false;
    }

    /**
     * Check if the index name is an alias
     *
     * @param string $index
     * @return boolean
     */
    public function isAlias($index)
    {
        // Link to make function more obvious
        return $this->aliasExists($index);
    }

    /**
     * Removes an alias or aliaes. If a data index was provided then it will
     * remove all aliases associated with it. If an alias was provided it will unlink it.
     *
     * @param string $index
     * @return Zym_App_Registry
     */
    public function removeAlias($alias)
    {
        $alias = $this->_normalizeIndex($alias);

        if ($this->isAlias($alias)) { // Handle alias input
            foreach ($this->_aliasMap as $dataIndex => $aliases) {
                if (isset($aliases[$alias])) {
            	   unset($this->_aliasMap[$dataIndex][$alias]);
                }
            }
        } else if ($this->_hasData($alias)) { // Handle data input
            unset($this->_aliasMap[$alias]);
        } else {
            throw $this->_getException(sprintf('Cannot remove alias "%s" because it/none exist', $alias));
        }

        return $this;
    }

    /**
     * Get the alias of a data index
     *
     * @param string $index
     * @return array
     */
    public function getAlias($index)
    {
        $index = $this->_normalizeIndex($index);

        if (!$this->hasAlias($index)) {
            return array();
        }

        return $this->_aliasMap[$index];
    }

    /**
     * Get the name of a data index from an alias
     *
     * @param string $alias
     * @return string
     */
    public function getFromAlias($alias)
    {
        $alias = $this->_normalizeIndex($alias);
        foreach ($this->_aliasMap as $dataIndex => $aliases) {
        	if (isset($aliases[$alias])) {
        	    return $dataIndex;
        	}
        }

        throw $this->_getException(sprintf('An alias with the name "%s" does not exist', $alias));
    }

    /**
     * Set the data in key/value
     *
     * @param array $data
     * @return Zym_App_Registry
     */
    protected function _setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Set alias map for data
     *
     * @param array $aliasMap
     * @return Zym_App_Registry
     */
    protected function _setAliasMap(array $aliasMap)
    {
        $this->_aliasMap = $aliasMap;
        return $this;
    }

    /**
     * Check if a data index exists
     *
     * @param string $index
     * @return boolean
     */
    protected function _hasData($index)
    {
        return isset($this->_data[$this->_normalizeIndex($index)]);
    }

    /**
     * Returns the data
     *
     * @param mixed $index If null, it returns the whole data array
     * @return mixed
     */
    protected function _getData($index = null)
    {
        if ($index === null) {
            return (array) $this->_data;
        }

        if ($this->_hasData($index)) {
            return $this->_data[$this->_normalizeIndex($index)];
        }

        throw $this->_getException(sprintf('Index "%s" does not exist', $index));
    }

    /**
     * Set an index
     *
     * @param string $index
     * @param mixed $value
     * @return Zym_App_Registry
     */
    protected function _setDataItem($index, $value)
    {
        $index = $this->_normalizeIndex($index);
        $this->_data[$index] = $value;
        return $this;
    }

    /**
     * Unset a data index
     *
     * @param string $index
     * @return Zym_App_Registry
     */
    protected function _removeData($index = null)
    {
        if ($index === null) {
            $this->_data = array();
            return $this;
        }

        if ($this->_hasData($index)) {
            unset($this->_data[$this->_normalizeIndex($index)]);
        } else {
            throw $this->_getException(sprintf('Index "%s" does not exist', $index));
        }

        return $this;
    }

    /**
     * Normalize the idexes
     *
     * @param string $index
     * @return string
     */
    protected function _normalizeIndex($index)
    {
        $index = str_replace(' ', '', (string) $index);
        return strtolower($index);
    }

    /**
     * Validate an object
     *
     * @param string $class
     * @param mixed $obj
     */
    protected function _assertClass($class, $obj)
    {
        if (is_object($class)) {
            $class = array($class);
        }

        foreach ((array) $class as $class) {
            if ($class instanceof $obj) {
                return true;
            }
        }

        return false;
    }

    /**
     * Throw an exception
     *
     * @param string $message
     * @throws Zym_App_Registry_Exception
     */
    protected function _getException($message)
    {
        /**
         * @see Zym_App_Registry_Exception
         */
        require_once 'Zym/App/Registry/Exception.php';

        return new Zym_App_Registry_Exception($message);
    }
}