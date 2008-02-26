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
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * @see Zend_Session_Namespace
 */
require_once 'Zend/Session/Namespace.php';

/**
 * Works similar to FlashMessenger in that this helper allows the setting of
 * a url with a specified duration before expiration. The url can be retrieved
 * on the next request. Useful for redirecting to previous page after login.
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_FlashRedirector extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Redirect urls from the previous request
     *
     * @var array
     */
    static protected $_redirect = array();

    /**
     * $_namespace - Instance namespace, default is 'default'
     *
     * @var string
     */
    protected $_namespace = 'default';

    /**
     * Prevents previously set expiration hop from being overrided
     *
     * @var array
     */
    protected $_expirationHops = array();

    /**
     * Zend_Session storage object
     *
     * @var Zend_Session_Namespace
     */
    static protected $_session = null;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        if (!self::$_session instanceof Zend_Session_Namespace) {
            self::$_session = new Zend_Session_Namespace($this->getName());

            // Get previous redirects
            foreach (self::$_session as $namespace => $redirect) {
                self::$_redirect[$namespace] = $redirect;
            }
        }
    }

    /**
     * postDispatch() - runs after action is dispatched, in this
     * case, it is resetting the namespace in case we have forwarded to a different
     * action, Flashmessage will be 'clean' (default namespace)
     *
     * @return Zym_Controller_Action_Helper_FlashRedirector
     */
    public function postDispatch()
    {
        $this->resetNamespace();
        return $this;
    }

    /**
     * setNamespace() - change the namespace messages are added to, useful for
     * per action controller messaging between requests
     *
     * @param string $namespace
     * @return Zym_Controller_Action_Helper_FlashRedirector
     */
    public function setNamespace($namespace = 'default')
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * Get the current namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * resetNamespace() - reset the namespace to the default
     *
     * @return Zym_Controller_Action_Helper_FlashRedirector
     */
    public function resetNamespace()
    {
        $this->setNamespace();
        return $this;
    }

    /**
     * Set a url to redirect
     *
     * @param string $url
     */
    public function setRedirect($url)
    {
        self::$_session->{$this->getNamespace()} = (string) $url;

        if (isset($this->_expirationHops[$this->getNamespace()])) {
            $this->setExpirationHops($this->_expirationHops[$this->getNamespace()]);
        } else {
            $this->setExpirationHops();
        }
    }

    /**
     * Whether a url redirect has been set
     *
     * This function checks the redirect for the previous request and not
     * this one, use hasCurrentRedirect()
     *
     * @return bool
     */
    public function hasRedirect()
    {
        return (!empty(self::$_redirect[$this->getNamespace()]));
    }

    /**
     * Get url to redirect
     *
     * This function checks the redirect for the previous request and not
     * this one, use getCurrentRedirect()
     *
     * @return string
     */
    public function getRedirect()
    {
        if ($this->hasRedirect()) {
            return self::$_redirect[$this->getNamespace()];
        }
    }

    /**
     * Whether a url redirect has been set this request
     *
     * @return bool
     */
    public function hasCurrentRedirect()
    {
        return (!empty(self::$_session->{$this->getNamespace()}));
    }

    /**
     * Get url to redirect that was set this request
     *
     * @return string
     */
    public function getCurrentRedirect()
    {
        if ($this->hasRedirect()) {
            return self::$_session->{$this->getNamespace()};
        }
    }

    /**
     * Clear url redirect
     *
     * @return bool True if url were cleared, false if none existed
     */
    public function clearRedirect()
    {
        if ($this->hasRedirect()) {
            unset(self::$_session->{$this->getNamespace()});
            return true;
        }

        return false;
    }

    /**
     * Extend the url redirect
     *
     * Same as setExpirationHops
     *
     * @param integer $hops
     */
    public function extendRedirect($hops = 1)
    {
        $this->setExpirationHops($hops);
    }

    /**
     * Set redirect expiration hops
     *
     * @param integer $hops
     */
    public function setExpirationHops($hops = 1)
    {
        // Store setting
        $this->_expirationHops[$this->getNamespace()] = $hops;

        // Set hops
        self::$_session->setExpirationHops((int) $hops, $this->getNamespace());
    }
}
