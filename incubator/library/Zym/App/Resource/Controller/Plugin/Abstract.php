<?php
/**
 * SpotSec Framework
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category SpotSec
 * @package Zym_App_Resource_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2006 SpotSec Networks
 * @license GNU Public License
 * @link http://www.spotsec.com
 */

/**
 * Zym_App_Resource_Controller_Plugin_Interface
 */
require_once('Zym/App/Resource/Controller/Plugin/Interface.php');

/** 
 * @author Geoffrey Tran
 * @license GNU Public License
 * @category SpotSec
 * @package Zym_App_Resource_Controller
 * @subpackage Plugin
 * @copyright Copyright 2006, SpotSec Networks
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
     * Return a controller plugin
     *
     * @return Zend_Controller_Plugin_Abstract
     */
    public function getPlugin()
    {}
    
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