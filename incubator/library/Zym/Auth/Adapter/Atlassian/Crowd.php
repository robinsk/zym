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
 * @package    Zym_Auth
 * @subpackage Adapter_Atlassian
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';

/**
 * @see Zym_Service_Atlassian_Crowd
 */
require_once 'Zym/Service/Atlassian/Crowd.php';

/**
 * Authentication adapter for Atlassian Crowd
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Auth
 * @subpackage Adapter_Atlassian
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Auth_Adapter_Atlassian_Crowd implements Zend_Auth_Adapter_Interface
{
    /**
     * Crowd client
     *
     * @var Zym_Service_Atlassian_Crowd
     */
    private $_client;

    /**
     * Identity (Username
     *
     * @var string
     */
    private $_identity;

    /**
     * Credential (Password)
     *
     * @var string
     */
    private $_credential;

    /**
     * User authentication token
     *
     * @var string
     */
    private $_token;

    /**
     * Construct
     *
     * @param string $wsdl
     * @param string $appName
     * @param string $appPassword
     */
    public function __construct($wsdl, $appName, $appPassword)
    {
        $client = new Zym_Service_Atlassian_Crowd($wsdl, $appName, $appPassword);
        $this->setClient($client);
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authenication.  Previous to this call, this adapter would have already
     * been configured with all nessissary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $username = (string) $this->getIdentity();
        $password = (string) $this->getCredential();
        $token    = $this->getToken();

        try {
            // Try app authentication
            $client = $this->getClient();
            $client->authenticate();
        } catch (Zym_Service_Atlassian_Crowd_Exception $e) {
            /**
             * @see Zym_Auth_Adapter_Exception
             */
            require_once 'Zym/Auth/Adapter/Exception.php';
            throw new Zym_Auth_Adapter_Exception('Connection to Crowd server failed: ' . $e->getMessage());
        }

        try {
            // Validate SSO
            if ($token !== null) {
                $isValidPrincipal = $client->isValidPrincipalToken($token);

                if ($isValidPrincipal === true) {
                    $username = $client->findPrincipalByToken($token)->getName();
                } else {
                    /**
                     * @see Zym_Service_Atlasian_Crowd_Exception
                     */
                    require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
                    throw new Zym_Service_Atlassian_Crowd_Exception(
                        'Principal token is not valid'
                    );
                }
            } else { // Validate username/pw
                $principal = $client->authenticatePrincipalSimple($username, $password);
            }

            // Authentication success
            $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $principal);
        } catch (Zym_Service_Atlassian_Crowd_Exception $e) {
            // Authentication failure
            $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $username, array($e->getMessage()));
            return $result;
        }

        return $result;
    }

    /**
     * Get soap client
     *
     * @return Zym_Service_Atlassian_Crowd
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Set crowd client
     *
     * @param Zym_Service_Atlassian_Crowd$client
     * @return Zym_Auth_Adapter_Atlassian_Crowd
     */
    public function setClient(Zym_Service_Atlassian_Crowd $client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Get identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Set identity
     *
     * @param string $identity
     * @return Zym_Auth_Adapter_Atlassian_Crowd
     */
    public function setIdentity($identity)
    {
        $this->_identity = (string) $identity;

        return $this;
    }

    /**
     * Get credential
     *
     * @return string
     */
    public function getCredential()
    {
        return $this->_credential;
    }

    /**
     * Set credential
     *
     * @param string $credential
     * @return Zym_Auth_Adapter_Atlassian_Crowd
     */
    public function setCredential($credential)
    {
        $this->_credential = (string) $credential;

        return $this;
    }

    /**
     * Get user authentication token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Set user authentication token
     *
     * Useful for checking authententication of an already logged in
     * user from another application
     *
     * @param string $token
     * @return Zym_Auth_Adapter_Atlassian_Crowd
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }
}