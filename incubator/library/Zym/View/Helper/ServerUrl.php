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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_ServerUrl
{
    /**
     * Server url
     *
     * Returns the current hosts url like http://site.com
     *
     * @return string
     */
    public function serverUrl($requestUri = false)
    {
        // Protocol
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': '';

        // Display port if it is non-standard
        $port = ($_SERVER['SERVER_PORT'] == 80 || ($_SERVER['SERVER_PORT'] == 443 && $protocol))
                    ? '' : ":{$_SERVER['SERVER_PORT']}";

        // Display request uri
        $requestUri = $requestUri ? $_SERVER['REQUEST_URI'] : '';

        // Return url
        return sprintf('http%s://%s%s%s', $protocol, $_SERVER['SERVER_NAME'], $port, $requestUri);
    }
}
