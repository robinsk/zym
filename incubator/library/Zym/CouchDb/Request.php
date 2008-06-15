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
 * @see Zym_CouchDb
 */
require_once 'Zym/CouchDb.php';

/**
 * @see Zym_CouchDb_Response
 */
require_once 'Zym/CouchDb/Response.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_CouchDb_Request
{
    /**
     * Carriage return linefeed constant
     *
     * @var string
     */
    const CRLF = "\r\n";

    /**
     * Hostname
     *
     * @var string
     */
    protected $_host;

    /**
     * HTTP method
     *
     * @var string
     */
    protected $_method = Zym_CouchDb::GET;

    /**
     * Request URL
     *
     * @var string
     */
    protected $_url;

    /**
     * Request data
     *
     * @var string
     */
    protected $_data;

    /**
     * Response message
     *
     * @var Zym_CouchDb_Response
     */
    protected $_response;

    /**
     * Constructor
     *
     * @param string $host
     * @param int $port
     * @param string $url
     * @param string $method
     * @param string|array $data
     */
    public function __construct($host, $port = 5984, $url, $method = Zym_CouchDb::GET, $data = null)
    {
        $this->_method = strtoupper($method);

        $validMethods = array(Zym_CouchDb::GET,
                              Zym_CouchDb::POST,
                              Zym_CouchDb::PUT,
                              Zym_CouchDb::DELETE);

        if (!in_array($this->_method, $validMethods)) {
            /**
             * @see Zym_CouchDb_Exception
             */
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Invalid HTTP method: ' . $this->_method);
        }

        if (is_array($data)) {
            $data = Zend_Json::encode($data);
        }

        $this->_host = $host;
        $this->_port = $port;
        $this->_url = $url;
        $this->_data = $data;
    }

    /**
     * Get the raw request
     *
     * @return string
     */
    public function getRawRequest()
    {
        $request = $this->_method . ' ' . $this->_url . ' HTTP/1.0' . self::CRLF;
        $request .= 'Host: ' . $this->_host . self::CRLF;

        if ($this->data) {
            $request .= 'Content-Length: ' . strlen($this->_data) . self::CRLF;
            $request .= 'Content-Type: text/javascript' . self::CRLF . self::CRLF;
            $request .= $this->_data . self::CRLF;
        } else {
            $request .= self::CRLF;
        }

        return $request;
    }

    /**
     * Send the request and return the response
     *
     * @return Zym_CouchDb_Response
     */
    public function send()
    {
        $errorString = '';
        $errorNumber = '';
        $response    = '';

        $socket = fsockopen($this->_host, $this->_port, $errorNumber, $errorString);

        if (!$socket) {
            /**
             * @see Zym_CouchDb_Exception
             */
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Failed to open connection to ' . $this->_host . ':' .
                                            $this->_port . ' (Error number ' . $errorNumber . ': ' .
                                            $errorString . ')');
        }

        fwrite($socket, $this->getRawRequest());

        while (!feof($socket)) {
            $response .= fgets($socket);
        }

        $this->_response = new Zym_CouchDb_Response($response);

        fclose($socket);

        $socket = null;

        return $this->getResponse();
    }

    /**
     * Get the reponse object
     *
     * @return Zym_CouchDb_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}