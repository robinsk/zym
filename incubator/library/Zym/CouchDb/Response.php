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

        list($this->_headers, $this->_body) = explode("\r\n\r\n", $rawResponse);
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
    public function getReponse()
    {
        $response = array();

        $headers = explode("\r\n", $this->_rawResponse);

        foreach ($headers as $header) {
            list($key, $value) = explode(': ', $header);
            $response[$key] = $value;
        }

        return $response;
    }

    /**
     * Get the response headers
     *
     * @return string
     */
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