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
 * @package Zym_Error
 * @subpackage Handler
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Error
 * @subpackage Handler
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
interface Zym_Error_Handler_Interface
{
    /**
     * PHP Error handler
     *
     * @param integer $code
     * @param string $message
     * @param string $file
     * @param integer $line
     * @param array $context
     */
    public static function handle($code, $message, 
                                  $file = null, $line = null, array $context = array());
}