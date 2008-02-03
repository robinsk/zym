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

    protected function _getPrimaryId()
    {
        return $this->_getParam($this->_getPrimaryIdKey());
    }

    // @TODO: make the locations dynamic
    protected function _getNewSubmitLocation()
    {
        return array('module'     => $this->getRequest()->getModuleName(),
                     'controller' => $this->getRequest()->getControllerName(),
                     'action'     => 'addEdit');
    }

    protected function _getEditSubmitLocation()
    {
        return array('module'     => $this->getRequest()->getModuleName(),
                     'controller' => $this->getRequest()->getControllerName(),
                     'action'     => 'addEdit',
                     'id'         => $this->_getPrimaryId());
    }

    protected function _getPrimaryIdKey()
    {
        $info = $this->_getTable()->info();
        // @TODO: decide if we want support for multiple primary keys
        return $info[Zend_Db_Table_Abstract::PRIMARY][0];
    }

    protected function _getListAction()
    {
        return 'list';
    }

    public function indexAction ()
    {
        $this->_forward($this->_getListAction());
    }

    public function listAction()
    {
        $table = $this->_getTable();
        $models = $table->fetchAll();

        $this->view->models = $models;
    }

    public function viewAction()
    {
        $id = $this->_getPrimaryId();
        $model = null;

        if ($id) {
            $table = $this->_getTable();
            $model = $table->find($id)
                           ->current();

            if ($model) {
                $this->view->model = $model;
            }
        }

        if (!$model) {
            $this->_goto($this->_getListAction());
        }
    }

    public function addEditAction()
    {
        $idKey = $this->_getPrimaryIdKey();
        $form = $this->_getForm();

        $id = $this->_getPrimaryId();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $formValues = $form->getValues();
                $table = $this->_getTable();
                // @TODO: make a safe way to set the values
                if (!empty($formValues[$idKey])) {
                    $model = $table->find($formValues[$idKey])
                                   ->current();

                    if (!$model) {
                        // @TODO redirect to 'couldn't find model' page
                    }
                } else {
                    $model = $table->createRow();
                }

                /**
                 * @TODO: Set model values here....
                 * $model->key = $formValues['key'];
                 */

                $model->save();

                $this->_goto($this->_getListAction());
            }
        } else {
            $form->loadAddress($id);
        }

        if (!$id) {
            $url = $this->_getNewSubmitLocation();
        } else {
            $url = $this->_getEditSubmitLocation();
        }

        $form->setAction($this->view->url($url, null, true));

        $this->view->form = $form;
    }

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