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
 * @subpackage Atlassian
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Soap_Client
 */
require_once 'Zend/Soap/Client.php';

/**
 * Soap API wrapper for Atlassian Crowd
 *
 * Note to the next poor soul that modifies this code. The mapping for java string[]
 * is stdClass([string] => array()).
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Atlassian_Crowd
{
    /**
     * Soap Client
     *
     * @var Zend_Soap_Client
     */
    private $_client;

    /**
     * Application identity
     *
     * @var string
     */
    private $_identity;

    /**
     * Application credential
     *
     * @var string
     */
    private $_credential;

    /**
     * Authentication token
     *
     * @var string
     */
    private $_token;

    /**
     * Construct
     *
     * @param string $wsdl Service wsdl
     */
    public function __construct($wsdl = null, $identity = null, $credential = null)
    {
        if ($wsdl !== null) {
            $client = new SoapClient($wsdl);
            $this->setClient($client);
        }

        if ($identity !== null) {
            $this->setIdentity($identity);
        }

        if ($credential !== null) {
            $this->setCredential($credential);
        }
    }

    /**
     * Get soap client
     *
     * @return Zend_Soap_Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Set soap client
     *
     * @param Zend_Soap_Client $client
     * @return Zym_Service_Atlassian_Crowd
     */
    public function setClient(SoapClient $client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Get identity used for authentication
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Set identity used for authentication
     *
     * @param string $identity
     * @return Zym_Service_Atlassian_Crowd
     */
    public function setIdentity($identity)
    {
        $this->_identity = (string) $identity;

        return $this;
    }

    /**
     * Get credential used for authentication
     *
     * @return string
     */
    public function getCredential()
    {
        return $this->_credential;
    }

    /**
     * Set credential used for authentication
     *
     * @param string $credential
     * @return Zym_Service_Atlassian_Crowd
     */
    public function setCredential($credential)
    {
        $this->_credential = (string) $credential;

        return $this;
    }

    /**
     * Get authentication token
     *
     * @return array
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Set authentication token
     *
     * @param string $name
     * @param string $token
     * @return Zym_Service_Atlassian_Crowd
     */
    public function setToken($name, $token)
    {
        $this->_token = array(
            'name'  => $name,
            'token' => $token
        );

        return $this;
    }

    /**
     * Authenticate service
     *
     * When a service function is called, this function should be called to
     * handle authentication with a service. A long delay (30 mins) depending
     * on the session timeout set in crowd can cause a soapfault. So if developing
     * an application that persists longer than that time, you must call authenticate
     * yourself.
     *
     * @return array
     */
    public function authenticate()
    {
        if (!$tokenArray = $this->getToken()) {
            $credential = $this->getCredential();
            $name       = $this->getIdentity();

            $tokenArray = $this->authenticateApplication($name, $credential);
            $this->setToken($tokenArray['name'], $tokenArray['token']);
        }

        return $tokenArray;
    }

    /**
     * Adds an attribute to a principal who is in the application's assigned directory.
     *
     * Has more of a "set" behavior
     *
     * @param string $principal Principal name
     * @param string $name      Attribute name
     * @param array  $values    Array of attribute values
     * @return void
     */
    public function addAttributeToPrincipal($principal, $name, array $values = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->addAttributeToPrincipal(array(
                'in0' => $token,
                'in1' => $principal,
                'in2' => array('name' => $name, 'values' => $values)
             ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Adds a group to the application's assigned directory.
     *
     * @param Zym_Service_Atlassian_Crowd_Entity_Group $group
     * @return Zym_Service_Atlassian_Crowd_Entity_Group
     */
    public function addGroup(Zym_Service_Atlassian_Crowd_Entity_Group $group)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $groupReturn = $client->addGroup(array(
                'in0' => $token,
                'in1' => $group->toArray()
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $group = new Zym_Service_Atlassian_Crowd_Entity_Group();
        $group->setFromArray((array) $groupReturn->out);
        return $group;
    }

    /**
     * Adds a principal to the application's assigned directory.
     *
     * @param string $principal
     * @param string $credential
     * @return Zym_Service_Atlassian_Crowd_Entity_Principal
     */
    public function addPrincipal(Zym_Service_Atlassian_Crowd_Entity_Principal $principal, $credential)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $principalReturn = $client->addPrincipal(array(
                'in0' => $token,
                'in1' => $principal->toArray(),
                'in2' => array('credential' => $credential)
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $principal = new Zym_Service_Atlassian_Crowd_Entity_Principal();
        $principal->setFromArray((array) $principalReturn->out);
        return $principal;
    }

    /**
     * Adds a principal to a group for the application's assigned directory.
     *
     * @param Zym_Service_Atlassian_Crowd_Entity_Principal $principal
     * @param string $group
     * @return void
     */
    public function addPrincipalToGroup(Zym_Service_Atlassian_Crowd_Entity_Principal $principal, $group)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->addPrincipalToGroup(array(
                'in0' => $token,
                'in1' => $principal->toArray(),
                'in2' => (string) $group
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Adds the principal to a role for the application's assigned directory.
     *
     * @param string $principal
     * @param string $role
     * @return void
     */
    public function addPrincipalToRole($principal, $role)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->addPrincipalToRole(array(
                'in0' => $token,
                'in1' => $principal->toArray(),
                'in2' => (string) $role
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Adds a role to the application's assigned directory.
     *
     * @param Zym_Service_Atlassian_Crowd_Entity_Role $role
     * @return Zym_Service_Atlassian_Crowd_Entity_Role
     */
    public function addRole(Zym_Service_Atlassian_Crowd_Entity_Role $role)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $roleReturn = $client->addRole(array(
                'in0' => $token,
                'in1' => $role->toArray()
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $role = new Zym_Service_Atlassian_Crowd_Entity_Role();
        $role->setFromArray((array) $roleReturn->out);
        return $role;
    }

    /**
     * Authenticate Application
     *
     * @param string $name
     * @param string $credential
     * @param array  $validationFactors
     * @return array token and name
     */
    public function authenticateApplication($name, $credential, array $validationFactors = array())
    {
        $client = $this->getClient();

        try {
            $return = $client->authenticateApplication(array(
                'in0' => array(
                    'credential'        => array('credential' => $credential),
                    'name'              => (string) $name,
                    'validationFactors' => $validationFactors
                )
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (array) $return->out;
    }

    /**
     * Authenticates a principal verses the calling who is in the application's assigned directory.
     *
     * @param string $name
     * @param string $credential
     * @param array  $validationFactors
     *
     * @return string The principal's authenticated token.
     */
    public function authenticatePrincipal($name, $credential, array $validationFactors = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->authenticatePrincipal(array(
                'in0' => $token,
                'in1' => array(
                    'credential'        => array('credential' => $credential),
                    'name'              => (string) $name,
                    'validationFactors' => $validationFactors
                )
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (string) $return->out;
    }

    /**
     * Authentiates a principal without SSO details utilizing centralized authentication only.
     *
     * @param string $username
     * @param string $password
     *
     * @return string The principal's authenticated token.
     */
    public function authenticatePrincipalSimple($username, $password)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->authenticatePrincipalSimple(array(
                'in0' => $token,
                'in1' => (string) $username,
                'in2' => (string) $password
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (string) $return->out;
    }

    /**
     * Authenticates a principal without validating a password.
     *
     * @param string $username
     * @param array  $validationFactors
     *
     * @return string The principal's authenticated token.
     */
    public function createPrincipalToken($username, array $validationFactors = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->createPrincipalToken(array(
                'in0' => $token,
                'in1' => $username
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (string) $return->out;
    }

    /**
     * Finds all of the groups who are visible in the application's assigned directory.
     *
     * @return array
     */
    public function findAllGroupNames()
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->findAllGroupNames(array(
                'in0' => $token
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        if (isset($return->out->string)) {
            return array_values((array) $return->out->string);
        } else {
            return array_values((array) $return->out);
        }
    }

    /**
     * Finds all of the groups who are visible in the application's assigned directory.
     *
     * @return array Array of Zym_Service_Atlassian_Crowd_Entity_NestableGroup
     */
    public function findAllGroupRelationships()
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->findAllGroupRelationships(array(
                'in0' => $token
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $returnArray = array();
        $return = array_values((array) $return->out);
        foreach (array_pop($return) as $group) {
        	$relation = new Zym_Service_Atlassian_Crowd_Entity_NestableGroup();
        	$relation->setFromArray((array) $group);

        	$returnArray[] = $relation;
        }

        return $returnArray;
    }

    /**
     * Finds all of the principals who are visable in the application's assigned directory.
     *
     * @return array
     */
    public function findAllPrincipalNames()
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->findAllPrincipalNames(array(
                'in0' => $token
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        if (isset($return->out->string)) {
            return array_values((array) $return->out->string);
        } else {
            return array_values((array) $return->out);
        }
    }

    /**
     * Finds all of the roles who are visable in the application's assigned directory.
     *
     * @return array
     */
    public function findAllRoleNames()
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->findAllRoleNames(array(
                'in0' => $token
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        if (isset($return->out->string)) {
            return array_values((array) $return->out->string);
        } else {
            return array_values((array) $return->out);
        }
    }

    /**
     * Find a group by name for the application's assigned directory.
     *
     * @param string $name
     * @return Zym_Service_Atlassian_Crowd_Entity_Group
     */
    public function findGroupByName($name)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $groupReturn = $client->findGroupByName(array(
                'in0' => $token,
                'in1' => (string) $name
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $group = new Zym_Service_Atlassian_Crowd_Entity_Group();
        $group->setFromArray((array) $groupReturn->out);
        return $group;
    }

    /**
     * Finds all of the principals who are members of a group that is in the application's assigned directory.
     *
     * @param string $principalName
     * @return array
     */
    public function findGroupMemberships($principalName)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->findGroupMemberships(array(
                'in0' => $token,
                'in1' => (string) $principalName
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        if (isset($return->out->string)) {
            return array_values((array) $return->out->string);
        } else {
            return array_values((array) $return->out);
        }
    }

    /**
     * Finds a principal by name who is in the application's assigned directory.
     *
     * @param string $name
     * @return Zym_Service_Atlassian_Crowd_Entity_Principal
     */
    public function findPrincipalByName($name)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $principalReturn = $client->findPrincipalByName(array(
                'in0' => $token,
                'in1' => (string) $name
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $principal = new Zym_Service_Atlassian_Crowd_Entity_Principal();
        $principal->setFromArray((array) $principalReturn->out);
        return $principal;
    }

    /**
     * Finds a principal by token.
     *
     * @param string $key
     * @return Zym_Service_Atlassian_Crowd_Entity_Principal
     */
    public function findPrincipalByToken($key)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $principalReturn = $client->findPrincipalByToken(array(
                'in0' => $token,
                'in1' => (string) $key
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $principal = new Zym_Service_Atlassian_Crowd_Entity_Principal();
        $principal->setFromArray((array) $principalReturn->out);
        return $principal;
    }

    /**
     * Finds a role by name for the application's assigned directory.
     *
     * @param string $name
     * @return Zym_Service_Atlassian_Crowd_Entity_Role
     */
    public function findRoleByName($name)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $roleReturn = $client->findRoleByName(array(
                'in0' => $token,
                'in1' => (string) $name
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $role = new Zym_Service_Atlassian_Crowd_Entity_Role();
        $role->setFromArray((array) $roleReturn->out);
        return $role;
    }

    /**
     * Finds all of the principals who are members of a role that is in the application's assigned directory.
     *
     * @param string $principalName
     * @return array
     */
    public function findRoleMemberships($principalName)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->findRoleMemberships(array(
                'in0' => $token,
                'in1' => (string) $principalName
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        if (isset($return->out->string)) {
            return array_values((array) $return->out->string);
        } else {
            return array_values((array) $return->out);
        }
    }

    /**
     * This will return the domain configured in Crowd or null if no domain has been set.
     *
     * @return string|null
     */
    public function getDomain()
    {
        $this->authenticate();

        $client = $this->getClient();
        try {
            $token  = $this->getToken();
            $domain = $client->getDomain(array('in0' => $token));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return $domain->out;
    }

    /**
     * Will return the List of group names that have been given access to connect to the application
     *
     * @return array
     */
    public function getGrantedAuthorities()
    {
        $this->authenticate();

        $client = $this->getClient();
        try {
            $token  = $this->getToken();
            $domain = $client->getGrantedAuthorities(array('in0' => $token));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return array_values((array) $domain->out);
    }

    /**
     * Invalidates a token for all integrated applications.
     *
     * @param string $principalToken
     * @return void
     */
    public function invalidatePrincipalToken($principalToken)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->invalidatePrincipalToken(array(
                'in0' => $token,
                'in1' => (string) $principalToken
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     *  Checks if the client application should cache security information from the Crowd server.
     *
     * @return boolean
     */
    public function isCacheEnabled()
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->isCacheEnabled(array(
                'in0' => $token
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (bool) $return->out;
    }

    /**
     * Checks if a prinicipal is a member of a group for the application's assigned directory.
     *
     * @param string $group
     * @param string $principal
     *
     * @return boolean
     */
    public function isGroupMember($group, $principal)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->isGroupMember(array(
                'in0' => $token,
                'in1' => (string) $group,
                'in2' => (string) $principal
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (bool) $return->out;
    }

    /**
     * Checks if a prinicipal is a member of a role for the application's assigned directory.
     *
     * @param string $role
     * @param string $principal
     *
     * @return boolean
     */
    public function isRoleMember($role, $principal)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->isRoleMember(array(
                'in0' => $token,
                'in1' => (string) $role,
                'in2' => (string) $principal
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (bool) $return->out;
    }

    /**
     * Checks if the principal's current token is still valid.
     *
     * @param string $principalToken
     * @param array  $validationFactors
     * @return boolean
     */
    public function isValidPrincipalToken($principalToken, array $validationFactors = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $return = $client->isValidPrincipalToken(array(
                'in0' => $token,
                'in1' => (string) $principalToken,
                'in2' => $validationFactors
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        return (bool) $return->out;
    }

    /**
     * Removes an attribute from a principal who is in the application's assigned directory.
     *
     * @param string $principal
     * @param string $attributeName
     * @return void
     */
    public function removeAttributeFromPrincipal($principal, $attributeName)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->removeAttributeFromPrincipal(array(
                'in0' => $token,
                'in1' => (string) $principal,
                'in2' => (string) $attributeName
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes a group from the applciation's assigned directory.
     *
     * @param string $group
     * @return void
     */
    public function removeGroup($group)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->removeGroup(array(
                'in0' => $token,
                'in1' => (string) $group
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes a principal from the applciation's assigned directory.
     *
     * @param string $principal
     * @return void
     */
    public function removePrincipal($principal)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->removePrincipal(array(
                'in0' => $token,
                'in1' => (string) $principal
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes a principal from a group for the application's assigned directory.
     *
     * @param string $principal
     * @param string $group
     * @return void
     */
    public function removePrincipalFromGroup($principal, $group)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->removePrincipalFromGroup(array(
                'in0' => $token,
                'in1' => (string) $principal,
                'in2' => (string) $group
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes a principal from a role for the application's assigned directory.
     *
     * @param string $principal
     * @param string $role
     * @return void
     */
    public function removePrincipalFromRole($principal, $role)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->removePrincipalFromRole(array(
                'in0' => $token,
                'in1' => (string) $principal,
                'in2' => (string) $role
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes a role from the applciation's assigned directory.
     *
     * @param string $role
     * @return void
     */
    public function removeRole($role)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->removeRole(array(
                'in0' => $token,
                'in1' => (string) $role
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Resets a principal's password credential to a random password and emails the new password who is in the application's assigned directory.
     *
     * @param string $principal
     * @return void
     */
    public function resetPrincipalCredential($principal)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->resetPrincipalCredential(array(
                'in0' => $token,
                'in1' => (string) $principal
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Searches for groups that are in the application's assigned directory.
     *
     * @param array $searchRestrictions
     * @return array Array of Zym_Service_Atlassian_Crowd_Entity_Group
     */
    public function searchGroups(array $searchRestrictions = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $groupReturn = $client->searchGroups(array(
                'in0' => $token,
                'in1' => $searchRestrictions
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $groupArray = array();
        foreach ($groupReturn->out->SOAPGroup as $item) {
            $group = new Zym_Service_Atlassian_Crowd_Entity_Group();
            $group->setFromArray((array) $item);
            $groupArray[] = $group;
        }

        return $groupArray;
    }

    /**
     * Searches for principal that are in the application's assigned directory.
     *
     * @param array $searchRestrictions
     * @return array Array of Zym_Service_Atlassian_Crowd_Entity_Principal
     */
    public function searchPrincipals(array $searchRestrictions = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $principalReturn = $client->searchPrincipals(array(
                'in0' => $token,
                'in1' => $searchRestrictions
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $principalArray = array();
        foreach ($principalReturn->out->SOAPGroup as $item) {
            $principal = new Zym_Service_Atlassian_Crowd_Entity_Principal();
            $principal->setFromArray((array) $item);
            $principalArray[] = $principal;
        }

        return $principalArray;
    }


    /**
     * Searches for role that are in the application's assigned directory.
     *
     * @param array $searchRestrictions
     * @return array Array of Zym_Service_Atlassian_Crowd_Entity_Role
     */
    public function searchRoles(array $searchRestrictions = array())
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $roleReturn = $client->searchRoles(array(
                'in0' => $token,
                'in1' => $searchRestrictions
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }

        $roleArray = array();
        foreach ($roleReturn->out->SOAPGroup as $item) {
            $role = new Zym_Service_Atlassian_Crowd_Entity_Role();
            $role->setFromArray((array) $item);
            $roleArray[] = $role;
        }

        return $roleArray;
    }

    /**
     * Updates the first group located from the list of directories assigned to
     * an application Available fields that can be updated are description and active
     *
     * @param string  $group
     * @param string  $description
     * @param boolean $active
     * @return void
     */
    public function updateGroup($group, $description = null, $active = null)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->updateGroup(array(
                'in0' => $token,
                'in1' => (string) $group,
                'in2' => $description,
                'in3' => $active
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Updates an attribute for a principal who is in the application's assigned directory..
     *
     * @param string $group
     * @param string $attrName
     * @param array  $attrValues
     * @return void
     */
    public function updatePrincipalAttribute($principal, $attrName, array $attrValues)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->updatePrincipalAttribute(array(
                'in0' => $token,
                'in1' => (string) $principal,
                'in2' => (string) $attrName,
                'in3' => $attrValues
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Updates the password credential for a principal who is in the application's assigned directory.
     *
     * @param string $group
     * @param string $credential
     * @return void
     */
    public function updatePrincipalCredential($principal, $credential)
    {
        $token  = $this->authenticate();
        $client = $this->getClient();

        try {
            $client->updatePrincipalCredential(array(
                'in0' => $token,
                'in1' => (string) $principal,
                'in2' => (string) $credential
            ));
        } catch (SoapFault $e) {
            /**
             * @see Zym_Service_Atlassian_Crowd_Exception
             */
            require_once 'Zym/Service/Atlassian/Crowd/Exception.php';
            throw new Zym_Service_Atlassian_Crowd_Exception($e->getMessage(), $e->getCode());
        }
    }
}