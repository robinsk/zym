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
 * @see Zym_Model_Form_Interface
 */
require_once 'Zym/Model/Form/Interface.php';

/**
 * @see Zym_Model_Table_Interface
 */
require_once 'Zym/Model/Table/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Model_Abstract implements Zym_Model_Form_Interface, Zym_Model_Table_Interface
{
    /**
     * Form instance
     *
     * @var Zend_Form
     */
    protected $_form = null;
    
    /**
     * Form class name
     *
     * @var string
     */
    protected $_formName = null;
    
    /**
     * Table instance
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_table = null;
    
    /**
     * Table class name
     *
     * @var string
     */
    protected $_tableName = null;
    
    /**
     * Get the form instance. Instantiate a new form object if none is set.
     *
     * @return Zend_Form
     */
    public function getForm()
    {
        if (null === $this->_form) {
            if (null === $this->_formName) {
                /**
                 * @see Zym_Model_Exception
                 */
                require_once 'Zym/Model/Exception.php';
                
                throw new Zym_Model_Exception('No form instance set and no form class name specified.');
            }
            
            $this->_form = new $this->_formName();
        }
        
        return $this->_form;
    }
    
    /**
     * Get the table instance. Instantiate a table object if none is set.
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        if (null === $this->_table) {
            if (null === $this->_formName) {
                /**
                 * @see Zym_Model_Exception
                 */
                require_once 'Zym/Model/Exception.php';
                
                throw new Zym_Model_Exception('No table instance set and no table class name specified.');
            }
            
            $this->_table = new $this->_tableName();
        }
        
        return $this->_table;
    }
}