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
    const TIMEOUT          = 'Timeout';

    /**
     * Timeout Infinite
     */
    const TIMEOUT_INFINITE = 'Infinite';

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
        // Create default http client
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
     * @param string $path
     * @return Zend_Http_Client
     */
    public function getHttpClient($path = null)
    {
        // Return with path set
        if ($path !== null) {
            $client = $this->_httpClient;
            $client->setUri($this->getServer() . $this->_cleanPath($path));

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
    public function createCollection($path)
    {
        $client = $this->getHttpClient($path);

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
        $client = $this->getHttpClient($path);

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
        $client =  $this->getHttpClient($path);
        $client->setHeaders(array(
                   Zend_Http_Client::CONTENT_LENGTH => strlen($data),
                   Zend_Http_Client::CONTENT_TYPE   => 'application/octet-stream'
               ))
               ->setRawData($data);

        $response = $client->request('PUT');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        // Reset obj
        $client->setHeaders(array(
               Zend_Http_Client::CONTENT_TYPE   => 'text/xml',
               Zend_Http_Client::CONTENT_LENGTH => null,
               ))
               ->setRawData(null);
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
     * @param string  $source
     * @param string  $destination
     * @param boolean $overwrite
     * @param string  $depth
     */
    public function copy($source, $destination, $overwrite = null, $depth = null)
    {
        $client = $this->getHttpClient($source);
        $client->setHeaders(array(
                   self::DESTINATION => $this->getServer() . $this->_cleanPath($destination)
               ));

        // Overwrite
        if ($overwrite !== null) {
            $overwrite = ($overwrite) ? self::OVERWRITE_T : self::OVERWRITE_F;

            $client->setHeaders(array(self::OVERWRITE => $overwrite));
        }

        // Depth
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        $response = $client->request('COPY');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        // Reset
        $client->setHeaders(array(
               self::DESTINATION => null,
               self::OVERWRITE   => null,
               self::DEPTH       => null
        ));
    }

    /**
     * Move a resource
     *
     * @param string $source
     * @param string $destination
     * @param string $overwrite   {@see self::OVERWRITE_T} or {@see self::OVERWRITE_F}
     * @param string $depth
     */
    public function move($source, $destination, $overwrite = null, $depth = null)
    {
        $client = $this->getHttpClient($source);
        $client->setHeaders(array(
                   self::DESTINATION => $this->getServer() . $this->_cleanPath($destination)
               ));

        // Overwrite
        if ($overwrite !== null) {
            $overwrite = ($overwrite) ? self::OVERWRITE_T : self::OVERWRITE_F;

            $client->setHeaders(array(self::OVERWRITE => $overwrite));
        }

        // Depth
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        $response = $client->request('MOVE');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        // Reset
        $client->setHeaders(array(
               self::DESTINATION => null,
               self::OVERWRITE   => null,
               self::DEPTH       => null
        ));
    }

    /**
     * Lock resource
     *
     * @param  string  $path
     * @param  string  $owner
     * @param  string  $scope
     * @param  integer $timeout
     * @param  string  $depth
     * @return string  Lock Token
     */
    public function lock($path, $owner, $scope, $timeout = null, $depth = null)
    {
        $client = $this->getHttpClient($path);

        if ($timeout !== null) {
            $client->setHeaders(array(self::TIMEOUT => 'Second-' . (string) $timeout));
        }

        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        // Create request
        $dom = new DomDocument('1.0', 'UTF-8');
        $root = $dom->createElementNS('DAV:', 'D:lockinfo');

        // Scope (self::LOCK_SCOPE_*)
        $lockScope = $dom->createElementNS('DAV:', 'D:lockscope');
        $lockScope->appendChild($dom->createElementNS('DAV:', 'D:' . $scope));
        $root->appendChild($lockScope);

        // Lock type
        $lockType = $dom->createElementNS('DAV', 'D:locktype', self::LOCK_TYPE_WRITE);
        $root->appendChild($lockType);

        //TODO: currently too lazy to allow array specification of owner
        $lockOwner = $dom->createElementNS('DAV', 'D:owner', $owner);
        $root->appendChild($lockOwner);

        $dom->appendChild($root);
        $client->setRawData($dom->saveXML());

        $response = $client->request('LOCK');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        // Parse multistatus to ensure success
        $return = $this->_parseLock($response->getBody());
        return $return;
    }

    /**
     * Refresh lock token
     *
     * @param string  $path
     * @param string  $token
     * @param integer $timeout
     * @return array
     */
    public function refreshLockToken($path, $token, $timeout = null)
    {
        $client = $this->getHttpClient($path);

        if ($timeout !== null) {
            $client->setHeaders(array(self::TIMEOUT => 'Second-' . (string) $timeout));
        }

        $client->setHeaders(array('If' => sprintf('(<%s>)', $token)));

        // Create request
        $dom = new DomDocument('1.0', 'UTF-8');
        $root = $dom->createElementNS('DAV:', 'D:lockinfo');

        // Scope (self::LOCK_SCOPE_*)
        $lockScope = $dom->createElementNS('DAV:', 'D:lockscope');
        $lockScope->appendChild($dom->createElementNS('DAV:', 'D:' . $scope));
        $root->appendChild($lockScope);

        // Lock type
        $lockType = $dom->createElementNS('DAV', 'D:locktype', self::LOCK_TYPE_WRITE);
        $root->appendChild($lockType);

        //TODO: currently too lazy to allow array specification of owner
        $lockOwner = $dom->createElementNS('DAV', 'D:owner', $owner);
        $root->appendChild($lockOwner);

        $dom->appendChild($root);
        $client->setRawData($dom->saveXML());

        $response = $client->request('LOCK');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        // Parse multistatus to ensure success
        $return = $this->_parseLock($response->getBody());
        return $return;
    }

    /**
     * Unlock a file or collection
     *
     * @param  string  $path
     * @param  string  $lockToken
     * @return boolean
     */
    public function unlock($path, $lockToken)
    {
        $client = $this->getHttpClient($path);
        $client->setHeaders(array(
                   self::LOCK_TOKEN => sprintf('<%s>', $lockToken)
               ));

        $response = $client->request('UNLOCK');
        if ($response->isError()) {
            return false;
        }

        // Reset
        $client->setHeaders(array(
                   self::LOCK_TOKEN => null
               ));

        return true;
    }

    /**
     * Delete a file or collection
     *
     * @param string $path
     */
    public function delete($path)
    {
        $client = $this->getHttpClient($path);

        $response = $client->request('DELETE');
        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }
    }

    /**
     * Find property
     *
     * @param  string $path
     * @param  array  $properties
     * @param  mixed  $depth
     * @return array
     */
    public function findProperty($path, array $properties = array(), $depth = null)
    {
        $client = $this->getHttpClient($path);

        // Depth
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        // Properties
        if (count($properties)) {
            $header = '<?xml version="1.0" encoding="UTF-8"?>'
                        . '<propfind xmlns="DAV:"></propfind>';
            $xml  = @simplexml_load_string($header);
            $prop = $xml->addChild('prop', null, 'DAV:');
            foreach ($properties as $property) {
                $prop->addChild($property, null, 'DAV:');
            }

            $client->setRawData($xml->asXML());
        }

        $response = $client->request('PROPFIND');

        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        $return = $this->_parsePropFind($response->getBody());

        // Reset
        $client->setHeaders(array(self::DEPTH => null))
               ->setRawData(null);

        return $return;
    }

    /**
     * Get property List
     *
     * @param string $path
     * @param string $depth
     */
    public function getPropertyList($path, $depth = null)
    {
        $client = $this->getHttpClient($path);

        // Depth
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        // Build Request
        $header = '<?xml version="1.0" encoding="UTF-8"?>'
                    . '<propfind xmlns="DAV:"></propfind>';
        $xml = @simplexml_load_string($header);
        $xml->addChild('propname', null, 'DAV:');
        $client->setRawData($xml->asXML());

        $response = $client->request('PROPFIND');

        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        $return = $this->_parsePropName($response->getBody());

        // Reset
        $client->setHeaders(array(self::DEPTH => null))
               ->setRawData(null);

        return $return;
    }

    /**
     * Set property
     *
     * @param string $path
     * @param string $name
     * @param mixed  $value
     * @param string $namespaceUri
     */
    public function setProperty($path, $name, $value, $namespaceUri = null)
    {
        $client = $this->getHttpClient($path);

        // Depth
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        if ($namespaceUri === null) {
            $namespaceUri = 'DAV:';
        }

        // Create request
        $dom = new DomDocument('1.0', 'UTF-8');

        $root = $dom->createElementNS('DAV:', 'D:propertyupdate');
        $set  = $dom->createElementNS('DAV:', 'D:set');
        $prop = $dom->createElementNS('DAV', 'D:prop');
        $item = $dom->createElementNS($namespaceUri, $name, $value);

        $prop->appendChild($item);
        $set->appendChild($prop);
        $root->appendChild($set);
        $dom->appendChild($root);

        $client->setRawData($xml);

        $response = $client->request('PROPPATCH');

        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        // Reset
        $client->setHeaders(array(self::DEPTH => null))
               ->setRawData(null);

        // Parse multistatus to ensure success
        $return = $this->_parsePropPatch($response->getBody());
        foreach ($return as $href) {
            foreach ($href as $prop => $propStatus) {
                if ($prop == $name) {
                    $statusCode = $this->_extractCode($propStatus);

                    if ($statusCode == 200) {
                        return;
                    }

                    $statusMessage = $this->_extractMessage($propStatus);

                    require_once 'Zym/WebDav/Client/Exception.php';
                    throw new Zym_WebDav_Client_Exception($statusCode. ' ' . $statusMessage);
                }
            }
        }

        require_once 'Zym/WebDav/Client/Exception.php';
        throw new Zym_WebDav_Client_Exception(sprintf('Property "%s" with value "%s" could not be set', $name, $value));
    }

    /**
     * Remove property
     *
     * @param string $path
     * @param string $name
     * @param string $namespaceUri
     */
    public function removeProperty($path, $name, $namespaceUri = null)
    {
        $client = $this->getHttpClient($path);

        // Depth
        if ($depth !== null) {
            $client->setHeaders(array(self::DEPTH => $depth));
        }

        if ($namespaceUri === null) {
            $namespaceUri = 'DAV:';
        }

        // Create request
        $dom = new DomDocument('1.0', 'UTF-8');

        $root = $dom->createElementNS('DAV:', 'D:propertyupdate');
        $set  = $dom->createElementNS('DAV:', 'D:remove');
        $prop = $dom->createElementNS('DAV', 'D:prop');
        $item = $dom->createElementNS($namespaceUri, $name);

        $prop->appendChild($item);
        $set->appendChild($prop);
        $root->appendChild($set);
        $dom->appendChild($root);

        $client->setRawData($xml);

        $response = $client->request('PROPPATCH');

        if ($response->isError()) {
            require_once 'Zym/WebDav/Client/Exception.php';
            throw new Zym_WebDav_Client_Exception($response->getStatus() . ' ' . $response->getMessage());
        }

        $client->setHeaders(array(self::DEPTH => null))
               ->setRawData(null);

        // Parse multistatus to ensure success
        $return = $this->_parsePropPatch($response->getBody());
        foreach ($return as $href) {
            foreach ($href as $prop => $propStatus) {
                if ($prop == $name) {
                    $statusCode = $this->_extractCode($propStatus);

                    if ($statusCode == 200) {
                        return;
                    }

                    $statusMessage = $this->_extractMessage($propStatus);

                    require_once 'Zym/WebDav/Client/Exception.php';
                    throw new Zym_WebDav_Client_Exception($statusCode. ' ' . $statusMessage);
                }
            }
        }

        require_once 'Zym/WebDav/Client/Exception.php';
        throw new Zym_WebDav_Client_Exception(sprintf('Property "%s" with value "%s" could not be removed', $name, $value));
    }

    /**
     * Get Allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $client = $this->getHttpClient('');

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
        $client = $this->getHttpClient('');

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
     * @param  string  $method
     * @return boolean
     */
    public function isMethodAllowed($method)
    {
        $methods = $this->getAllowedMethods();
        return in_array(strtoupper($method), $methods);
    }

    /**
     * Return whether the capability is supported
     *
     * @param  string  $capability
     * @return boolean
     */
    public function isCapabilitySupported($capability)
    {
        $capabilities = $this->getDavCapabilities();
        return in_array($capability, $capabilities);
    }

    /**
     * Cleans path
     *
     * @param  string $path
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

    /**
     * Extract the response code from a response string
     *
     * @param  string  $response_str
     * @return integer
     */
    protected function _extractCode($response)
    {
        preg_match("|^HTTP/[\d\.x]+ (\d+)|", $response, $m);

        if (isset($m[1])) {
            return (int) $m[1];
        } else {
            return false;
        }
    }

    /**
     * Extract the HTTP message from a response
     *
     * @param  string $response_str
     * @return string
     */
    protected function _extractMessage($response)
    {
        preg_match("|^HTTP/[\d\.x]+ \d+ ([^\r\n]+)|", $response, $m);

        if (isset($m[1])) {
            return $m[1];
        } else {
            return false;
        }
    }

    /**
     * Parse propfind request
     *
     * @param  string $xml
     * @return array
     */
    protected function _parsePropFind($xml)
    {
        $xml     = @simplexml_load_string($xml);
        $return  = array();
        if (!$xml instanceof SimpleXMLElement) {
            return $return;
        }

        foreach ($xml->children('DAV:') as $response) {
            $href = (string) urldecode($response->href);
            $return[$href] = array();

            foreach ($response->propstat as $propstat) {
                foreach ($propstat->prop as $prop) {
                    $return[$href] = $this->_toArray($propstat->prop);
                }
            }
        }

        return $return;
    }

    /**
     * Parse property names from propname request
     *
     * @param  string $xml
     * @return array
     */
    protected function _parsePropName($xml)
    {
        $xml     = @simplexml_load_string($xml);
        $return  = array();
        if (!$xml instanceof SimpleXMLElement) {
            return $return;
        }

        foreach ($xml->children('DAV:') as $response) {
            $href = (string) urldecode($response->href);
            $return[$href] = array();

            foreach ($response->propstat as $propstat) {
                foreach ($propstat->prop as $prop) {
                    foreach ($prop as $name => $value) {
                        $return[$href][] = $name;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Parse proppatch request
     *
     * @param  string $xml
     * @return array
     */
    protected function _parsePropPatch($xml)
    {
        $xml     = @simplexml_load_string($xml);
        $return  = array();
        if (!$xml instanceof SimpleXMLElement) {
            return $return;
        }

        foreach ($xml->children('DAV:') as $response) {
            $href = (string) urldecode($response->href);
            $return[$href] = array();

            foreach ($response->propstat as $propstat) {
                foreach ($propstat->prop as $prop) {
                    $return[$href] = array_combine($this->_toArray($propstat->prop), $propstat->status);
                }
            }
        }

        return $return;
    }

    /**
     * Parse lock response
     *
     * @param string $xml
     * @return array
     */
    protected function _parseLock($xml)
    {
        /*
        <?xml version="1.0" encoding="utf-8" ?>
        <D:prop xmlns:D="DAV:">
            <D:lockdiscovery>
                <D:activelock>
                <D:locktype><D:write/></D:locktype>
                <D:lockscope><D:exclusive/></D:lockscope>
                <D:depth>Infinity</D:depth>
                <D:owner>
                    <D:href>
                    http://www.ics.uci.edu/~ejw/contact.html
                    </D:href>
                </D:owner>
                <D:timeout>Second-604800</D:timeout>
                <D:locktoken>
                    <D:href>
                    opaquelocktoken:e71d4fae-5dec-22d6-fea5-00a0c91e6be4
                    </D:href>
                </D:locktoken>
                </D:activelock>
            </D:lockdiscovery>
        </D:prop>
        */

        $xml     = @simplexml_load_string($xml);
        $return  = array();
        if (!$xml instanceof SimpleXMLElement) {
            return $return;
        }

        $return = $this->_toArray($xml);
        echo $return;
        if (isset($return['lockdiscovery']['activelock'])) {
            return $return;
        }

        return array();
    }

    /**
     * Returns a string or an associative and possibly multidimensional array from
     * a SimpleXMLElement.
     *
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array|string
     */
    protected function _toArray(SimpleXMLElement $xmlObject)
    {
        $config = array();

        foreach ($xmlObject->getNamespaces() as $namespace) {
            // Search for parent node values
            if (count($xmlObject->attributes()) > 0) {
                foreach ($xmlObject->attributes() as $key => $value) {
                    $value = (string) $value;

                    if (array_key_exists($key, $config)) {
                        if (!is_array($config[$key])) {
                            $config[$key] = array($config[$key]);
                        }

                        $config[$key][] = $value;
                    } else {
                        $config[$key] = $value;
                    }
                }
            }

            // Search for children
            if (count($xmlObject->children($namespace)) > 0) {
                foreach ($xmlObject->children($namespace) as $key => $value) {
                    foreach ($value->getNamespaces() as $namespace) {
                        if (count($value->children($namespace)) > 0) {
                            $value = $this->_toArray($value);
                        } else if (count($value->attributes($namespace)) > 0) {
                            $attributes = $value->attributes($namespace);
                            if (isset($attributes['value'])) { // leaving it for now
                                $value = (string) $attributes['value'];
                            } else {
                                $value = $this->_toArray($value);
                            }
                        } else {
                            $value = (string) $value;
                        }


                        if (array_key_exists($key, $config)) {
                            if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
                                $config[$key] = array($config[$key]);
                            }

                            $config[$key][] = $value;
                        } else {
                            $config[$key] = $value;
                        }
                    }
                }
            }
        }

        if (count($config) === 0) {
            // Object has no children nor attributes
            // attribute: it's a string
            $config = (string) $xmlObject;
        }

        return $config;
    }
}