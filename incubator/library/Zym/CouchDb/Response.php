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
class Zym_CouchDb_Response
{
    /**
     * The raw response message
     *
     * @var string
     */
    protected $_rawResponse;

    /**
     * Response status
     *
     * @var int
     */
    protected $_status;

    /**
     * Response headers
     *
     * @var string
     */
    protected $_headers;

    /**
     * Response body
     *
     * @var string
     */
    protected $_body;

    /**
     * Constructor
     *
     * @param string $response
     */
    public function __construct($rawResponse)
    {
        $this->_rawResponse = $rawResponse;

        list($rawHeaders, $this->_body) = explode("\r\n\r\n", $rawResponse);

        $rawHeaders = explode("\r\n", $rawHeaders);

        $this->_status = (int) substr(array_shift($rawHeaders), 8, 3);

        $headers = array();

        foreach ($rawHeaders as $header) {
            list($key, $value) = explode(': ', $header);
            $headers[$key] = $value;
        }

        $this->_headers = $headers;
    }

    /**
     * Get the raw response
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }

    /**
     * Get the response as an array
     *
     * @return array
     */
    public function toArray()
    {
        $response = array();

        $headers = explode("\r\n", $this->_rawResponse);

        $response['status'] = array_shift($headers);

        foreach ($this->_headers as $header => $value) {
        	$response[$header] = $value;
        }

        $response['body'] = $this->_body;

        return $response;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody($decode = false)
    {
        return $decode ? Zend_Json::decode($this->_body) : $this->_body;
    }
}