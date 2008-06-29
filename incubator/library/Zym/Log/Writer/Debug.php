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
 * A Zend_Log debugging writer similar to the Mock writer
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
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