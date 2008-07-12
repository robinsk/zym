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
class Default_IndexController extends Zym_Controller_Action_Abstract
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
        /*
        $classes = array_merge(get_declared_classes(), get_declared_interfaces());

        $zendZym = array();
        foreach ($classes as $class) {;
            if (strtolower(substr($class, 0, 4)) == 'zend' || strtolower(substr($class, 0, 3)) == 'zym') {
               $zendZym[] = str_ireplace('_', '/', $class) . '.php';

               echo 'require_once \'' . str_ireplace('_', '/', $class) . '.php\';'.'<br />';
            }
        }
        */
    }

    public function logAction()
    {
        $this->getHelper('ViewRenderer')->setNoRender();
    }
}