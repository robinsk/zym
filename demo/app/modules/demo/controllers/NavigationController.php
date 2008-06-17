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
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Controller_Action_Abstract
 */
require_once 'Zym/Controller/Action/Abstract.php';

/**
 * @see Zym_Notification
 */
require_once 'Zym/Navigation.php';

/**
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Demo_NavigationController extends Zym_Controller_Action_Abstract 
{
    /**
     * init
     * 
     * @return void
     */
    public function init()
    {
        // Setup navigation
        $this->_setupNavigation();    
    }
    
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
        /*
        // add a route to show that zym_navigation
        // can be aware of routes and params
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->addRoute(
            'myroute',
            new Zend_Controller_Router_Route('page2/:format', array(
                'controller' => 'page2', 'action' => 'index')
            )
        );
        */
        
        $this->view->navSetup = highlight_file('App/Demo/Navigation.setup.php', true);
    }
    
    /**
     * Prints an XML sitemap
     *
     */
    public function sitemapAction()
    {
        header('Content-type: text/xml; charset=UTF-8');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        echo $this->view->sitemap();
    }
    
    /**
     * Setup navigation
     *
     * @return void
     */
    protected function _setupNavigation()
    {
        require_once 'App/Demo/Navigation.setup.php';
    }
}