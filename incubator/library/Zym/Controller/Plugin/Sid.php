<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * Changes a session to a specified session id... This allows changing of the session
 * via url variables such as /index/index/sid/ASD231sd123
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Plugin
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Plugin_Sid extends Zend_Controller_Plugin_Abstract
{
    /**
     * Session id key
     *
     * @var string
     */
    protected $_sidKey;

    /**
     * Construct
     *
     * @param string $sidKey Key to search for sid
     */
    public function __construct($sidKey = 'sid')
    {
        $this->setSidKey($sidKey);
    }

    /**
     * Get the sid key
     *
     * @return string
     */
    public function getSidKey()
    {
        return $this->_sidKey;
    }

    /**
     * Set the sid key
     *
     * @param string $sidKey
     * @return Zym_Controller_Plugin_Sid
     */
    public function setSidKey($sidKey = 'sid')
    {
        $this->_sidKey = (string) $sidKey;
        return $this;
    }

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        // Attempt to get session id
        $sid = $this->getRequest()->getParam($this->getSidKey());

        // Check if an override was provided else stop execution
        if (!$sid) {
            return;
        }

        // Close an existing session
        if (Zend_Session::isStarted()) {
            // Already using this sid
            if (Zend_Session::getId() == $sid) {
                return;
            }

            Zend_Session::destroy();
            Zend_Session::writeClose();
        }

        // Start the session with the requested id
        /*
        We did not use Zend_Session here because it does not check
        whether a session was closed, so it throws an error

        Zend_Session::setId($sid);
        Zend_Session::start();
        */
        session_id($sid);
        session_start();
    }
}
