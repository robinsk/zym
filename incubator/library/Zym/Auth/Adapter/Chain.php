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
 * Authentication adapter used for chaining other auth adapters
 * to authenticate from multiple sources
 *
 * Adapters are processed in FIFO order.
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Auth
 * @subpackage Adapter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Auth_Adapter_Chain implements Zend_Auth_Adapter_Interface
{
    /**
     * Authentication adapter instances
     *
     * @var array Array of Zend_Auth_Adapters
     */
    private $_adapters = array();

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
        $adapters = $this->getAdapters();

        $results        = array();
        $resultMessages = array();
        foreach ($adapters as $adapter) {
            // Validate adapter
            if (!$adapter instanceof Zend_Auth_Adapter_Interface) {
                /**
                 * @see Zym_Auth_Adapter_Exception
                 */
                require_once 'Zym/Auth/Adapter/Exception.php';
                throw new Zym_Auth_Adapter_Exception(sprintf(
                    'Adapter "%s" is not an instance of Zend_Auth_Adapter_Interface',
                get_class($adapter)));
            }

            $result = $adapter->authenticate();

            // Success
        	if ($result->isValid()) {
        	    return $result;
        	}

        	// Failure
        	$results[]        = $result;
        	$resultMessages[] = $result->getMessages();
        }

        $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, $resultMessages);

        return $result;
    }

    /**
     * Get array of authentication adapters
     *
     * @return array
     */
    public function getAdapters()
    {
        return $this->_adapters;
    }

    /**
     * Add adapter to the stack in FIFO order
     *
     * @param Zend_Auth_Adapter_Interface $adapter
     * @return Zym_Auth_Adapter_Chain
     */
    public function addAdapter(Zend_Auth_Adapter_Interface $adapter)
    {
        $this->_adapter[] = $adapters;
        return $this;
    }

    /**
     * Set array of authentication adapters
     *
     * @param array $adapters
     * @return Zym_Auth_Adapter_Chain
     */
    public function setAdapters(array $adapters)
    {
        $this->_adapters = $adapters;
        return $this;
    }
}