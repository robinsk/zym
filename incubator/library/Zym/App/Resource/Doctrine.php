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
 * Doctrine
 * 
 * @link http://www.phpdoctrine.org/
 * 
 * @author Robin Skoglund
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Doctrine extends Zym_App_Resource_Abstract
{
    /**
     * Doctrine path configuration
     *
     * @var array
     */
    protected $_pathConfig = array(
        'data_fixtures_path'  =>  null,
        'models_path'         =>  null,
        'migrations_path'     =>  null,
        'sql_path'            =>  null,
        'yaml_schema_path'    =>  null
    );
    
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            // all paths are relative to app's home directory
            'path_config' => array(
                'data_fixtures_path'  =>  'data/doctrine/data/fixtures',
                'models_path'         =>  'application/models',
                'migrations_path'     =>  'data/doctrine/migrations',
                'sql_path'            =>  'data/doctrine/data/sql',
                'yaml_schema_path'    =>  'data/doctrine/schema'
            ),
            
            'charset'    => null,
            
            'connection' => array()
        )
    );

    /**
     * Setup Doctrine
     *
     * @return void
     */
    public function setup(Zend_Config $config)
    {
        if (is_array($config->path_config)) {
            $this->setPathConfig($config->path_config);
        } elseif ($config->path_config instanceof Zend_Config) {
            $this->setPathConfig($config->path_config->toArray());
        }
        
        if ($charset = $config->get('charset')) {
            $listener = new Zym_App_Resource_Doctrine_ConnectionListener();
            $listener->setCharset($charset);
            Doctrine_Manager::getInstance()->addListener($listener);
        }
        
        // determine if config is for a single-db or a multi-db site
        $connections = $config->connection instanceof Zend_Config
                     ? $config->connection->toArray()
                     : (array) $config->connection;

        // add connection(s) to doctrine
        foreach ($connections as $name => $connection) {
            if ($connection instanceof Zend_Config) {
                $connection = $connection->toArray();
            }
            
            if (is_string($name)) {
                Doctrine_Manager::connection($connection, $name);
            } else {
                Doctrine_Manager::connection($connection);
            }
        }
    }
    
    /**
     * Sets Doctrine path configuration
     *
     * @param array $config
     */
    public function setPathConfig(array $config)
    {
        $home = rtrim(realpath($this->getApp()->getHome()), '/') . '/';
        
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->_pathConfig)) {
                $this->_pathConfig[$key] = $home . ltrim($value, '/');
            }
        }
    }
    
    /**
     * Returns Doctrine path configuration
     *
     * @return array
     */
    public function getPathConfig()
    {
        return $this->_pathConfig;
    }
}
