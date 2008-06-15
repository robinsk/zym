<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_CouchDb_Request
 */
require_once 'Zym/CouchDb/Request.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_CouchDb
{
    /**
     * Request type constants
     *
     * @var string
     */
    const GET    = 'GET';
    const POST   = 'POST';
    const PUT    = 'PUT';
    const DELETE = 'DELETE';

    /**
     * Database name
     *
     * @var string
     */
    protected $_dbname;

    /**
     * Hostname
     *
     * @var string
     */
    protected $_host;

    /**
     * Port number
     *
     * @var int
     */
    protected $_port;

    /**
     * Constructor
     *
     * @param array|Zend_Config $config
     */
    public function __construct($config)
    {
        $defaults = array('dbname' => '',
                          'host'   => 'localhost',
                          'port'   => 5984);

        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            /**
             * @see Zym_CouchDb_Exception
             */
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Config must be an array or instance of Zend_Config.');
        }

        $config = array_merge($defaults, $config);

        foreach ($config as $key => $value) {
        	if (empty($value)) {
        	    throw new Zym_CouchDb_Exception('Config entry "' . $key . '" can\'t be empty.');
        	}
        }

        $this->_dbname = $config['dbname'];
        $this->_host   = $config['host'];
        $this->_port   = $config['port'];
    }

    /**
     * Send a request
     *
     * @param string $url
     * @param string $method
     * @param string|array $data
     * @return Zym_CouchDb_Response
     */
    public function send($url, $method = self::GET, $data = null)
    {
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }

        $url = '/' . $this->_dbname . $url;

        return $this->_sendRequest($url, $method, $data);
    }

    /**
     * Actually send the request
     *
     * @param string $url
     * @param string $method
     * @param string|array @data
     * @return Zym_CouchDb_Response
     */
    protected function _sendRequest($url, $method = self::GET, $data = null)
    {
        $request = new Zym_CouchDb_Request($this->_host, $this->_port, $url, $method, $data);

        return $request->send();
    }

    /**
     * Send a POST request
     *
     * @param string $url
     * @param string|array $data
     * @return Zym_CouchDb_Response
     */
    public function post($url, $data = null)
    {
        return $this->send($url, self::POST, $data);
    }

    /**
     * Send a PUT request
     *
     * @param string $url
     * @param string|array $data
     * @return Zym_CouchDb_Response
     */
    public function put($url, $data = null)
    {
        return $this->send($url, self::PUT, $data);
    }

    /**
     * Send a GET request
     *
     * @param string $url
     * @param string|array $data
     * @return Zym_CouchDb_Response
     */
    public function get($url)
    {
        return $this->send($url, self::GET);
    }

    /**
     * Send a DELETE request
     *
     * @param string $url
     * @param string|array $data
     * @return Zym_CouchDb_Response
     */
    public function delete($url)
    {
        return $this->send($url, self::DELETE);
    }

    /**
     * List all databases
     *
     * @return Zym_CouchDb_Response
     */
    public function listAll()
    {
        return $this->_sendRequest('/_all_dbs');
    }

    /**
     * Get CouchDb info
     *
     * @return Zym_CouchDb_Response
     */
    public function info()
    {
        return $this->_sendRequest('/');
    }

    /**
     * Get all documents for the current database
     *
     * @return Zym_CouchDb_Response
     */
    public function getAllDocs()
    {
        return $this->send('/_all_docs');
    }

    /**
     * Get item by id
     *
     * @param int|string $id
     * @return Zym_CouchDb_Response
     */
    public function getItem($id)
    {
        return $this->send('/' . $id);
    }
}