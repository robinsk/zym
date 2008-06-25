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
 * @package Zym_Loader
 * @subpackage Autoload
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Doctrine
 */
require_once 'Doctrine.php';

/**
 * @see Zym_Loader_Autoload_Interface
 */
require_once 'Zym/Loader/Autoload/Interface.php';

/**
 * Zym Autoload for Doctrine (http://phpdoctrine.org)
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Loader
 * @subpackage Autoload
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Loader_Autoload_Doctrine implements Zym_Loader_Autoload_Interface
{
    /**
     * spl_autoload() suitable implementation for supporting class autoloading.
     *
     * Attach to spl_autoload() using the following:
     * <code>
     * spl_autoload_register(array('Zend_Loader', 'autoload'));
     * </code>
     *
     * @param string $class
     * @return string|false Class name on success; false on failure
     */
    public static function autoload($class)
    {
        return Doctrine::autoload($class);
    }
}