<?php
abstract class Zym_CouchDb_Database
{
    protected static $_defaultConnection;

    protected $_connection;

    protected $_dbname;

    public function __construct($dbname = null)
    {
        $this->_dbname = $dbname;
    }

    public static function setDefaultConnection(Zym_CouchDb_Connection $connection)
    {
        self::$_defaultConnection = $connection;
    }

    public function setConnection(Zym_CouchDb_Connection $connection)
    {
        $this->_connection = $connection;

        return $this;
    }

    public function getConnection()
    {
        if (!$this->_connection) {
            if (self::$_defaultConnection) {
                throw new Zym_CouchDb_Exception('No connection available.');
            }

            $this->_connection = self::$_defaultConnection;
        }

        return $this->_connection;
    }

    public function getDbName()
    {
        if (!$this->_dbname) {
            throw new Zym_CouchDb_Exception('No database name set.');
        }

        return $this->_dbname;
    }

    /**
     * Get a request object
     *
     * @param string $url
     * @param string $method
     * @param string|array $data
     * @return Zym_CouchDb_Request
     */
    public function getRequest($url, $method = Zym_CouchDb_Request::GET, $data = null)
    {
        $dbPrefix = '/' . $this->_dbname;

        if (strpos($url, $dbPrefix) !== 0) {
            if (strpos($url, '/') === 0) {
                $url = $dbPrefix . '/' . $url;
            } else {
                $url = $dbPrefix . $url;
            }
        }

        return new Zym_CouchDb_Request($url, $method, $data);
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
        $request = $this->getRequest($url, Zym_CouchDb_Request::POST, $data);

        return $this->_connection->send($request);
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
        $request = $this->getRequest($url, Zym_CouchDb_Request::PUT, $data);

        return $this->_connection->send($request);
    }

    /**
     * Send a GET request
     *
     * @param string $url
     * @return Zym_CouchDb_Response
     */
    public function get($url)
    {
        $request = $this->getRequest($url, Zym_CouchDb_Request::GET);

        return $this->_connection->send($request);
    }

    /**
     * Get all documents for the current database
     *
     * @return Zym_CouchDb_Response
     */
    public function getAllDocs()
    {
        $request = $this->getRequest('/_all_docs');

        return $this->_connection->send($request);
    }

    /**
     * Get item by id
     *
     * @param int|string $id
     * @return Zym_CouchDb_Response
     */
    public function getItem($id)
    {
        $request = $this->getRequest('/' . $id);

        return $this->_connection->send($request);
    }
}