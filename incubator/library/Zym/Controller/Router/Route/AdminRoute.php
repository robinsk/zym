<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Router_Route
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Controller_Router_Route
 */
require_once 'Zend/Controller/Router/Route.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Router_Route
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Controller_Router_Route_AdminRoute extends Zend_Controller_Router_Route
{
    public function __construct($route = null, $defaults = array(), $reqs = array())
    {
        $route = 'admin/:module/:action/*';
        $defaults = array('module'     => 'admin',
                          'controller' => 'admin',
                          'action'     => 'index');

        parent::__construct($route, $defaults);
    }
}