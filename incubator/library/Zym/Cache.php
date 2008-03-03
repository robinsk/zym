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
 * @package Zym_Cache
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * Zym/ Cache
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Cache
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_Cache extends Zend_Cache
{
    /**
     * Default backend
     *
     * @var string|Zend_Cache_Backend
     */
    protected static $_defaultBackend;

    /**
     * Frontend config
     *
     * <code>
     * array(
     *     'core' => array()
     * )
     * </code>
     *
     * @var array
     */
    protected static $_frontendOptions = array();

    /**
     * Backend config
     *
     * <code>
     * array(
     *     'apc' => array()
     * )
     * </code>
     *
     * @var array
     */
    protected static $_backendOptions = array();

    /**
     * Set default cache frontend and backend config
     *
     * @param Zend_Config $config
     */
    public static function setConfig(Zend_Config $config)
    {
        if (!empty($config->default_backend)) {
            self::setDefaultBackend($config);
        }

        // Set config
        $map = array('frontend', 'backend');
        foreach ($map as $item) {
            if (isset($config->{$item})) {
                foreach ($config->{$item} as $end => $config) {
                    $setConfigFunc = 'set' . ucfirst($item) . 'Options';
                    $this->$setConfigFunc($end, $config->toArray());
                }
            }
        }
    }

    /**
     * Set default backend
     *
     * @param string|Zend_Cache_Backend $backend
     */
    public static function setDefaultBackend($backend)
    {
        self::$_defaultBackend = $backend;
    }

    /**
     * Get default backend
     *
     * @return string|Zend_Cache_Backend
     */
    public static function getDefaultBackend()
    {
        return self::$_defaultBackend;
    }

    /**
     * Set frontend options
     *
     * @param string $frontend
     * @param array $options
     */
    public static function setFrontendOptions($frontend, $options = array())
    {
        self::$_frontendOptions[$frontend] = $options;
    }

    /**
     * Get frontend options
     *
     * @param string $frontend
     * @return array
     */
    public static function getFrontendOptions($frontend)
    {
        if (!isset(self::$_frontendOptions[$frontend]) || !is_array(self::$_frontendOptions[$frontend])) {
            $config = array();
        } else {
            $config = self::$_frontendOptions[$frontend];
        }
        
        return $config;
    }

    /**
     * Set backend options
     *
     * @param string $backend
     * @param array $options
     */
    public static function setBackendOptions($backend, array $options = array())
    {
        self::$_backendOptions[$backend] = $options;
    }

    /**
     * Get backend options
     *
     * @param string $backend
     * @return array
     */
    public static function getBackendOptions($backend)
    {
        if (!isset(self::$_backendOptions[$backend]) || !is_array(self::$_backendOptions[$backend])) {
            $config = array();
        } else {
            $config = self::$_backendOptions[$backend];
        }
        
        return $config;
    }

    /**
     * Factory
     *
     * @param string $frontend frontend name
     * @param string $backend backend name Leave as null for default backend
     * @param array $frontendOptions associative array of options for the corresponding frontend constructor
     * @param array $backendOptions associative array of options for the corresponding backend constructor
     */
    public static function factory($frontend, $backend = null, array $frontendOptions = null, array $backendOptions = null)
    {
        if ($backend === null) {
            $backend = self::getDefaultBackend();
        }
        
        // We can't use a Zend_Cache_Backend instance
        if ($backend instanceof Zend_Cache_Backend) {
            $backend = get_class($backend);
        }

        $frontendOptions = self::_arrayMergeRecursiveOverwrite(self::getFrontendOptions($frontend), 
                                                              (array) $frontendOptions);
        
        $backendOptions  = self::_arrayMergeRecursiveOverwrite(self::getBackendOptions($backend),
                                                              (array) $backendOptions);

        return parent::factory($frontend, $backend, $frontendOptions, $backendOptions);
    }

    /**
     * Merge two arrays recursively, overwriting keys of the same name name
     * in $array1 with the value in $array2.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected static function _arrayMergeRecursiveOverwrite($array1, $array2)
    {
        if (is_array($array1) && is_array($array2)) {
            foreach ($array2 as $key => $value) {
                if (isset($array1[$key])) {
                    $array1[$key] = self::_arrayMergeRecursiveOverwrite($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        } else {
            $array1 = $array2;
        }
        return $array1;
    }
}
