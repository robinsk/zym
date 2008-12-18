<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym
 * @package   Zym_Svn_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license   http://www.zym-project.com/license New BSD License
 */

/**
 * PHP Subversion Client
 *
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym
 * @package   Zym_Svn_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Svn_Client
{
    const NON_RECURSIVE = '';
    const IGNORE_EXTERNALS = '';
    const REVISION_HEAD = -1;

    /**
     * Connect to repositry
     *
     * @param string $path
     */
    public static function factory($path, $user = null, $pass = null)
    {
        if (substr($path, 0, 7) === 'http://') {
            require_once 'Zym/Svn/Client/Adapter/Http.php';
            $adapter = new Zym_Svn_Client_Adapter_Http(substr($path, 7), $user, $pass);
        } else if (substr($path, 0, 6) === 'svn://' ) {
            require_once 'Zym/Svn/Client/Adapter/Svn.php';
            $adapter = new Zym_Svn_Client_Adapter_Svn(substr($path, 6), $user, $pass);
        } else {
            require_once 'Zym/Svn/Client/Adapter/Svn.php';
            $adapter = new Zym_Svn_Client_Adapter_Svn($path, $user, $pass);
        }
        
        return $adapter;
    }
}