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
    const HEADER_DAV = 'DAV';
    const HEADER_DEPTH = 'Depth';
    const HEADER_DESTINATION = 'Destination';
    const HEADER_IF = 'If';
    const HEADER_LOCK_TOKEN = 'Lock-Token';
    const HEADER_OVERWRITE = 'Overwrite';
    const HEADER_STATUS_URI = 'Status-URI';
    
    const DEPTH          = 'Depth';
    const DEPTH_0        = 0;
    const DEPTH_1        = 1;
    const DEPTH_INFINITY = 'infinity';
    
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
        $client->setConfig(array('Zym_WebDav_Client'));
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
    public function getHttpClient()
    {
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
        file_put_contens($destination, $response);
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
                   Zend_Http_Client::CONTENT_TYPEany   => 'application/octet-stream'
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
    
    public function copy($source, $destination, $overwrite = false)
    {
        
    }
    
    public function move($source, $destination, $overwrite = false)
    {
        
    }
    
    public function lock($path, $owner)
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
        foreach ($capabilities as $capability) {
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
        return ltrim($path, '/\\');
    }
}