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
 * @see Zend_Locale
 */
require_once 'Zend/Locale.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Setup default locale
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Locale extends Zym_App_Resource_Abstract
{
    /**
     * Priority
     *
     * @var integer
     */
    protected $_priority = self::PRIORITY_HIGH;

    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_PRODUCTION => array(
            'cache' => true
        ),

        Zym_App::ENV_DEFAULT => array(
            'class'   => 'Zend_Locale',
            'cache'   => false,
            'default' => null,
            'locale'  => null,

            'registry' => array(
                'enabled' => true,
                'key'     => 'Zend_Locale'
            )
        )
    );

    /**
     * Locale class
     *
     * @var string
     */
    protected $_class;

    /**
     * PreSetup
     *
     * @param Zend_Config $config
     */
    public function preSetup(Zend_Config $config)
    {
        // Load locale class
        $this->_loadLocaleClass($config->get('class'));
    }

    /**
     * Setup
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {
        // Set cache
        $this->_setCache($config);

        // Set default locale
        $this->_setDefault($config);

        // Registry key to set default application locale
        $this->_setRegistry($config);
    }

    /**
     * Load locale class
     *
     * @param string $class
     */
    protected function _loadLocaleClass($class)
    {
        Zend_Loader::loadClass($class);
        $this->_class = $class;
    }

    /**
     * Set cache
     *
     * @param Zend_Config $config
     */
    protected function _setCache(Zend_Config $config)
    {
        if (!$config->get('cache')) {
            return;
        }

        /**
         * @see Zym_Cache
         */
        require_once 'Zym/Cache.php';
        $cache = Zym_Cache::factory('Core');
        call_user_func(array($this->_class, 'setCache'), $cache);
    }

    /**
     * Set default locale
     *
     * @param Zend_Config $config
     */
    protected function _setDefault(Zend_Config $config)
    {
        $locale = $config->get('default');
        if (!empty($locale)) {
            call_user_func(array($this->_class, 'setDefault'), $locale);
        }
    }

    /**
     * Set registry key
     *
     * @param Zend_Config $config
     */
    protected function _setRegistry(Zend_Config $config)
    {
        if ((bool) $config->get('registry')->get('enabled')) {
            /**
             * @see Zend_Registry
             */
            require_once 'Zend/Registry.php';

            $locale = new Zend_Locale($config->get('locale'));
            Zend_Registry::set($config->get('registry')->get('key'), $locale);
        }
    }
}