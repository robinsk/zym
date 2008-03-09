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
 * @see Zend_Log
 */
require_once 'Zend/Log.php';

/**
 * @see Zend_Log_Writer_Db
 */
require_once 'Zend/Log/Writer/Db.php';

/**
 * @see Zend_Log_Writer_Stream
 */
require_once 'Zend/Log/Writer/Stream.php';

/**
 * @see Zend_Log_Writer_Null
 */
require_once 'Zend/Log/Writer/Null.php';

/**
 * Logger
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Log extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'writer' => array(
                'database' => array(
                    'enabled' => false,
                    'table' => 'logs',
                    'key' => 'db'
                ),
    
                'stream' => array(
                    'enabled' => false,
                    'stream' => 'application.log',
                    'mode' => 'a',
                    'formatter' => array(/*
                        'name' => 'Zend_Log_Formatter_Simple',
                        'params' => array(
                            '%timestamp% %ipAddr% %priorityName% (%priority%): %message%'
                        )*/
                    )
                ),
    
                'syslog' => array(
                    'enabled' => false,
                )
            ),
    
            'filter' => array(
                'priority' => array(
                    'enabled' => true,
                    'level' => Zend_Log::WARN
                )
            )
        )
    );

    /**
     * Setup db
     *
     */
    public function setup(Zend_Config $config)
    {
        // Get resource config
        $writerConfig = $config->writer;
        $filterConfig = $config->filter;

        // Log instance
        $log = new Zend_Log();

        // Enable database writer?
        if ($writerConfig->database->enabled) {
            $db = $this->getRegistry()->get($writerConfig->database->key, 'Zend_Db_Adapter_Abstract');
            
            // Make sure it's a right object
            if (!$db instanceof Zend_Db_Adapter_Abstract) {
                require_once('Zym/App/Resource/Exception.php');
                throw new Zym_App_Resource_Exception('Resource ' . get_class($this) 
                    . ' requires the internal registry item "db" to contain a Zend_Db_Adapter_Abstract object');
            }
            
            $dbWriter = new Zend_Log_Writer_Db($db, $writerConfig->database->table);
            $log->addWriter($dbWriter);
        }

        // Enable stream writer?
        if ($writerConfig->stream->enabled) {
            $streamWriter = new Zend_Log_Writer_Stream($writerConfig->stream->stream, $writerConfig->stream->mode);
            $streamWriter->setFormatter(new Zend_Log_Formatter_Simple('%timestamp% %ipAddr% %priorityName% (%priority%): %message%' . PHP_EOL));
            $log->addWriter($streamWriter);
        }

        // Enable null writer
        $log->addWriter(new Zend_Log_Writer_Null());

        // Setup filters
        // Priority filter
        if ($filterConfig->priority->enabled) {
            $log->addFilter(new Zend_Log_Filter_Priority((int) $filterConfig->priority->level));
        }

        // Setup events
        // Log user ip address
        //$log->setEventItem('ipAddr', $_SERVER['REMOTE_ADDR']);
    }
}
$a = array(
    'writer' => array(
        'foo' => array(
            'filter'
        )
    ),
    'filter' => array(),
    'event'  => array()
);