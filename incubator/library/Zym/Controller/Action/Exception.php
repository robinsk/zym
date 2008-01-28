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
 * @package Zym_Controller
 * @subpackage Action
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @link http://www.spotsec.com
 */

/**
 * Zym_Controller_Exception
 */
require_once('Zym/Controller/Exception.php');

/**
 * Zym_Controller_Action_Exception_Interface
 */
require_once('Zym/Controller/Action/Exception/Interface.php');

/**
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Action
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Controller_Action_Exception extends Zym_Controller_Exception 
    implements Zym_Controller_Action_Exception_Interface
{}