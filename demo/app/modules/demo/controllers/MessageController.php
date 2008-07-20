<?php
/**
 * Zym Framework Demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Controller_Action_Abstract
 */
require_once 'Zym/Controller/Action/Abstract.php';

/**
 * @see Zym_Message_Dispatcher
 */
require_once 'Zym/Message/Dispatcher.php';

/**
 * @see App_Demo_Message
 */
require_once 'App/Demo/Message.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_MessageController extends Zym_Controller_Action_Abstract
{
    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        // Setup notification
        $this->_setupMessageDispatcher();
    }

    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * Notify sandy
     *
     * @return void
     */
    public function notifySandyAction()
    {
        $dispatcher = Zym_Message_Dispatcher::get();
        $dispatcher->post('sandy', $this);

        $this->getHelper('ViewRenderer')->setNoRender();
    }

    /**
     * Notify bill
     *
     * @return void
     */
    public function notifyBillAction()
    {
        $dispatcher = Zym_Message_Dispatcher::get();
        $dispatcher->post('bill', $this);

        $this->getHelper('ViewRenderer')->setNoRender();
    }

    /**
     * Setup message dispatcher
     *
     * @return void
     */
    protected function _setupMessageDispatcher()
    {
        $demo = new App_Demo_Message();

        $dispatcher = Zym_Message_Dispatcher::get();

        // Register sandy if not already registered
        if (!$dispatcher->isRegistered('sandy')) {
            $dispatcher->attach($demo, 'sandy');
        }

        // Register bill if not already registered
        if (!$dispatcher->isRegistered('bill')) {
            $dispatcher->attach($demo, 'bill', 'hello');
        }
    }
}