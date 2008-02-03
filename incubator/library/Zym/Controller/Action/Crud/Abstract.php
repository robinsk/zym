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
 * @see Zym_Controller_Exception
 */
require_once 'Zym/Controller/Exception.php';

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
     * Get the table for this model
     *
     * @return Zend_Db_Table_Abstract
     */
    abstract protected function _getTable();

    /**
     * Get the form for this model
     *
     * @return Zend_Form
     */
    abstract protected function _getForm();

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

        return $table->find((int) $id)
                     ->current();
    }

    /**
     * Get the name of the action that takes care of the add/edit stuff
     *
     * @return string
     */
    protected function _getAddEditAction()
    {
        return 'addEdit';
    }

    /**
     * Get the name of the action that takes care of the listing
     *
     * @return string
     */
    protected function _getListAction()
    {
        return 'list';
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
        $limit = $this->_getParam('limit');
        $page  = $this->_getParam('page');

        $table = $this->_getTable();
        $models = $table->fetchAll(null, $limit, $page);

        $this->view->limit = $limit;
        $this->view->page = $page;
        $this->view->models = $models;
    }

    /**
     * View a model if it exists
     */
    public function viewAction()
    {
        $id = $this->_getPrimaryId();
        $model = null;

        if ($id) {
            $model = $this->_getModel($id);

            if ($model) {
                $this->view->model = $model;
            }
        }

        if (!$model) {
            $this->_goto($this->_getListAction());
        }
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
                $formValues = $form->getValues();

                if (!empty($formValues[$this->_getPrimaryIdKey()])) {
                    $model = $this->_getModel($id);

                    if (!$model) {
                        throw new Zym_Controller_Exception('The model could not be loaded.');
                    }
                } else {
                    $model = $this->_getTable()->createRow();
                }

                foreach ($formValues as $key => $value) {
                	if (isset($model->$key)) {
                	    $model->$key = $value;
                	}
                }

                $model->save();

                $this->_goto($this->_getListAction());
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