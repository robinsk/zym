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
class Demo_FormController extends Zym_Controller_Action_Abstract
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $params  = $request->getParams();
        $form    = $this->getHelper('Form')->create('Example');

        $isRequestValid = $request->isPost() && $form->isValid($params);
        if ($isRequestValid) {
            // Success
            $this->getHelper('FlashMessenger')->addMessage('Login Success');
            $this->_goto('index');
        }

        // Populate form with data
        $form->populate($params);

        // View
        $this->getView()->assign(array(
            'form' => $form
        ));
    }
}