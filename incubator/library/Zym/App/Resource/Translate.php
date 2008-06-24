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
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */
 
/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * Setup translation
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Translate extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_PRODUCTION => array(
            'cache' => true
        ),
        
        Zym_App::ENV_DEFAULT => array(
            'cache'   => false,
            'adapter' => 'tmx',
            'data'    => 'locale', // Relative to data directory
            'locale'  => 'auto',
            'options' => array(),
        
            'registry' => array(
                'enabled' => true,
                'key'     => 'Zend_Translate'
            )
        )
    );

    /**
     * Setup
     *
     * @return void
     */
    public function setup(Zend_Config $config)
    {
        // Setup Cache
        if ($config->get('cache')) {
            $cache = Zym_Cache::factory('Core');
            Zend_Translate::setCache($cache);
        }
        
        $adapter = $config->get('adapter');
        $data    = $this->_parseDataPath($config->get('data'));
        $locale  = $config->get('locale');
        $options = $this->_parseOptions($config->get('options')->toArray());

        $translate = new Zend_Translate($adapter, $data, null, $options);
        
        // Weird Zend_Translate issues
        // We cannot set a locale in the constructor
        $translate->getAdapter()->setLocale($locale);
        
        if ((bool) $config->get('registry')->get('enabled')) {
            /**
             * @see Zend_Registry
             */
            require_once 'Zend/Registry.php';
            
            Zend_Registry::set($config->get('registry')->get('key'), $translate);
        }
    }
    
    /**
     * Parse data path and make it relative to data dir
     *
     * @param mixed $data
     * @return mixed
     */
    protected function _parseDataPath($data)
    {
        if (is_string($data)) {
            // Change path relative to data dir
            return $this->getApp()->getPath(Zym_App::PATH_DATA, $data);
        }
        
        if ($data instanceof Zend_Config) {
            return $data->toArray();
        }
        
        return $data;
    }
    
    /**
     * Parse options
     *
     * @param array $options
     */
    protected function _parseOptions(array $options)
    {
        if (isset($options['scan'])) {
            $options['scan'] = (int) $options['scan'];
        }
        
        return $options;
    }
}