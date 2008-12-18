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
 * @package   Zym_WebDav_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license   http://www.zym-project.com/license New BSD License
 */
 
/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * Simple WebDav Client
 *
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym
 * @package   Zym_WebDav_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_WebDav_Client
{
    /**
     * DAV header
     *
     * @var string
     */
    const DAV = 'DAV';
    
    /**
     * DAV compatibility 1
     */
    const DAV_LEVEL1 = 1;
    
    /**
     * DAV compatibility 2
     */
    const DAV_LEVEL2 = 2;
    
    /**
     * DAV compatibility with DeltaV
     */
    const DAV_DELTAV = 'deltav';
    
    /**
     * Destination header
     */
    const DESTINATION = 'Destination';
    
    /**
     * Depth header
     */
    const DEPTH          = 'Depth';
    
    /**
     * Depth 0
     */
    const DEPTH_0        = 0;
    
    /**
     * Depth 1
     */
    const DEPTH_1        = 1;
    
    /**
     * Depth Infinity
     */
    const DEPTH_INFINITY = 'infinity';
    
    /**
     * If Header
     */
    const IF = 'If';
    
    /**
     * Lock-Token Header
     */
    const LOCK_TOKEN           = 'Lock-Token';
    
    /**
     * Lock Scope Exclusive
     */
    const LOCK_SCOPE_EXCLUSIVE = 'exclusive';
    
    /**
     * Lock Scope Shared
     */
    const LOCK_SCOPE_SHARED    = 'shared';
    
    /**
     * Lock Type Write
     */
    const LOCK_TYPE_WRITE      = 'write';
    
    /**
     * Overwrite Header
     */
    const OVERWRITE   = 'Overwrite';
    
    /**
     * Overwrite True
     */
    const OVERWRITE_T = 'T';
    
    /**
     * Overwrite False
     */
    const OVERWRITE_F = 'F';
    
    /**
     * Status-URI Header
     */
    const STATUS_URI = 'Status-URI';
    
    /**
     * Timeout Header
     */
    const TIMEOUT = 'Timeout';
    
    /**
     * Timeout Infinite
     */
    const TIMEOUT_INFINITY = 2147483647;
    
    /**
     * WebDav Server
     *
     * A string http://foo.com to the root of the webdav
     *
     * @var string
     */
    private $_server;
    
    /**
     * Http client
     *
     * @var Zend_Http_Client
     */
    private $_httpClient;
    
    /**
     * Construct
     *
     * @param string $server
     * @param string $username
     * @param string $password
     */
    public function __construct($server, $username = null, $password = null)
    {
        $client = new Zend_Http_Client();
        $client->setConfig(array('useragent' =>  'Zym_WebDav_Client'));
        $client->setAuth($username, $password);
        
        $this->setHttpClient($client);
        $this->setServer($server);
    }

    /**
     * Set Http Client
     *
     * @param Zend_Http_Client $client
     * @return Zym_WebDav_Client
     */
    public function setHttpClient(Zend_Http_Client $client)
    {
        $client->setHeaders(Zend_Http_Client::CONTENT_TYPE, 'text/xml');
        $this->_httpClient = $client;
        
        return $this;
    }
    
    /**
     * Get Http Client
     *
     * @return Zym_WebDav_Client
     */
    public function getHttpClient($path = null)
    {
        if ($path !== null) {
            $client = clone $this->_httpClient;
            $client->setUri(rawurlencode($this->getServer() . $this->_cleanPath($path)));
            return $client;
        }
        
        return $this->_httpClient;
    }
    
    /**
     * Set Server Url
     *
     * @param string $server
     * @return Zym_WebDav_Client
     */
    public function setServer($server)
    {
        $path        = parse_url($server, PHP_URL_PATH);
        $encodedPath = explode('/', $path);
        
        foreach($encodedPath as &$item) {
            $item = rawurlencode($item);
        }
        unset($item);
        
        $encodedPath = implode('/', $encodedPath);

        $server      = substr($server, 0, strrpos($server, $path)) . '/' . $encodedPath;
        
        $this->_server = rtrim($server, '/\\') . '/';
        return $this;
    }
    
    /**
     * Get Server
     *
     * @return string
     */
    public function getServer()
    {
        return $this->_server;
    }
    
    /**
     * Create a new collection/directory
     *
     * @param string $path
     */
    public function mkcol($path)
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer() . $this->_cleanPath($path));
        
        $response = $client->request('MKCOL');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
    }
    
    /**
     * Get a file
     *
     * @param string $path
     * @return string
     */
    public function get($path)
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer() . $this->_cleanPath($path));
        
        $response = $client->request('GET');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
        
        return $response->getBody();
    }
    
    /**
     * Retrieve file from WebDav
     *
     * @param string $path
     * @param string $destination
     */
    public function getToFile($path, $destination)
    {
        $response = $this->get($path);
        file_put_contents($destination, $response);
    }
    
    /**
     * Put a file
     *
     * @param string $path
     * @param string $data
     */
    public function put($path, $data)
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer() . $this->_cleanPath($path))
               ->setHeaders(array(
                   Zend_Http_Client::CONTENT_LENGTH => strlen($data),
                   Zend_Http_Client::CONTENT_TYPE   => 'application/octet-stream'
               ))
               ->setRawData($data);
        
        $response = $client->request('PUT');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
    }
    
    /**
     * Perform a PUT from file
     *
     * @param string $path
     * @param string $file
     */
    public function putFromFile($path, $file)
    {
        $contents = file_get_contents($file);
        return $this->put($path, $contents);
    }
    
    /**
     * Copy a resource
     *
     * @param string $source
     * @param string $destination
     * @param boolean $overwrite
     * @param string $depth
     */
    public function copy($source, $destination, $overwrite = null, $depth = null)
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer() . $this->_cleanPath($source))
               ->setHeaders(array(
                   self::DESTINATION => $this->getServer() . $this->_cleanPath($destination)
               ));
        
        if ($overwrite !== null) {
            $overwrite = ($overwrite) ? self::OVERWRITE_T : self::OVERWRITE_F;

            $client->setHeaders(array(self::OVERWRITE => $overwrite));
        }
        
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }
        
        $response = $client->request('COPY');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
    }
    
    /**
     * Move a resource
     *
     * @param string $source
     * @param string $destination
     * @param boolean $overwrite
     * @param string $depth
     */
    public function move($source, $destination, $overwrite = null, $depth = null)
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer() . $this->_cleanPath($source))
               ->setHeaders(array(
                   self::DESTINATION => $this->getServer() . $this->_cleanPath($destination)
               ));
        
        if ($overwrite !== null) {
            $overwrite = ($overwrite) ? self::OVERWRITE_T : self::OVERWRITE_F;

            $client->setHeaders(array(self::OVERWRITE => $overwrite));
        }
        
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }
        
        $response = $client->request('MOVE');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
    }
    
    public function lock($path, $owner, $scope, $timeout, $depth = null)
    {
        
    }
    
    public function refreshOpaqueLockToken($path, $token, $timeout)
    {
        
    }
    
    public function unlock($path, $owner)
    {
        
    }
    
    public function delete($path)
    {
        
    }
    
    public function propfind($path, $properties = null, $depth = null)
    {}
    
    /**
     * Get Allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer());
        
        $response = $client->request('OPTIONS');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
        
        $allow = explode(',', $response->getHeader('Allow'));
        return $allow;
    }
    
    /**
     * Get DAV capabilities
     *
     * @return array
     */
    public function getDavCapabilities()
    {
        $client = clone $this->getHttpClient();
        $client->setUri($this->getServer());
        
        $response = $client->request('OPTIONS');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
        
        $capabilities = $response->getHeader('DAV');
        $return       = array();
        foreach ((array) $capabilities as $capability) {
            if (strpos($capability, ',') !== false) {
                $return = array_merge($return, explode(',', $capability));
            } else {
                $return[] = $capability;
            }
        }
        
        return $return;
    }
    
    /**
     * Return whether method is allowed
     *
     * @param string $method
     * @return boolean
     */
    public function isAllowed($method)
    {
        $methods = $this->getAllowedMethods();
        return in_array(strtoupper($method), $methods);
    }
    
    /**
     * Return whether the capability is supported
     *
     * @param string $capability
     * @return boolean
     */
    public function isSupported($capability)
    {
        $capabilities = $this->getDavCapabilities();
        return in_array($capability, $capabilities);
    }
    
    /**
     * Cleans path
     *
     * @param string $path
     * @return string
     */
    protected function _cleanPath($path)
    {
        $path = explode('/', $path);
        foreach($path as &$item) {
            $item = rawurlencode($item);
        }
        unset($item);
        
        $path = implode('/', $path);

        return ltrim($path, '/\\');
    }
}