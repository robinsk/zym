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
 * @subpackage Resource_Controller_Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zym_App_Resource_Controller_Plugin_Interface
 */
require_once 'Zym/App/Resource/Controller/Plugin/Interface.php';

/**
 * @see Zym_Controller_Plugin_ErrorHandler
 */
require_once 'Zym/Controller/Plugin/ErrorHandler.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource_Controller_Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Controller_Plugin_ErrorHandler implements Zym_App_Resource_Controller_Plugin_Interface
{   
    /**
     * Return errorHandler
     *
     * @param  Zend_Config $config
     * @return Zym_Controller_Plugin_ErrorHandler
     */
    public function getPlugin(Zend_Config $config = null)
    {
        if ($config instanceof Zend_Config) {
            $options = $config->toArray();
        } else {
            $options = array();
        }
        
        $plugin = new Zym_Controller_Plugin_ErrorHandler($options);
        return $plugin;
    }
}