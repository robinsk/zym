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
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * Database
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Db extends Zym_App_Resource_Abstract
{
    /**
     * Database adapters
     *
     * @var array
     */
    protected $_dbAdapter = array();
    
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEVELOPMENT => array(
            'default_config' => array(
                'profiler' => array(
                    'enabled' => true
                )
            )
        ),
        
        Zym_APP::ENV_DEFAULT => array(
            'default_config' => array(
                'adapter' => 'Mysqli',
            
                'registry' => array(
                    'disabled' => false,
                    'key' => 'db'
                ),
                
                'set_default_adapter' => array(
                    'Zend_Db_Table_Abstract'
                ),
                
                'profiler' => array(
                    'enabled' => false,
                    'class' => null,
                    'filter' => array(
                        'elapsed_secs' => null,
                        'query_type' => null
                    )
                )
            ),
            
            'connection' => array()
        )
    );

    /**
     * Setup db
     *
     */
    public function setup(Zend_Config $config)
    {
        // Determine if config is for a single-db or a multi-db site
        $dbConfigs = (isset($config->dbname)) ? array($config) : $config->get('connection');
        foreach ($dbConfigs as $dbConfig) {
            // Merge default config
            $dbConfig = $this->_mergeConfig($config->get('default_config'), $dbConfig);

            // TODO: Cleanup config
            // Sigh, bad code in Zend_Db_Adapter_Abstract... we cannot have an empty string for profile class
            
            // Create db adapter
            $db = Zend_Db::factory($dbConfig->get('adapter'), $dbConfig->toArray());
            
            // Setup profiler
            $this->_setupProfiler($dbConfig->get('profiler'), $db);
            
            // Setup tables
            $this->_setupTables($dbConfig, $db);

            // Make sure db keys don't already exist, else add numbers to them such as db-2, db-3
            $dbKey = $this->_makeDbKey($dbConfig->get('registry')->get('key'));
            
            // Store db obj
            $this->setAdapter($db, $dbKey);
            
            // Pass to internal registry
            $this->getRegistry()->set($dbKey, $db);
            
            // Determine if we should save the db adapter in the registry
            $dbRegistryDisabled = (isset($dbConfig->get('registry')->disabled) && $dbConfig->get('registry')->get('disabled') === '') 
                                    || $dbConfig->get('registry')->get('disabled') == true;
            if (!$dbRegistryDisabled) {
                // Save in registry
                Zend_Registry::set($dbKey, $db);
            }
        }
    }
    
    /**
     * Set the database adapter
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @param string|null $id
     * @return Zym_App_Resource_Db
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $db, $id)
    {
        $matches = $this->_adapterIdMatches($id);
        if (count($matches) && !empty($matches[1]) && !empty($matches[2])) {
            $id  = $matches[1];
            $num = $matches[2];
            $this->_dbAdapter[$id][$num] = $db;
        } else {
            $this->_dbAdapter[$id][] = $db;
        }

        return $this;
    }
    
    /**
     * Get the database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter($id = null)
    {
        $matches = $this->_adapterIdMatches($id);
        if (count($matches) && !empty($matches[1]) && !empty($matches[2])) {
            $id  = $matches[1];
            $num = $matches[2];
            $db  = $this->_dbAdapter[$id][$num];
        } else {
            $db = reset($this->_dbAdapter[$matches[0]]);
        }
        
        return $db;
    }
    
    /**
     * Get matches for an adapter id
     *
     * @param string $id
     * @return array
     */
    protected function _adapterIdMatches($id)
    {
        // TODO: Check regex for bugs
        $matches = array();
        preg_match('/(.*)-(\d*)/', $id, $matches);
        return $matches;
    }
    
    /**
     * Make a db key
     *
     * @param string $id
     * @return string
     */
    protected function _makeDbKey($id)
    {
        $matches = $this->_adapterIdMatches($id);
        if (count($matches) && !empty($matches[1]) && !empty($matches[2])) {
            // Make sure it does not exist
            if (isset($this->_dbAdapter[$id][$matches[2]])) {
                throw new Zym_App_Resource_Exception(
                    "Database connection \"$id\" exists, it cannot be overwritten"
                );
            } else {
                $id = $matches[1];
                $num = $matches[2];
                $key = "{$id}-{$num}";
            }
        } else if (isset($this->_dbAdapter[$id])) {
            $num = count($this->_dbAdapter[$id]);
            $key = "{$id}-{$num}";
        } else {
            $key = $id;
        }
        
        return $key;
    }
    
    /**
     * Setup profiler
     *
     * @param Zend_Config $profilerConfig
     * @param Zend_Db_Adapter_Abstract $db
     */
    protected function _setupProfiler(Zend_Config $profilerConfig, Zend_Db_Adapter_Abstract $db)
    {
        // Get profiler obj
        $profiler = $db->getProfiler();
        
        // Handle profiler elapsed secs filter
        if ($profilerConfig->get('filter')->get('elapsed_secs')) {
            $profiler->setFilterElapsedSecs($profilerConfig->get('filter')->get('elapsed_secs'));
        }
        
        // Handle profiler query type filter
        if ($profilerConfig->get('filter')->get('query_type')) {
            // TODO: Remove eval() hack used for logical OR of values 
            $profiler->setFilterQueryType(eval("return {$profilerConfig->get('filter')->get('query_type')};"));
        }
    }
    
    /**
     * Setup tables
     *
     * @param Zend_Config $dbConfig
     * @param Zend_Db_Adapter_Abstract $db
     */
    protected function _setupTables(Zend_Config $dbConfig, Zend_Db_Adapter_Abstract $db)
    {
        // Set default adapter for tables
        $tableDefaultAdapters = (is_string($dbConfig->get('set_default_adapter')))
                                ? $dbConfig->get('set_default_adapter')
                                : $dbConfig->get('set_default_adapter')->toArray();
                                
        foreach ((array) $tableDefaultAdapters as $class) {
            call_user_func_array(array($class, 'setDefaultAdapter'), array($db));
        }
        
        // Set metadata cache instance
        // TODO: Zend_Db_Table_Abstract::setDefaultMetadataCache();
    }
}