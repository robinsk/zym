<?php
/**
 * Zym Framework
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
 * @category Zym
 * @package Zym_Log
 * @subpackage Writer
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @link http://www.spotsec.com
 */

/**
 * @see Zend_Log_Writer_Abstract
 */
require_once 'Zend/Log/Writer/Abstract.php';

/**
 * A Zend_Log Syslog writer
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Log
 * @subpackage Writer
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
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