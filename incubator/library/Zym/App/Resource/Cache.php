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
 * @see Zym_Cache
 */
require_once 'Zym/Cache.php';

/**
 * Cache component configuration
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Cache extends Zym_App_Resource_Abstract
{   
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEVELOPMENT => array(
            'frontend' => array(
                'core' => array(
                    'caching' => false
                ),
                
                'output' => array(
                    'caching' => false
                ),
                
                'function' => array(
                    'caching' => false
                ),
                
                'class' => array(
                    'caching' => false
                ),
                
                'file' => array(
                    'caching' => false
                ),
                
                'page' => array(
                    'caching' => false
                )
            )
        ),
        
        Zym_App::ENV_DEFAULT     => array(
            'default_backend' => 'file',
        
            'frontend' => array(
                'core' => array(
                    'automatic_serialization' => true
                )
            ),
            
            'backend' => array(
                'file' => array(
                    'cache_dir' => 'cache' // Relative to Zym_App::PATH_TEMP
                ),
                
                'sqlite' => array(
                    'cache_db_complete_path' => 'cache/cache.sqlite'
                )
            )
        )
    );


    /**
     * Setup mail
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {
        Zym_Cache::setConfig($config);
        
        // Set file cache dir
        $this->_prependTempPath($config->backend);        
    }
    
    /**
     * Prepend temp path to paths
     *
     * @param Zend_Config $config
     */
    protected function _prependTempPath(Zend_Config $config)
    {
        // File
        $fileOptions = Zym_Cache::getBackendOptions('file');
        if (isset($fileOptions['cache_dir'])) {
            $fileOptions = $this->getApp()->getPath(Zym_App::PATH_TEMP, $fileOptions['cache_dir']);
        }
        
        Zym_Cache::setBackendOptions('file', $fileOptions);
        
        // Sqlite
        $sqliteOptions = Zym_Cache::getBackendOptions('sqlite');
        if (isset($sqliteOptions['cache_db_complete_path'])) {
            $sqliteOptions = $this->getApp()->getPath(Zym_App::PATH_TEMP, $sqliteOptions['cache_db_complete_path']);
        }
        
        Zym_Cache::setBackendOptions('sqlite', $sqliteOptions);
    }
}