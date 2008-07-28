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
 * Setup php environment
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Php extends Zym_App_Resource_Abstract
{
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEVELOPMENT => array(
            'error_reporting' => 8191, // E_ALL | E_STRICT
            'display_errors'  => true
        ),

        Zym_App::ENV_DEFAULT     => array(
            'date' => array(
                'force_default_timezone' => false,
                'timezone' => 'Europe/London'
            ),

            'error_reporting' => 341, // E_PARSE | E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR | E_USER_ERROR
            'display_errors' => false,

            'include_path' => array()
        )
    );

    /**
     * Array of items to skip from ini_set()
     *
     * @todo make this format similar to the config array instead
     * @var array
     */
    protected $_customConfigMap = array(
        'date.force_default_timezone',
        'include_path'
    );

    /**
     * Set as high priority to load first in the dispatch
     *
     * @var integer
     */
    protected $_priority = self::PRIORITY_HIGH;

    /**
     * Setup
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {
        // Parse for php config and set them
        $this->_parseConfig($config);

        // Set/Force default timezone?
        $this->_forceDefaultTimezone($config->get('date'));

        // Set include path
        $this->_setIncludePath($config);

        // Kill the Evil
        $this->_undoMagicQuotesGpc($config);
    }

    /**
     * Recursively iterate through zend config elements
     * if one is a value, set it through ini_set
     *
     * @todo Use a better algorithm since this requires a lot of cpu
     * @param Zend_Config $config
     * @param string      $location Current dimension
     */
    protected function _parseConfig(Zend_Config $config, $location = null)
    {
        foreach ($config as $namespace => $child) {
            $key = (!empty($location) ? $location . '.' : '') . $namespace;

            // Skip if it's not a php config item
            if (in_array($key, $this->_customConfigMap)) {
                continue;
            }

            // Go deeper
            if ($child instanceof Zend_Config) {
                $this->_parseConfig($child, $key);
                continue;
            }

            ini_set($key, $child);
        }

        /* Snippet of code from discussion
            $queue = array();

            array_push(array('location' => null,
                             'config'   => $config));

            while(!empty($queue)) {
                $tmpNode = array_pop($queue);
                $tmpConfig = $tmpNode['config'];

                $key = $tmpNode['location'] . '.' . $tmpConfig->namespace;

                // Skip if it's not a php config item
                if (in_array($key, $this->_customConfigMap)) {
                    continue;
                }

                // Go deeper
                if ($tmpConfig->child instanceof Zend_Config) {
                    array_push(array('location' => $key,
                                     'config'   => $tmpConfig->child));
                    continue;
                }

                ini_set($key, $tmpConfig->child);
            }
        */
    }

    /**
     * Force default timezone
     *
     * @param Zend_Config $config Date obj
     */
    protected function _forceDefaultTimezone(Zend_Config $config)
    {
        // Support <force_default_timezone />
        if ($config->get('force_default_timezone') || $config->get('force_default_timezone')  === '') {
            $timezone = @date_default_timezone_get();

            // Set default timezone
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Parse include_path element and normalize with PATH_SEPARATORS
     *
     * If an an array (Zend_Config) is found, convert those with
     * PATH_SEPARATORS and shift them infront of the current include path
     *
     * @param Zend_Config $config
     */
    protected function _setIncludePath(Zend_Config $config)
    {
        // Setting the include path is an expensive operation
        // Only do it when needed
        if (!$config->get('include_path')) {
            return;
        }

        // Handle array
        if ($config->get('include_path') instanceof Zend_Config) {
            $paths = implode(PATH_SEPARATOR, $config->get('include_path')->toArray());
        } else {
            // Normalize include path string
            $paths = str_replace(array(':', ';'), PATH_SEPARATOR, $config->get('include_path'));
        }

        set_include_path($paths . get_include_path());
    }

    /**
     * Kill magic quotes gpc
     *
     * Strips the crap that magic quotes does...
     * Reads the magic_quotes_gpc value from the config to determine whether to
     * enable or disable hack
     *
     * @todo Discuss whether or not to include this hack...
     * @param Zend_Config $config
     */
    protected function _undoMagicQuotesGpc(Zend_Config $config)
    {
        $isMagicQuotesGpc = (isset($config->magic_quotes_gpc) && !$config->get('magic_quotes_gpc'));
        if ($isMagicQuotesGpc && get_magic_quotes_gpc()) {
            $in = array(&$_GET, &$_POST, &$_COOKIE);
            while (list($k,$v) = each($in)) {
                foreach ($v as $key => $val) {
                    if (!is_array($val)) {
                        $in[$k][$key] = stripslashes($val);
                        continue;
                    }
                    $in[] =& $in[$k][$key];
                }
            }

            unset($in);
        }
    }
}