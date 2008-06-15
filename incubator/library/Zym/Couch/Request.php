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
 * @see Zym_Couch_Response
 */
require_once 'Zym/Couch/Response.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Couch_Request
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
     * Carriage return linefeed constant
     *
     * @var string
     */
    const CRLF = "\r\n";

    /**
     * HTTP method
     *
     * @var string
     */
    protected $_method = self::GET;

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
     * @var Zym_Couch_Response
     */
    protected $_response;

    /**
     * Constructor
     *
     * @param string $url
     * @param string $method
     * @param string|array $data
     */
    public function __construct($url, $method = self::GET, $data = null)
    {
        $this->_method = strtoupper($method);

        $validMethods = array(self::GET, self::POST, self::PUT, self::DELETE);

        if (!in_array($this->_method, $validMethods)) {
            /**
             * @see Zym_Couch_Exception
             */
            require_once 'Zym/Couch/Exception.php';

            throw new Zym_Couch_Exception('Invalid HTTP method: ' . $this->_method);
        }

        if (is_array($data)) {
            $data = Zend_Json::encode($data);
        }

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

        $date = new Zend_Date();
        $request .= 'Date: ' . $date->toString(Zend_Date::RFC_2822);

        if ($this->_data) {
            $request .= 'Content-Length: ' . strlen($this->_data) . self::CRLF;
            $request .= 'Content-Type: application/json' . self::CRLF . self::CRLF;
            $request .= $this->_data . self::CRLF;
        } else {
            $request .= self::CRLF;
        }

        return $request;
    }

    /**
     * Get the reponse object
     *
     * @return Zym_Couch_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}