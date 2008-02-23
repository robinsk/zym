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
 * @see Zym_App_Resource_Controller_Plugin_Interface
 */
require_once 'Zym/App/Resource/Controller/Plugin/Interface.php';

/**
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource_Controller_Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
abstract class Zym_App_Resource_Controller_Plugin_Abstract implements Zym_App_Resource_Controller_Plugin_Interface
{
    /**
     * Config
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * Set config
     *
     * @param Zend_Config $config
     * @return Zym_App_Resource_Controller_Plugin_Abstract
     */
    public function setConfig(Zend_Config $config)
    {
        $this->_config = $config;
        return $this;
    }
    
    /**
     * Get config
     *
     * @return Zend_Config
     */
    public function getConfig()
    {        
        return $this->_config;
    }
}