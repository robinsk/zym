<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Model_Data_Interface
 */
require_once 'Zym/Model/Data/Interface.php';

/**
 * @see Zym_Model_Form_Interface
 */
require_once 'Zym/Model/Form/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Model_Db_Abstract implements Zym_Model_Data_Interface, Zym_Model_Form_Interface
{
    /**
     * Primary key column
     *
     * @var string
     */
    protected $_primary = 'id';
    
    /**
     * Table
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_table = null;
    
    /**
     * Form instance
     *
     * @var Zend_Form
     */
    protected $_form = null;
    
    /**
     * Set data source
     *
     * @param Zend_Db_Table_Abstract $table
     * @return App_Model_Db_Abstract
     */
    public function setDataSource(Zend_Db_Table_Abstract $table)
    {
        $this->_table = $table;
        
        return $this;
    }
    
    /**
     * Get data source
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDataSource()
    {
        return $this->_table;
    }
    
    /**
     * Set a form
     *
     * @param Zend_Form $form
     * @return App_Model_Db_Abstract
     */
    public function setForm(Zend_Form $form)
    {
        $this->_form = $form;
        
        return $this;
    }
    
    /**
     * Get the form instance
     *
     * @throws DomainException
     * @return Zend_Form
     */
    public function getForm()
    {
        if (null === $this->_form) {
            throw new DomainException('No form instance set');
        }
        
        return $this->_form;
    }
    
    /**
     * Get entry by id
     *
     * @param int $id
     * @throws InvalidArgumentException
     * @return Entry_Row
     */
    public function getById($id)
    {
        $result = $this->getDataSource()->find((int) $id);

        if ($result->count() < 1) {
            throw new InvalidArgumentException('No row found for id "' . $id . '"');
        }
        
        return $result->current();
    }
    
    /**
     * Delete entry by id
     * 
     * @param int $id
     * @return mixed
     */
    public function deleteById($id)
    {
        return $this->getDataSource()->delete(array($this->_primary => (int) $id));
    }
    
    /**
     * Save the data
     *
     * @param array $data
     * @param int|null $id
     * @return mixed
     */
    public function save(array $data, $id = null)
    {
        $table = $this->getDataSource();
        
        if (array_key_exists($this->_primary, $data)) {
            if (!empty($data[$this->_primary])) {
                $id = $data[$this->_primary];
            }
            
            unset($data[$this->_primary]);
        }
        
        if (empty($id)) {
            $result = $table->insert($data);
        } else {
            $result = $table->update($data, array($this->_primary . ' = ?' => (int) $id));
        }
        
        return $result;
    }
}