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
 * @subpackage Request
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Controller_Request_Http
 */
require_once 'Zend/Controller/Request/Http.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Request
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Request_Http extends Zend_Controller_Request_Http  
{
    /**
     * Scheme for http
     *
     */
    const SCHEME_HTTP  = 'http';
    
    /**
     * Scheme for https
     *
     */
    const SCHEME_HTTPS = 'https';
    
    /**
     * Get http scheme
     *
     * @return string
     */
    public function getRequestScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    /**
     * Get http host
     *
     * @return string
     */
    public function getRequestHost()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'])) {
            $name = $_SERVER['SERVER_NAME'];
            $port = $_SERVER['SERVER_PORT'];
         
            if ((!$this->isSecure() && $port == 80) || ($this->isSecure() && $port == 443)) {
                $host = $name;
            } else {
                $host = $name . ':' . $port;
            }
        }
        
        return $host;
    }
    
    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure()
    {
        return ($this->getRequestScheme() === self::SCHEME_HTTPS);
    }
}