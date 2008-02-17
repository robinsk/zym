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
 * Zym_App_Resource_Controller_Plugin_Abstract
 */
require_once('Zym/App/Resource/Controller/Plugin/Abstract.php');

/**
 * Zym_Controller_Plugin_ErrorHandler
 */
require_once('Zym/Controller/Plugin/ErrorHandler.php');

/** 
 * @author Geoffrey Tran
 * @license GNU Public License
 * @category SpotSec
 * @package Zym_App_Resource_Controller
 * @subpackage Plugin
 * @copyright Copyright 2006, SpotSec Networks
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