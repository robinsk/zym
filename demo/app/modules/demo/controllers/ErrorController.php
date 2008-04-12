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
 * @see Zym_Controller_Action_Error
 */
require_once 'Zym/Controller/Action/Error.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_ErrorController extends Zym_Controller_Action_Error
{
    public function init()
    {
        $this->addErrorHandler(Zym_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER, 'internal');
        $this->setFallBack('error', 'error', 'demo');
    }
    
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {}
    
    /**
     * Demonstrate exception handling
     *
     * @return void
     */
    public function exceptionAction()
    {
        throw new Exception('A test demo exception');
        
        // Disable ViewRenderer
        $this->getHelper('ViewRenderer')->setNoRender();
    }
    
    /**
     * Internal error
     *
     * @return void
     */
    public function internalAction()
    {}
}