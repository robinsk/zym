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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * Setup timezone
 * 
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App_Resource_Php extends Zym_App_Resource_Abstract
{
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        'date' => array(
            'force_default_timezone' => false
        )
    );
    
    protected $_customConfigMap = array(
        'date' => array(
            'force_default_timezone'
        )
    );

    /**
     * Set default timezone
     *
     */
    public function setup(Zend_Config $config)
    {
        foreach ($config as $namespace => $child) {
        	ini_set();
        }
        
        $timezone = $config->timezone;
        
        // Use default timezone
        if ((bool) $config->force_default) {
            $timezone = @date_default_timezone_get();
        }

        // Set default timezone
        date_default_timezone_set($timezone);
    }    
}