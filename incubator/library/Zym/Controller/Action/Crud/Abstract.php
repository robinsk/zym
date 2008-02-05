<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Controller
 * @subpackage Action
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
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
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    Controller
 * @subpackage Action
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
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
    protected function _getModel($id)
    {
        $table = $this->_getTable();

        $model = $table->find((int) $id)
                       ->current();

        if (!$model) {
            $this->_throwException('The model could not be loaded.');
        }

        return $model;
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
        $limit = (int) $this->_getParam('limit');
        $page  = (int) $this->_getParam('page');

        $select = $this->_getListSelect();

        if ($limit > 0 && $page > 0) {
            $select->limitPage($page, $limit);
        }

        $models = $this->_getTable()->fetchAll($select);

        $this->view->limit = $limit;
        $this->view->page = $page;
        $this->view->models = $models;
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
        $model = $this->_getModel($id);

        $this->view->model = $model;
    }

    /**
     * Add or edit a model
     */
    public function addEditAction()
    {
        $form = $this->_getForm();

        $id = $this->_getPrimaryId();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_processValidForm();
            }
        } else {
            if ($id) {
                $model = $this->_getModel($id);

                $form->setDefaults($model->toArray());
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
     * Process the form after it's been succesfully validated
     *
     */
    protected function _processValidForm()
    {
        $form = $this->_getForm();
        $table = $this->_getTable();

        $formValues = $form->getValues();

        if (!empty($formValues[$this->_getPrimaryIdKey()])) {
            $model = $this->_getModel($this->_getPrimaryId());
        } else {
            $model = $table->createRow();
        }
//@TODO hooks?
        $tableInfo = $table->info();
        $metaData = $tableInfo[Zend_Db_Table_Abstract::METADATA];

        foreach ($formValues as $key => $value) {
            if (isset($model->$key) && !(bool) $metaData[$key]['IDENTITY']) {
                $model->$key = $value;
            }
        }

        $model->save();

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