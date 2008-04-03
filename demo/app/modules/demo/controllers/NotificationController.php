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
 * @see Zym_Notification
 */
require_once 'Zym/Notification.php';

/**
 * @see App_Demo_Notification
 */
require_once 'App/Demo/Notification.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_NotificationController extends Zym_Controller_Action_Abstract 
{
    /**
     * init
     * 
     * @return void
     */
    public function init()
    {
        // Setup notification
        $this->_setupNotification();    
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
        $notification = Zym_Notification::get();
        $notification->post('sandy', $this);
        
        $this->getHelper('ViewRenderer')->setNoRender();
    }
    
    /**
     * Notify bill
     * 
     * @return void
     */
    public function notifyBillAction()
    {
        $notification = Zym_Notification::get();
        $notification->post('bill', $this);
        
        $this->getHelper('ViewRenderer')->setNoRender();
    }
    
    /**
     * Setup notification
     *
     * @return void
     */
    protected function _setupNotification()
    {
        $demo = new App_Demo_Notification();
        
        $notification = Zym_Notification::get();
        
        // Register sandy if not already registered
        if (!$notification->isRegistered('sandy')) {
            $notification->attach($demo, 'sandy');
        }
        
        // Register bill if not already registered
        if (!$notification->isRegistered('bill')) {
            $notification->attach($demo, 'bill', 'hello');
        }
    }
}