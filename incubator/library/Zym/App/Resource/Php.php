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
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * Setup php environment
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
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
            'error_reporting' => 8191 // E_ALL | E_STRICT
        ),
        
        Zym_App::ENV_DEFAULT     => array(
            'date' => array(
                'force_default_timezone' => false
            ),
            
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
        $this->_recurseConfig($config);
        
        // Set/Force default timezone?
        $this->_forceDefaultTimezone($config->date);
        
        // Set include path
        $this->_setIncludePath($config);
    }

    /**
     * Recursively iterate through zend config elements
     * if one is a value, set it through ini_set
     *
     * @todo Use a better algorithm since this requires a lot of cpu
     * @param Zend_Config $config
     * @param string      $location Current dimension
     */
    protected function _recurseConfig(Zend_Config $config, $location = null)
    {
        foreach ($config as $namespace => $child) {
            $key = $location . '.' . $namespace;

            // Skip if it's not a php config item
            if (in_array($key, $this->_customConfigMap)) {
                continue;
            }
            
            // Go deeper
            if ($child instanceof Zend_Config) {
                $this->_recurseConfig($child, $key);
                continue;
            }
            
            ini_set($key, $child);
        }
    }
    
    /**
     * Force default timezone
     *
     * @param Zend_Config $config Date obj
     */
    protected function _forceDefaultTimezone(Zend_Config $config)
    {
        if ((bool) $config->force_default_timezone) {
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
        if (!$config->include_path) {
            return;
        }
        
        // Handle array
        if ($config->include_path instanceof Zend_Config) {
            $paths = implode(PATH_SEPARATOR, $config->include_path->toArray());
        } else {
            // Normalize include path string
            $paths = str_replace(array(':', ';'), PATH_SEPARATOR, $config->include_path);
        }
        
        set_include_path($paths . get_include_path());
    }
    
    /**
     * Kill magic quotes gpc
     * 
     * Strips the crap that magic quotes does...
     * 
     * @todo Discuss whether or not to include this hack...
     * @param Zend_Config $config
     */
    protected function _undoMagicQuotesGpc(Zend_Config $config)
    {
        $isMagicQuotesGpc = (isset($config->magic_quotes_gpc) && !$config->magic_quotes_gpc);
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