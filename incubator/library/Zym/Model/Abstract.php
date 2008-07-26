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
 * @see Zym_Model_Data_Interface
 */
require_once 'Zym/Model/Data/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Model_Abstract implements Zym_Model_Form_Interface, Zym_Model_Data_Interface
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
     * Data source instance
     *
     * @var mixed
     */
    protected $_dataSource = null;
    
    /**
     * Data source class name
     *
     * @var string
     */
    protected $_dataSourceName = null;
    
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
     * Get the data source. This can be anything (Db, Service etc.)
     *
     * @return mixed
     */
    public function getDataSource()
    {
        if (null === $this->_dataSource) {
            if (null === $this->_dataSourceName) {
                /**
                 * @see Zym_Model_Exception
                 */
                require_once 'Zym/Model/Exception.php';
                
                throw new Zym_Model_Exception('No data source instance set and no data source class name specified.');
            }
            
            $this->_dataSource = new $this->_dataSourceName();
        }
        
        return $this->_dataSource;
    }
}