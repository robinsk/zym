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
require_once('Zym/App/Resource/Abstract.php');

/**
 * @see Zend_Session
 */
require_once('Zend/Session.php');

/**
 * Setup session
 * 
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App_Resource_Session extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        'auto_start' => true,
        'config' => array()
    );

    /**
     * Setup db
     *
     */
    public function setup(Zend_Config $config)
    {
        // Skip if session was already started
        if (Zend_Session::isStarted()) {
            return;
        }

        // Setup config
        Zend_Session::setOptions($config->config->toArray());

        // Autostart session?
        if ($config->auto_start) {
            // Start session
            Zend_Session::start();
        }
    }
}