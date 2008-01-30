<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category Zym
 * @package Zym_Loader
 * @subpackage Autoload
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @link http://www.spotsec.com
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
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Loader
 * @subpackage Autoload
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
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