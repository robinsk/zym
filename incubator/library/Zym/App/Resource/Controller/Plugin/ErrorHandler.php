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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zym_App_Resource_Controller_Plugin_Abstract
 */
require_once 'Zym/App/Resource/Controller/Plugin/Abstract.php';

/**
 * @see Zym_Controller_Plugin_ErrorHandler
 */
require_once 'Zym/Controller/Plugin/ErrorHandler.php';

/**
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource_Controller_Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App_Resource_Controller_Plugin_ErrorHandler extends Zym_App_Resource_Controller_Plugin_Abstract 
{   
    /**
     * Return errorHandler
     *
     * @return Zym_Controller_Plugin_ErrorHandler
     */
    public function getPlugin()
    {
        if ($this->getConfig() instanceof Zend_Config) {
            $options = $this->getConfig()->toArray();
        }
        
        $plugin = new Zym_Controller_Plugin_ErrorHandler($options);
        return $plugin;
    }
}