<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_View_Helper_Url
 */
 require_once 'Zend/View/Helper/Url.php';

/**
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_View_Helper_Url extends Zend_View_Helper_Url
{
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        if (empty($urlOptions)) {
            return $this;
        } else {
            return parent::url($urlOptions, $name, $reset, $encode);
        }
    }

    public function simple($action, $controller = null, $module = null, array $params = null)
    {
        //return Zend_Controller
    }

    public function route(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
    }

    public function __toString()
    {
        return $this->route();
    }
}