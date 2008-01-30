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
 * A Zend_Log debugging writer similar to the Mock writer
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Log
 * @subpackage Writer
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Log_Writer_Debug extends Zend_Log_Writer_Abstract
{
    /**
     * Logged events
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Writer shutdown status
     *
     * @var boolean
     */
    protected $_shutdown = false;

    /**
     * Formatting is not possible on this writer
     */
    public function setFormatter($formatter)
    {
        throw new Zym_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Get the logged events
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * Write a message to the log.
     *
     * @param  array $event log data event
     * @return void
     */
	protected function _write($event)
	{
        if ($this->_shutdown == true) {
            throw new Zym_Log_Exception(
                'Database adapter instance has been removed by shutdown'
            );
        }

        $this->_events[] = $event;
	}

	/**
	 * Perform shutdown
	 *
	 */
	public function shutdown()
	{
        $this->_shutdown = true;
	}
}
