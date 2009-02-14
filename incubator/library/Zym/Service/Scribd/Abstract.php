<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Service_Scribd_Abstract
 */
require_once 'Zym/Service/Scribd/Abstract.php';

/**
 * Zym Scribd Abstract API Implementation
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_Service_Scribd_Abstract
{
    /**
     * Get scribd client
     *
     * @var Zym_Service_Scribd
     */
    private $_scribdClient;

    /**
     * Set scribd client
     *
     * @param Zym_Service_Scribd $client
     * @return Zym_Service_Scribd_Abstract
     */
    public function setScribdClient(Zym_Service_Scribd $client)
    {
        $this->_scribdClient = $client;
        return $this;
    }

    /**
     * Get scribd client
     *
     * @return Zym_Service_Scribd
     */
    public function getScribdClient()
    {
        return $this->_scribdClient;
    }


    /**
     * Prepare options for the request
     *
     * @param  string $method         Scribd Method to call
     * @param  array  $options        User Options
     * @param  array  $defaultOptions Default Options
     *
     * @return array Merged array of user and default/required options
     */
    protected function _prepareOptions($method, array $options, array $defaultOptions = array())
    {
        $scribdClient = $this->getScribdClient();

        $sessionKey = $scribdClient->getUser()->getSessionKey();
        $phantomId  = $scribdClient->getUser()->getPhantomId();

        $authOptions = array();
        if ($sessionKey !== null) {
            $authOptions['session_key'] = $sessionKey;
        }

        if ($phantomId !== null) {
            $authOptions['my_user_id'] = $phantomId;
        }

        $defaultOptions = array_merge($authOptions, $defaultOptions);

        $options['method']  = (string) $method;
        $options['api_key'] = $scribdClient->getApiKey();


        $options = array_merge($defaultOptions, $options);
        if ($scribdClient->useSignRequest()) {
            $options['api_sig'] = $this->_calculateRequestHash($options);
        }

        return $options;
    }

    /**
     * Calculate request hash for signing
     *
     * @param array $options
     * @return string
     */
    protected function _calculateRequestHash(array $options)
    {
        ksort($options);

        $input = $this->getScribdClient()->getSecretKey();
        foreach ($options as $key => $value) {
            if ($key == 'file') {
                continue;
            }

            $input .= $key . $value;
        }

        return md5($input);
    }

    /**
     * Perform a rest get throwing an exception if result failed
     *
     * @param string $method
     * @param array  $options
     * @param array  $defaultOptions
     * @return Zend_Rest_Client_Result
     */
    protected function _restGet($method, array $options, array $defaultOptions = array())
    {
        $options  = $this->_prepareOptions($method, $options, $defaultOptions);
        $response = $this->getScribdClient()->getRestClient()->restGet('api', $options);

        if ($response->isError()) {
            $code = $response->extractCode($response->asString());

            /**
             * @see Zym_Service_Scribd_Exception
             */
            require_once 'Zym/Service/Scribd/Exception.php';
            throw new Zym_Service_Scribd_Exception($response->getMessage(), $code);
        }

        return $this->_handleResponse($response);
    }

    /**
     * Perform a rest post throwing an exception if result failed
     *
     * @param string $method
     * @param array  $options
     * @param array  $defaultOptions
     * @return Zend_Rest_Client_Result
     */
    protected function _restPost($method, array $options, array $defaultOptions = array())
    {
        $options  = $this->_prepareOptions($method, $options, $defaultOptions);
        $response = $this->getScribdClient()->getRestClient()->restPost('api', $options);

        if ($response->isError()) {
            $code = $response->extractCode($response->asString());

            /**
             * @see Zym_Service_Scribd_Exception
             */
            require_once 'Zym/Service/Scribd/Exception.php';
            throw new Zym_Service_Scribd_Exception($response->getMessage(), $code);
        }

        return $this->_handleResponse($response);
    }

    /**
     * Perform a rest post throwing an exception if result failed
     *
     * @param string $method
     * @param array  $options
     * @param array  $defaultOptions
     * @return Zend_Rest_Client_Result
     */
    protected function _restFileUpload($method, $file, $param, array $options, array $defaultOptions = array())
    {
        $options  = $this->_prepareOptions($method, $options, $defaultOptions);

        $client = Zend_Rest_Client::getHttpClient();
        $client->setUri($this->getScribdClient()->getRestClient()->getUri());

        $client->setParameterGet($options);
        $client->setFileUpload($file, $param);
        $response = $client->request('POST');

        echo $client->getLastRequest();
        echo $client->getLastResponse()->getBody();exit;


        if ($response->isError()) {
            $code = $response->extractCode($response->asString());

            /**
             * @see Zym_Service_Scribd_Exception
             */
            require_once 'Zym/Service/Scribd/Exception.php';
            throw new Zym_Service_Scribd_Exception($response->getMessage(), $code);
        }

        return $this->_handleResponse($response);
    }

    /**
     * Handle response
     *
     * @param Zend_Http_Response $response
     * @return SimpleXmlElement
     */
    public function _handleResponse(Zend_Http_Response $response)
    {
        set_error_handler(array($this, 'handleXmlErrors'));
        $xml = simplexml_load_string($response->getBody());
        if($xml === false) {
            $this->handleXmlErrors(0, "An error occured while parsing the REST response with simplexml.");
        } else {
            restore_error_handler();
        }

        if (isset($xml->error['code'])) {
            /**
             * @see Zym_Service_Scribd_Exception
             */
            require_once 'Zym/Service/Scribd/Exception.php';
            throw new Zym_Service_Scribd_Exception((string)$xml->error['message'], (int)$xml->error['code']);
        }

        return $xml;
    }

    /**
     * Temporary error handler for parsing REST responses.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param array  $errcontext
     * @throws Zend_Result_Client_Result_Exception
     */
    public function handleXmlErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
    {
        restore_error_handler();
        require_once "Zend/Rest/Client/Result/Exception.php";
        throw new Zend_Rest_Client_Result_Exception("REST Response Error: ".$errstr);
    }
}