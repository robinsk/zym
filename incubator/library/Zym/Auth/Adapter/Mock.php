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
 * @subpackage Adapter
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
 * Authentication adapter used for testing
 *
 * This adapter is useful for testing adapters like the Chain adapter.
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Auth
 * @subpackage Adapter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Auth_Adapter_Mock implements Zend_Auth_Adapter_Interface
{
    /**
     * Code
     *
     * @var integer
     */
    private $_code;

    /**
     * Identity
     *
     * @var string
     */
    private $_identity;

    /**
     * Messages
     *
     * @var array
     */
    private $_messages = array();

    /**
     * Construct
     *
     * @param integer $code
     * @param string  $identity
     * @param array   $messages
     */
    public function __construct($code = null, $identity = null, array $messages = array())
    {
        if ($code !== null) {
            $this->setCode($code);
        }

        if ($identity !== null) {
            $this->setIdentity($identity);
        }

        if (count($messages)) {
            $this->setMessages($messages);
        }
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
        $code     = $this->getCode();
        $identity = $this->getIdentity();
        $messages = $this->getMessages();

        $result   = new Zend_Auth_Result($code, $identity, $messages);

        return $result;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return Zym_Auth_Adapter_Mock
     */
    public function setCode($code)
    {
        $this->_code = $code;
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
     * @return Zym_Auth_Adapter_Mock
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;

        return $this;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Set Messages
     *
     * @param array $messages
     * @return Zym_Auth_Adapter_Mock
     */
    public function setMessages(array $messages)
    {
        $this->_messages = $messages;

        return $this;
    }
}