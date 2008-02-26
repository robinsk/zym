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
 * @subpackage ExceptionHandler
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */


/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage ExceptionHandler
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_App_ExceptionHandler_Abstract
{
    /**
     * Application
     *
     * @var Zym_App
     */
    protected $_app = null;
    
    /**
     * Get Application
     *
     * @return Zym_App
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * Set Application
     *
     * @param Zym_App $application
     * @return Zym_App_Resource_Abstract
     */
    public function setApp(Zym_App $application)
    {
        $this->_app = $application;
        return $this;
    }

    /**
     * Get the internal bootstrap registry
     *
     * @param string $index Shortcut to $this->getRegistry()->get($index)
     * @return Zym_App_Registry
     */
    public function getRegistry($index = null)
    {
        $registry = $this->getApplication()->getRegistry();
        if ($index !== null) {
            return $registry->get($index);
        }

        return $registry;
    }

    /**
     * Handle bootstrap exceptions
     *
     * @param Exception $e
     */
    abstract public function handle(Exception $e);
}