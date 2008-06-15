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
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Couch_Request
 */
require_once 'Zym/Couch/Request.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Couch_Db
{
    /**
     * Default connection
     *
     * @var Zym_Couch_Connection
     */
    protected static $_defaultConnection;

    /**
     * Current connection
     *
     * @var Zym_Couch_Connection
     */
    protected $_connection;

    /**
     * Database name
     *
     * @var string
     */
    protected $_dbname;

    /**
     * Constructor
     *
     * @param string $dbname
     */
    public function __construct($dbname = null)
    {
        if ($dbname) {
            $this->_dbname = $dbname;
        }
    }

    /**
     * Set a default DB connection
     *
     * @param Zym_Couch_Connection $connection
     */
    public static function setDefaultConnection(Zym_Couch_Connection $connection)
    {
        self::$_defaultConnection = $connection;
    }

    /**
     * Set the connection for this object
     *
     * @param Zym_Couch_Connection $connection
     * @return Zym_Couch_Db
     */
    public function setConnection(Zym_Couch_Connection $connection)
    {
        $this->_connection = $connection;

        return $this;
    }

    /**
     * Get the connection object
     *
     * @throws Zym_Couch_Exception
     * @return Zym_Couch_Connection
     */
    public function getConnection()
    {
        if (!$this->_connection) {
            if (self::$_defaultConnection) {
                /**
                 * @see Zym_Couch_Exception
                 */
                require_once 'Zym/Couch/Exception.php';

                throw new Zym_Couch_Exception('No connection available.');
            }

            $this->_connection = self::$_defaultConnection;
        }

        return $this->_connection;
    }

    /**
     * Get the database name
     *
     * @throws Zym_Couch_Exception
     * @return string
     */
    public function getDbName()
    {
        if (!$this->_dbname) {
            /**
             * @see Zym_Couch_Exception
             */
            require_once 'Zym/Couch/Exception.php';

            throw new Zym_Couch_Exception('No database name set.');
        }

        return $this->_dbname;
    }

    /**
     * Get a request object
     *
     * @param string $url
     * @param string $method
     * @param string|array $data
     * @return Zym_Couch_Request
     */
    public function getRequest($url, $method = Zym_Couch_Request::GET, $data = null)
    {
        $dbPrefix = '/' . $this->_dbname;

        if (strpos($url, $dbPrefix) !== 0) {
            if (strpos($url, '/') === 0) {
                $url = $dbPrefix . '/' . $url;
            } else {
                $url = $dbPrefix . $url;
            }
        }

        return new Zym_Couch_Request($url, $method, $data);
    }

    /**
     * Send a POST request
     *
     * @param string $url
     * @param string|array $data
     * @return Zym_Couch_Response
     */
    public function post($url, $data = null)
    {
        $request = $this->getRequest($url, Zym_Couch_Request::POST, $data);

        return $this->_connection->send($request);
    }

    /**
     * Send a PUT request
     *
     * @param string $url
     * @param string|array $data
     * @return Zym_Couch_Response
     */
    public function put($url, $data = null)
    {
        $request = $this->getRequest($url, Zym_Couch_Request::PUT, $data);

        return $this->_connection->send($request);
    }

    /**
     * Send a GET request
     *
     * @param string $url
     * @return Zym_Couch_Response
     */
    public function get($url)
    {
        $request = $this->getRequest($url, Zym_Couch_Request::GET);

        return $this->_connection->send($request);
    }

    /**
     * Send a DELETE request
     *
     * @param string $url
     * @return Zym_Couch_Response
     */
    public function delete($url)
    {
        $request = $this->getRequest($url, Zym_Couch_Request::DELETE);

        return $this->_connection->send($request);
    }

    /**
     * Get all documents for the current database
     *
     * @return Zym_Couch_Response
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
     * @return Zym_Couch_Response
     */
    public function getItem($id)
    {
        $request = $this->getRequest('/' . $id);

        return $this->_connection->send($request);
    }
}