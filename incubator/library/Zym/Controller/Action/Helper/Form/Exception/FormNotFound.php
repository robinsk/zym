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
 * @subpackage Action_Helper_Form_Exception
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Controller_Action_Helper_Form_Exception
 */
require_once 'Zym/Controller/Action/Helper/Form/Exception.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Action_Helper_Form_Exception
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_Form_Exception_FormNotFound extends Zym_Controller_Action_Helper_Form_Exception
{
    /**
     * Construct
     *
     */
    public function __construct($class, $path)
    {
        parent::__construct(sprintf(
            'Form action helper could not load form class "%s" at path "%s"', $class, $path
        ));
    }
}