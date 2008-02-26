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
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zym_App_Registry_Exception
 */
require_once('Zym/App/Registry/Exception.php');

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
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
            
            throw new Zym_App_Registry_Exception(
                "The requested index \"$index\" does not contain an object of type(s) \"$assertClass\""
            );
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
        } elseif ($this->_hasData($index)) {
            $this->_removeData($index);
        } else {
            throw new Zym_App_Registry_Exception(
                'Cannot remove index "'. $index . '" because it does not exist'
            );
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
            throw new Zym_App_Registry_Exception(
                'The provided index "' . $index . '" is not an index of an existing data index'
            );
        }

        // Make sure the alias is not already set
        if ($this->aliasExists($alias)) {
            throw new Zym_App_Registry_Exception(
                'An alias of the name "'. $alias . '" is already registered'
            );
        }

        $this->_alias[$this->_normalizeIndex($index)][] = $this->_normalizeIndex($alias);
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
        return (bool) count($this->_aliasMap[$this->_normalizeIndex($index)]);
    }

    /**
     * Check if an aliases of a name exists
     *
     * @todo Refractor the loop
     * @param string $alias
     * @return boolean
     */
    public function aliasExists($alias)
    {
        $alias = $this->_normalizeIndex($alias);
        foreach ($this->_aliasMap as $dataIndex => $aliases) {
        	if (in_array($alias, $aliases)) {
        	    return true;
        	}
        }

        return false;
    }

    /**
     * Check if the index name is an alias
     *
     * @todo Refractor the loop
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
     * @todo Refractor the loop
     * @param string $index
     * @return Zym_App_Registry
     */
    public function removeAlias($index)
    {
        $index = $this->_normalizeIndex($index);

        if ($this->isAlias($index)) { // Handle alias input
            foreach ($this->_aliasMap as $dataIndex => $aliases) {
                if (in_array($index, $aliases)) {
            	   $aliasIndex = array_search($index, $aliases);
            	   unset($this->_aliasMap[$dataIndex][$aliasIndex]);
                }
            }
        } elseif ($this->_hasData($index)) { // Handle data input
            unset($this->_aliasMap[$index]);
        } else {
            throw new Zym_App_Registry_Exception(
                'Cannot remove alias "' . $index . '" because it/none exist'
            );
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
     * @todo Refractor the loop
     * @param string $alias
     * @return string
     */
    public function getFromAlias($alias)
    {
        $alias = $this->_normalizeIndex($alias);
        foreach ($this->_aliasMap as $dataIndex => $aliases) {
        	if (in_array($alias, $aliases)) {
        	    return $dataIndex;
        	}
        }

        throw new Zym_App_Registry_Exception(
            'An alias with the name "' . $alias . '" does not exist'
        );
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

        throw new Zym_App_Registry_Exception('Index "' . $index . '" does not exist');
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
        $this->_data[$this->_normalizeIndex($index)] = $value;
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
            throw new Zym_App_Registry_Exception('Index "' . $index . '" does not exist');
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
            $assertClass = array($class);
        }
        
        foreach ((array) $class as $class) {
            if ($class instanceof $obj) {
                return true;
            }
        }
        
        return false;
    }
}