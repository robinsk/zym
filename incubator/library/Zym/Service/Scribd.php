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
 * @package   Zym_Service
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license   http://www.zym-project.com/license New BSD License
 */

/**
 * Zym Scribd API Implementation
 *
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym
 * @package   Zym_Service
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Scribd
{
    /**
     * Scribd API address
     *
     */
    const API_URL = 'http://api.scribd.com/api';

    /**
     * Rest client
     *
     * @var Zend_Rest_Client
     */
    private $_restClient;

    /**
     * API Key
     *
     * @var string
     */
    private $_apiKey;

    /**
     * API secret key
     *
     * @var string
     */
    private $_secretKey;

    /**
     * Request signing
     *
     * @var boolean
     */
    private $_signRequest = false;

    /**
     * Document
     *
     * @var Zym_Service_Scrib_Document
     */
    private $_document;

    /**
     * User
     *
     * @var Zym_Service_Scribd_User
     */
    private $_user;

    /**
     * Construct
     *
     * @param string $apiKey
     * @param string $secretKey
     * @param boolean $useRequestSigning
     */
    public function __construct($apiKey, $secretKey = null, $useRequestSigning = null)
    {
        $this->setApiKey($apiKey);

        if ($secretKey !== null) {
            $this->setSecretKey($secretKey);
        }

        if ($useRequestSigning !== null) {
            $this->useSignRequest($useRequestSigning);
        }
    }

    /**
     * Set document
     *
     * @param Zym_Service_Scribd_Document $doc
     * @return Zym_Service_Scribd
     */
    public function setDocument(Zym_Service_Scribd_Document $doc)
    {
        $doc->setScribdClient($this);
        $this->_document = $doc;
        return $this;
    }

    /**
     * Get document
     *
     * @return Zym_Service_Scribd_Document
     */
    public function getDocument($id = null)
    {
        if (! $this->_document instanceof Zym_Service_Scribd_Document || $id !== null) {
            /**
             * @see Zym_Service_Scribd_Document
             */
            require_once 'Zym/Service/Scribd/Document.php';

            if ($id !== null) {
                $doc = new Zym_Service_Scribd_Document($id);
            } else {
                $doc = new Zym_Service_Scribd_Document();
            }

            $this->setDocument($doc);
        }

        return $this->_document;
    }

    /**
     * Set user
     *
     * @param Zym_Service_Scribd_User $user
     * @return Zym_Service_Scribd
     */
    public function setUser(Zym_Service_Scribd_User $user)
    {
        $user->setScribdClient($this);

        $this->_user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Zym_Service_Scribd_User
     */
    public function getUser()
    {
        if (! $this->_user instanceof Zym_Service_Scribd_User) {
            /**
             * @see Zym_Service_Scribd_User
             */
            require_once 'Zym/Service/Scribd/User.php';

            $user = new Zym_Service_Scribd_User();
            $this->setUser($user);
        }

        return $this->_user;
    }

    /**
     * Set API key
     *
     * @param string $key
     * @return Zym_Service_Scribd
     */
    public function setApiKey($key)
    {
        $this->_apiKey = $key;
        return $this;
    }

    /**
     * Set secret key
     *
     * @param string $key
     * @return Zym_Service_Scribd
     */
    public function setSecretKey($key)
    {
        $this->_secretKey = $key;
        return $this;
    }

    /**
     * Get secret key
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->_secretKey;
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Use request signing
     *
     * @param boolean $sign
     * @return boolean
     */
    public function useSignRequest($sign = null)
    {
        if ($sign !== null) {
            $this->_signRequest = (bool) $sign;
        }

        return $this->_signRequest;
    }

    /**
     * Set rest client
     *
     * @param Zend_Rest_Client $client
     * @return Zym_Service_Scribd
     */
    public function setRestClient(Zend_Rest_Client $client)
    {
        $this->_restClient = $client;
        return $this;
    }

    /**
     * Get rest client
     *
     * @return Zend_Rest_Client
     */
    public function getRestClient()
    {
        if (! $this->_restClient instanceof Zend_Rest_Client) {
            /**
             * @see Zend_Rest_Client
             */
            require_once 'Zend/Rest/Client.php';
            $client = new Zend_Rest_Client(self::API_URL);
            $this->setRestClient($client);
        }

        return $this->_restClient;
    }
}