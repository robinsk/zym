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
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Returns current server url (w/ or w/o request URI)
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_ServerUrl
{
    /**
     * Scheme
     *
     * @var string
     */
    protected $_scheme;

    /**
     * Host
     *
     * Including port
     *
     * @var string
     */
    protected $_host;

    /**
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        // Protocol
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https': 'http';
        $this->setScheme($protocol);

        if (isset($_SERVER['HTTP_HOST'])) {
            $this->setHost($_SERVER['HTTP_HOST']);
        } else if (isset($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'])) {
            $name = $_SERVER['SERVER_NAME'];
            $port = $_SERVER['SERVER_PORT'];

            if (($protocol == 'http' && $port == 80) || ($protocol == 'https' && $port == 443)) {
                $this->setHost($name);
            } else {
                $this->setHost($name . ':' . $port);
            }
        }
    }

    /**
     * Server url
     *
     * Returns the current hosts url like http://site.com
     *
     * @return string
     */
    public function serverUrl($requestUri = false)
    {
        // Display request uri
        $path = $requestUri ? $_SERVER['REQUEST_URI'] : '';

        // Return url
        return $this->getScheme() . '://' . $this->getHost() . $path;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * Set host
     *
     * @param string $_host
     */
    public function setHost($host)
    {
        $this->_host = $host;
    }

    /**
     * Get scheme
     *
     * Http or https
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

    /**
     * Set scheme
     *
     * @param string $_scheme
     */
    public function setScheme($scheme)
    {
        $this->_scheme = $scheme;
    }
}
