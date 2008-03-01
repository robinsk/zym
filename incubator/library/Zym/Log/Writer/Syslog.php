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
 * @package Zym_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Log_Writer_Abstract
 */
require_once 'Zend/Log/Writer/Abstract.php';

/**
 * A Zend_Log Syslog writer
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Log_Writer_Syslog extends Zend_Log_Writer_Abstract
{
	/**
	 * Class constructor
	 *
	 * @param string $ident
	 * @param string $option
	 * @param string $facility
	 */
	public function __construct($ident, $option, $facility)
	{
		// Define syslog related constants
		define_syslog_variables();

		// Open connection to syslog
		openlog($ident, $option, $facility);
	}

    /**
     * Formatting is not possible on this writer
     */
    public function setFormatter($formatter)
    {
        throw new Zend_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
	protected function _write($event)
	{
		// Write to log
		syslog($event['priority'], "({$event['priorityName']}) " . $event['message']);
	}

	/**
	 * Perform syslog shutdown
	 *
	 */
	public function shutdown()
	{
		// Close connection
		closelog();
	}
}