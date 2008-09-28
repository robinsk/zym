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
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Couch_Response
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
        
        $this->_status = (int) substr(array_shift($rawHeaders), 9, 3);

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

        $response['status'] = $this->_status;

        foreach ($this->_headers as $header => $value) {
            $response[$header] = $value;
        }

        $response['body'] = $this->getBody(true);

        return $response;
    }

    /**
     * Get status code
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Get the response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    /**
     * Get a specific header.
     * Returns null when the header doesn't exist
     *
     * @param string $header
     * @return string|null
     */
    public function getHeader($header)
    {
        if (isset($this->_headers[$header])) {
            return $this->_headers[$header];
        }
        
        return null;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody($decode = true)
    {
        return $decode ? Zend_Json::decode($this->_body) : $this->_body;
    }
}