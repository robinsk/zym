<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Controller
 * @subpackage ActionHelper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
// @TODO: Allow custom mapping of forms to action
/**
 * @see Zend_Session_Namespace
 */
require_once 'Zend/Session/Namespace.php';

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * @category   Zym
 * @package    Controller
 * @subpackage ActionHelper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Controller_Action_Helper_MultiPageForm extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Default keys for controlling the multiform
     *
     */
    const ACTION_PREFIX     = '_';
    const ACTION_KEY_NEXT   = '_next';
    const ACTION_KEY_BACK   = '_back';
    const ACTION_KEY_SUBMIT = '_submit';
    const ACTION_KEY_CANCEL = '_cancel';

    /**
     * Zend_Session storage object
     *
     * @var Zend_Session
     */
    protected $_session = null;

    /**
     * The complete Zend_Form instance
     *
     * @var Zend_Form
     */
    protected $_form = null;

    /**
     * The current subform instance
     *
     * @var Zend_Form
     */
    protected $_currentSubForm = null;

    /**
     * The form data
     *
     * @var array
     */
    protected $_tempFormData = array();

    /**
     * The form action in order of apearance
     *
     * @var array
     */
    protected $_subFormActions = array();

    /**
     * The action that will be used for processing the form
     *
     * @var string
     */
    protected $_processAction = 'process';

    /**
     * The action for canceling the form
     *
     * @var string
     */
    protected $_cancelAction = null;

    /**
     * The last valid action
     *
     * @var string
     */
    protected $_lastValidAction = null;

    /**
     * Construct and set default session object
     */
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace($this->getName());
    }

    /**
     * Executed at preDispatch
     *
     */
    public function preDispatch()
    {
        if (empty($this->_subFormActions)) {
            $this->_throwException('Multiform has not been assigned any actions');
        }

        $action = $this->getRequest()->getActionName();

        if ($action == $this->_processAction && !$this->isValid()) {
            $this->redirect($this->getLastValidAction());
        } else if ($this->isSubformAction($action)) {
            if (!$this->isValidAction($action)) {
                $this->redirect($this->getLastValidAction());
            } else {
                $this->handle();
            }

            $this->getActionController()->view->form = $this->getCurrentSubForm();
        }
    }

    /**
     * Get the subform data
     *
     * @return array
     */
    public function getSubformData()
    {
        $formData = array();

        $forms = $this->_form->getSubForms();

        foreach ($forms as $form) {
            $formData[] = $form->getValues();
        }

        return $formData;
    }

    /**
     * Determine if an action has been validated
     *
     * @param string $current
     * @return string
     */
    public function isValidAction($current)
    {
        foreach ($this->_subFormActions as $action) {
            $this->_lastValidAction = $action;

            if ($current == $action) {
                break;
            }

            if (!$this->isCompleteAction($action)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if an action has been submitted
     *
     * @param string $action
     * @return mixed
     */
    public function isCompleteAction($action)
    {
        if (isset($this->_session->valid[$action])) {
            return $this->_session->valid[$action];
        }

        return false;
    }

    /**
     * Set the action used for processing the complete form
     *
     * @param string $action
     * @return Zym_Controller_Action_Helper_MultiForm
     */
    public function setProcessAction($action)
    {
        $this->_processAction = $action;

        return $this;
    }

    /**
     * Get the processing action
     *
     * @return unknown
     */
    public function getProcessAction()
    {
        return $this->_processAction;
    }

    /**
     * Set a custom cancel action
     *
     * @param string $action
     * @return Zym_Controller_Action_Helper_MultiForm
     */
    public function setCancelAction($action)
    {
        $this->_cancelAction = $action;

        return $this;
    }

    /**
     * Get the custom cancel action
     *
     * @return string
     */
    public function getCancelAction()
    {
        return $this->_cancelAction;
    }

    /**
     * Set sequence of actions
     *
     * @param array $actions
     * @return Zym_Controller_Action_Helper_MultiForm
     */
    public function setActions(array $actions)
    {
        $this->_subFormActions = $actions;

        if (is_null($this->_session->valid) || !array_key_exists($actions[0], $this->_session->valid)) {
            $this->clear();
        }

        return $this;
    }

    /**
     * Set values for an action
     *
     * @param Zend_Form $values
     * @param boolean $valid
     * @return Zym_Controller_Action_Helper_MultiForm
     */
    public function setValues(Zend_Form $form, $valid = false)
    {
        $action = $form->getName();

        if (!$this->isSubformAction($action)) {
            $this->_throwException(sprintf('"%s" is not a valid action', $action));
        }

        $formValues = $form->getValues();
        $formKeys = array_keys($formValues);

        foreach ($formKeys as $key) {
            if (strpos($key, self::ACTION_PREFIX) === 0) {
                unset($formValues[$key]);
            }
        }

        $this->_session->valid[$action] = (boolean) $valid;
        $this->_session->value[$action] = $formValues;

        return $this;
    }

    /**
     * Retrieve action values
     *
     * @param string $action
     * @return mixed
     */
    public function getValues($action = null)
    {
        if ($action === null) {
            return $this->_session->value;
        }

        if (isset($this->_session->value[$action])) {
            return $this->_session->value[$action];
        }

        return array();
    }

    /**
     * Retrieve current lsat valid action
     *
     * @return string
     */
    public function getLastValidAction()
    {
        return $this->_lastValidAction;
    }

    /**
     * Set a form instance
     *
     * @param Zend_Form $form
     * @return Zym_Controller_Action_Helper_MultiForm
     */
    public function setForm(Zend_Form $form)
    {
        $this->_form = $form;

        $subForms = $form->getSubForms();

        foreach ($subForms as $subForm) {
            $formName = $subForm->getName();

            if (empty($formName)) {
                $this->_throwException('A subform needs to have a name.');
            }
        }

        return $this;
    }

    /**
     * Get the form instance
     *
     * @return Zend_Form
     */
    public function getForm()
    {
        if (!$this->_form) {
            $this->_throwException('No form instance set.');
        }

        return $this->_form;
    }

    /**
     * Get the current subform
     *
     * @return Zend_Form
     */
    public function getCurrentSubForm()
    {
        if (!$this->_currentSubForm) {
            $formName = $this->getRequest()->getActionName();

            $this->_currentSubForm = $this->getSubForm($formName);
        }

        return $this->_currentSubForm;
    }

    /**
     * Get a subform by name.
     *
     * @param string $formName
     * @return Zend_Form
     */
    public function getSubForm($formName)
    {
        $subForm = $this->_form->getSubForm($formName);

        if (empty($subForm)) {
            $this->_throwException(sprintf('No form by the name of "%s" was registered.', $formName));
        }

        $subForm->populate($this->getValues($formName));

        return $subForm;
    }

    /**
     * Check if the entire form is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $formData = $this->getFormData();

        return $this->_form->isValid($formData);
    }

    /**
     * Get all data from the subforms
     *
     * @return array
     */
    public function getFormData($flatten = false)
    {
        $formData = array();

        foreach ($this->_subFormActions as $action) {
            $formValues = $this->getValues($action);

            if ($flatten) {
                $formData = array_merge($formData, $formValues);
            } else {
                $formData[$action] = $formValues;
            }
        }

        return $formData;
    }

    /**
     * Get the form data. If it's empty and a submitted form is available,
     * populate it first from POST.
     *
     * @return array
     */
    public function getPostData()
    {
        if (empty($this->_tempFormData) && $this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            $elements = $this->getCurrentSubForm()->getElements();

            $this->_tempFormData = array_intersect_key($postData, $elements);
        }

        return $this->_tempFormData;
    }

    /**
     * Use the redirector helper to navigate the controller
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function redirect($action, $controller = null, $module = null)
    {
        $redirector = $this->getActionController()->getHelper('Redirector');

        return $redirector->gotoAndExit($action,
                                        $controller,
                                        $module);
    }

    /**
     * Get the action used to submit the form
     *
     * @return string|false
     */
    public function getSubmitAction()
    {
        $formData = $this->getPostData();

        if (!empty($formData)) {
            $formDataKeys = array_keys($formData);

            foreach ($formDataKeys as $key) {
                if (strpos($key, self::ACTION_PREFIX) === 0) {
                    return $key;
                }
            }
        }

        return false;
    }

    /**
     * Handle the form
     *
     * @return boolean
     */
    public function handle()
    {
        $action = $this->getSubmitAction();

        if ($action === false) {
            return false;
        }

        $currentSubForm = $this->getCurrentSubForm();
        $valid = $currentSubForm->isValid($this->getPostData());

        $this->setValues($currentSubForm, $valid);

        switch ($action) {
            case self::ACTION_KEY_BACK:
                $position = array_search($currentSubForm->getName(), $this->_subFormActions);

                if ($position <= 0) {
                    $action = $this->_subFormActions[0];
                } else {
                    $action = $this->_subFormActions[$position - 1];
                }
                break;

            case self::ACTION_KEY_NEXT:
                if (!$valid) {
                    return false;
                }

                $position = array_search($currentSubForm->getName(), $this->_subFormActions);

                if ($position == count($this->_subFormActions) - 1) {
                    $action = $this->_subFormActions[count($this->_subFormActions) - 1];
                } else {
                    $action = $this->_subFormActions[$position + 1];
                }
                break;

            case self::ACTION_KEY_SUBMIT;
                if (!$valid) {
                    return false;
                }

                $action = $this->getProcessAction();
                break;

            case self::ACTION_KEY_CANCEL:
                if (empty($this->_cancelAction)) {
                    $this->clear();
                    $action = $this->_subFormActions[0];
                } else {
                    $action = $this->getCancelAction();
                }
                break;

            default:
                if (!in_array($action, $this->_subFormActions)) {
                    $this->redirect($this->getLastValidAction());
                }
                break;
        }

        return $this->redirect($action);
    }

    /**
     * Check if the action is an action for this form.
     *
     * @param string $action
     * @return boolean
     */
    public function isSubformAction($action)
    {
        return in_array($action, $this->_subFormActions);
    }

    /**
     * Reset all session data
     */
    public function clear()
    {
        $this->_session->valid = array();
        $this->_session->value = array();

        foreach ($this->_subFormActions as $id) {
            if (!isset($this->_session->valid[$id])) {
                $this->_session->valid[$id] = false;
                $this->_session->value[$id] = array();
            }
        }
    }

    /**
     * Throw an exception
     *
     * @param string $message
     * @throws Zym_Controller_Exception
     */
    protected function _throwException($message)
    {
        /**
         * @see Zym_Controller_Exception
         */
        require_once 'Zym/Controller/Exception.php';

        throw new Zym_Controller_Exception($message);
    }
}