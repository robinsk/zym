<?php
class MyformController extends Zend_Controller_Action
{
    public function init()
    {
        $multiPageForm = $this->getHelper('multiPageForm');
        $multiPageForm->setForm(new MyForm());
        $multiPageForm->setActions(array('order', 'user', 'survey'));
    }

    public function indexAction()
    {
        $this->_helper->multiPageForm->clear();
        $this->getHelper('Redirector')->gotoAndExit('order');
    }

    public function orderAction()
    {
        // custom logic for order action
    }

    public function userAction()
    {
        // custom logic for user action
    }

    public function surveyAction()
    {
        // custom logic for survey action
    }

    public function processAction()
    {
        $data = $this->_helper->multiPageForm->getFormData();

        /**
         * After fetching the data you'd probably want to clear the form
         * so it can go for another round.
         */
        $this->_helper->multiPageForm->clear();

        /**
         * Display a success screen for this demo.
         * In real-life use you'd probably write the data to the DB here.
         * Note that all data in the $data array is filtered and validated,
         * so you don't have to do that here anymore.
         */
        $this->view->formData = $data;
    }
}