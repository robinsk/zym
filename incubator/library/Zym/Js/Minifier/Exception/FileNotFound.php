<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Js
 * @author     Geoffrey Tran
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Js_Minifier_Exception
 */
require_once 'Zym/Js/Minifier/Exception.php';

/**
 * Zym_Js_Exception
 *
 * @category   Zym
 * @package    Zym_Js
 * @author     Geoffrey Tran
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Js_Minifier_Exception_FileNotFound extends Zym_Js_Minifier_Exception
{
    /**
     * Construct
     *
     * @param string $file
     * @param integer $code
     */
    public function __construct($file, $code = null)
    {
        if ($file === null) {
            $message = 'File could not be found: ' . $file;
        }
        
        parent::__construct($message, $code);
    }
}
