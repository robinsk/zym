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
 * @package    Zym_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Zym_Controller_Action_Abstract
 */
require_once 'Zym/Controller/Action/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Controller_Action_Crud_Abstract extends Zym_Controller_Action_Abstract
{
    /**
     * Table instance
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Form instance
     *
     * @var Zend_Form
     */
    protected $_form;

    /**
     * The add-edit action
     *
     * @var string
     */
    protected $_addEditAction = 'addEdit';

    /**
     * The list action
     *
     * @var string
     */
    protected $_listAction = 'list';

    /**
     * Default page limit for pagination
     *
     * @var int
     */
    protected $_defaultPageLimit = null;

    /**
     * Default page number for pagination
     */
    protected $_defaultPageNr = 1;

    /**
     * Get the table for this model
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function _getTable()
    {
        if (!$this->_table) {
            $this->_throwException('No table instance set.');
        }

        return $this->_table;
    }

    /**
     * Set the table
     *
     * @param Zend_Db_Table_Abstract $table
     * @return Zym_Controller_Action_Crud_Abstract
     */
    protected function _setTable(Zend_Db_Table_Abstract $table)
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Get the form for this model
     *
     * @return Zend_Form
     */
    protected function _getForm()
    {
        if (!$this->_form) {
            $this->_throwException('No form instance set.');
        }

        return $this->_form;
    }

    /**
     * Set a form instance
     *
     * @param Zend_Form $form
     * @return Zym_Controller_Action_Crud_Abstract
     */
    protected function _setForm(Zend_Form $form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
     * Get the primary id from the request
     *
     * @return int|null
     */
    protected function _getPrimaryId()
    {
        return $this->_getParam($this->_getPrimaryIdKey());
    }

    /**
     * Get the location to where the form needs to submit for a new entry
     *
     * @return array
     */
    protected function _getNewSubmitLocation()
    {
        return array('module'     => $this->getRequest()->getModuleName(),
                     'controller' => $this->getRequest()->getControllerName(),
                     'action'     => $this->_getAddEditAction());
    }

    /**
     * Get the location to where the form needs to submit for an edited entry
     *
     * @return array
     */
    protected function _getEditSubmitLocation()
    {
        $location = $this->_getNewSubmitLocation();
        $location[$this->_getPrimaryIdKey()] = $this->_getPrimaryId();

        return $location;
    }

    /**
     * Get the column name of the primary id
     *
     * @return string
     */
    protected function _getPrimaryIdKey()
    {
        $info = $this->_getTable()->info();

        return (string) array_shift($info[Zend_Db_Table_Abstract::PRIMARY]);
    }

    /**
     * Get the model from the table
     *
     * @param int $id
     * @return Zend_Db_Table_Row_Abstract|null
     */
    protected function _getRow($id)
    {
        $table = $this->_getTable();

        $row = $table->find((int) $id)
                     ->current();

        if (!$row) {
            $this->_throwException('The requested row could not be loaded.');
        }

        return $row;
    }

    /**
     * Get the name of the action that takes care of the add/edit stuff
     *
     * @return string
     */
    protected function _getAddEditAction()
    {
        if (!$this->_addEditAction) {
            $this->_throwException('You must set an add-edit action.');
        }

        return $this->_addEditAction;
    }

    /**
     * Set the add-edit action
     *
     * @param string $action
     * @return Zym_Controller_Action_Crud_Abstract
     */
    protected function _setAddEditAction($action)
    {
        $this->_addEditAction = $action;

        return $this;
    }

    /**
     * Get the name of the action that takes care of the listing
     *
     * @return string
     */
    protected function _getListAction()
    {
        if (!$this->_listAction) {
            $this->_throwException('You must set a list action.');
        }

        return $this->_listAction;
    }

    /**
     * Set the add-edit action
     *
     * @param string $action
     * @return Zym_Controller_Action_Crud_Abstract
     */
    protected function _setListAction($action)
    {
        $this->_listAction = $action;

        return $this;
    }

    /**
     * Throw an exception
     *
     * @param string $message
     * @throws Zym_Controller_Action_Exception
     */
    protected function _throwException($message)
    {
        /**
         * @see Zym_Controller_Action_Exception
         */
        require_once 'Zym/Controller/Action/Exception.php';

        throw new Zym_Controller_Action_Exception($message);
    }

    /**
     * Index action. Forward to the list action
     */
    public function indexAction()
    {
        $this->_forward($this->_getListAction());
    }

    /**
     * Show a list with all available models
     */
    public function listAction()
    {
        $limit = (int) $this->_getParam('limit', $this->_defaultPageLimit);
        $page  = (int) $this->_getParam('page', $this->_defaultPageNr);

        $select = $this->_getListSelect();

        if ($limit > 0 && $page > 0) {
            $select->limitPage($page, $limit);
        }

        $rows = $this->_getTable()->fetchAll($select);

        $this->view->limit = $limit;
        $this->view->page = $page;
        $this->view->rows = $rows;
    }

    /**
     * Get the select object for the list action
     *
     * @return Zend_Db_Table_Select
     */
    protected function _getListSelect()
    {
        return $this->_getTable()->select();
    }

    /**
     * View a model if it exists
     */
    public function viewAction()
    {
        $id = $this->_getPrimaryId();
        $row = $this->_getRow($id);

        $this->view->row = $row;
    }

    /**
     * Add or edit a model
     */
    public function addEditAction()
    {
        $form = $this->_getForm();

        $id = $this->_getPrimaryId();

        if ($this->getRequest()->isPost()) {
            $this->_handlePostAction();

            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_processValidForm();
            }
        } else {
            if ($id) {
                $row = $this->_getRow($id);

                $form->setDefaults($row->toArray());
            }
        }

        if (!$id) {
            $url = $this->_getNewSubmitLocation();
        } else {
            $url = $this->_getEditSubmitLocation();
        }

        $form->setAction($this->view->url($url, null, true));

        $this->view->form = $form;
    }

    /**
     * Check if a special submit button is used and act accordingly.
     * @TODO make this nice...
     */
    protected function _handlePostAction()
    {
        $postDataKeys = array_keys($this->getRequest()->getPost());

        foreach ($postDataKeys as $key) {
        	if (strpos($key, '_') === 0) {
        	    switch ($key) {
        	        case '_cancel':
        	            $this->_goto($this->_getListAction());
        	            break;
        	    }
        	    break;
        	}
        }
    }

    /**
     * Process the form after it's been succesfully validated
     *
     */
    protected function _processValidForm()
    {
        $table = $this->_getTable();

        $formValues = $this->_getForm()->getValues();

        if (!empty($formValues[$this->_getPrimaryIdKey()])) {
            $row = $this->_getRow($this->_getPrimaryId());
        } else {
            $row = $table->createRow();
        }

        $tableInfo = $table->info();
        $metaData = $tableInfo[Zend_Db_Table_Abstract::METADATA];

        foreach ($formValues as $key => $value) {
            if (isset($row->$key) && !(bool) $metaData[$key]['IDENTITY']) {
                $row->$key = $value;
            }
        }

        $row->save();

        $this->_goto($this->_getListAction());
    }

    /**
     * Delete a model
     */
    public function deleteAction()
    {
        $id = $this->_getPrimaryId();

        if ($id) {
            $table = $this->_getTable();

            $where = $table->getAdapter()
                           ->quoteInto(sprintf('%s=?', $this->_getPrimaryIdKey()), $id);

            $table->delete($where);
        }

        $this->_goto($this->_getListAction());
    }
}