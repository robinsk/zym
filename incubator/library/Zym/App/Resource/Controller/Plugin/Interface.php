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
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource_Controller_Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
interface Zym_App_Resource_Controller_Plugin_Interface 
{    
    /**
     * Get controller plugin
     *
     * @param Zend_Config $config
     * @return Zend_Controller_Plugin_Abstract
     */
    public function getPlugin(Zend_Config $config = null);
}