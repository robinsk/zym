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
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_FlashMessengerController extends Zym_Controller_Action_Abstract 
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {}
    
    /**
     * Set message
     *
     */
    public function setAction()
    {
        $message = $this->getRequest()->getParam('message');

        if ($message) {
            $this->getHelper('flashMessenger')->addMessage($message);
        }
        
        // Go back to form
        $this->getHelper('Redirector')->goto('index');
    }
}