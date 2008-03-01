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
 * @package Zym_Debug
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Log_Writer_Debug
 */
require_once 'Zym/Log/Writer/Debug.php';

/**
 * @see Zym_Timer_Manager
 */
require_once 'Zym/Timer/Manager.php';

/**
 * @see Zend_Debug
 */
require_once 'Zend/Debug.php';

/**
 * @see Zend_Log
 */
require_once 'Zend/Log.php';

/**
 * Debugging component
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Debug
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Debug extends Zend_Debug
{
    /**
     * Instance
     * 
     * Implements singleton pattern
     *
     * @var Zym_Debug
     */
    protected static $_instance;
    
    /**
     * Timer manager instance
     *
     * @var Zym_Timer_Manager
     */
    protected $_timerManager;
    
    /**
     * Zend_Log instance
     *
     * @var Zend_Log
     */
    protected $_log;
    
    /**
     * Debug log writer
     * 
     * Used to obtain debugging data
     *
     * @var Zym_Log_Writer_Debug
     */
    protected $_logWriter;
    
    /**
     * Enforce singleton pattern
     *
     */
    protected function __construct()
    {}
    
    /**
     * Prevent cloning, enforce singleton pattern
     *
     */
    protected function __clone()
    {}
    
    /**
     * Get instance
     * 
     * Implements the singleton pattern
     *
     * @return Zym_Debug
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
            
            // Setup instance
            self::_init();
        }

        return self::$_instance;
    }
    
    /**
     * Init process
     *
     */
    protected static function _init()
    {
        /**
         * @var Zym_Debug
         */
        $debug = self::getInstance();
        $debug->setTimerManager(new Zym_Timer_Manager());
        $debug->setLogWriter();
        $debug->setLog(new Zend_Log());
        $debug->getLog()->addWriter($debug->getLogWriter());
    }
    
    /**
     * Setup MVC integration
     *
     */
    public static function startMvc()
    {
        Zend_Controller_Front::getInstance()->registerPlugin(new Zym_Debug_Controller_Plugin_Debug());
    }
    
    /**
     * Set the timer manager
     *
     * @param Zym_Timer_Manager $manager
     * @return Zym_Debug
     */
    public function setTimerManager(Zym_Timer_Manager $manager)
    {
        $this->_timerManager = $manager;
        return $this;
    }
    
    /**
     * Get the timer manager instance
     *
     * @return Zym_Timer_Manager
     */
    public function getTimerManager()
    {
        return $this->_timerManager;
    }
    
    /**
     * Set debugging log writer
     *
     * @param string|Zym_Log_Writer_Debug $writer
     * @return Zym_Debug
     */
    public function setLogWriter($writer = 'Zym_Log_Writer_Debug')
    {
        if ($this->getLog() instanceof Zend_Log) {
            /**
             * @see Zym_Debug_Exception
             */
            require_once('Zym/Debug/Exception.php');
            throw new Zym_Debug_Exception(
                'Cannot set a log writer because Zend_Log is already setup'
            );
        }
        
        if (is_string($writer)) {
            /**
             * @see Zend_Loader
             */
            require_once('Zend/Loader.php');
            Zend_Loader::loadClass($writer);
            $writer = new $writer();
        }
        
        if (!$writer instanceof Zym_Log_Writer_Debug) {
            /**
             * @see Zym_Debug_Exception
             */
            require_once('Zym/Debug/Exception.php');
            throw new Zym_Debug_Exception(
                'The provided writer is not an instance of Zym_Log_Writer_Debug'
            );
        }
        
        $this->_logWriter = $writer;
        return $this;
    }
    
    /**
     * Get log writer
     *
     * @return Zym_Log_Writer_Debug
     */
    public function getLogWriter()
    {
        return $this->_logWriter;
    }
    
    
    /**
     * Set log instance
     *
     * @param Zend_Log $log
     * @return Zym_Debug
     */
    public function setLog(Zend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }
    
    /**
     * Get log instance
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        return $this->_log;
    }
    
    /**
     * Log message
     *
     * @param string $message
     * @param integer $priority
     */
    public static function log($message, $priority = Zend_Log::DEBUG)
    {
        self::getInstance()->getLog()->log($message, $priority);
    }
    
    /**
     * Get a timer object
     *
     * @param string $name
     * @param string $group
     * @return Zym_Debug_Timer
     */
    public static function getTimer($name = null, $group = null)
    {
        return self::getInstance()->getTimerManager()->getTimer($name, $group);
    }
}