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
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * Zym_Log_Exception
 */
require_once('Zym/Log/Exception.php');

/**
 * Zend_Log_Writer_Abstract
 */
require_once('Zend/Log/Writer/Abstract.php');

/**
 * A Zend_Log writer for integration with Doctrine (http://PHPDoctrine.org)
 * 
 * This writer was modelled after Doctrine_Log_Writer_Db which itself was
 * derived from Zend_Log_Writer_Db. ;)
 *
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Log
 * @subpackage Writer
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Log_Writer_Doctrine extends Zend_Log_Writer_Abstract
{
    /**
     * Doctrine_Table instance
     *
     * @var Doctrine_Table
     */
    protected $_table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    protected $_columnMap;

    /**
     * Class constructor
     *
     * @param string|Doctrine_Table $table
     * @param array $columnMap
     */
    public function __construct($table, array $columnMap = null)
    {
        if (!$table instanceof Doctrine_Table) {
            // Doctrine sanity check
            if (!class_exists('Doctrine')) {
                throw new Zym_Log_Exception(get_class() . ' requires the Doctrine library');
            }
            
            $table = Doctrine::getTable($table);
        }
        
        $this->_table = $table;
        $this->_columnMap = $columnMap;
    }

    /**
     * Formatting is not possible on this writer
     */
    public function setFormatter($formatter)
    {
        throw new Zym_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Remove reference to database table
     *
     * @return void
     */
    public function shutdown()
    {
        unset($this->_table);
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        if ($this->_table === null) {
            throw new Zym_Log_Exception('Database adapter instance has been removed by shutdown');
        }

        if (!$this->_columnMap) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event[$fieldKey];
            }
        }
        
        $record = $this->_table->create($dataToInsert);
        $record->save();
    }
}
