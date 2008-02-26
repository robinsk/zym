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
 * @package Zym_App
 * @subpackage ExceptionHandler
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */


/**
 * @see Zym_App_ExceptionHandler_Abstract
 */
require_once('Zym/App/ExceptionHandler/Abstract.php');

/**
 * Throws a simple internal server error without any special stuff
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage ExceptionHandler
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_ExceptionHandler_ServerError extends Zym_App_ExceptionHandler_Abstract
{
    /**
     * Handle Boostrap exceptions
     *
     * @param Exception $e
     */
    public function handle(Exception $e)
    {
        header('HTTP/1.1 500 Internal Server Error');
    }
}