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
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * Setup session
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Session extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'auto_start' => true,
            'config'     => array(
                'save_path' => 'session',
                'name'      => 'SID'
            )
        )
    );

    /**
     * Setup db
     *
     */
    public function setup(Zend_Config $config)
    {
        $sessionConfig = $config->get('config');
        
        // Setup config
        Zend_Session::setOptions($sessionConfig->toArray());
        
        // save_path handler
        $this->_prependSavePath($sessionConfig);
        
        // Autostart session?
        if ($config->get('auto_start')) {
            // Start session
            Zend_Session::start();
        }
    }
    
    /**
     * Modify base dir for session path
     *
     * @param Zend_Config $config
     */
    protected function _prependSavePath(Zend_Config $config)
    {
        $app         = $this->getApp();
        $savePath    = $config->get('save_path');
        $configArray = array(
            'save_path' => $this->getApp()
                                ->getPath(Zym_App::PATH_DATA, $savePath)
        );

        Zend_Session::setOptions($configArray);
    }
}