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
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_View_Helper_Html_Abstract
 */
require_once 'Zym/View/Helper/Html/Abstract.php';

/**
 * @see Zym_Navigation
 */
require_once 'Zym/Navigation.php';

/**
 * @see Zend_Acl
 */
require_once 'Zend/Acl.php';

/**
 * Base class for navigation related helpers
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */ 
abstract class Zym_View_Helper_NavigationAbstract extends Zym_View_Helper_Html_Abstract
{
    /**
     * Container to operate on
     * 
     * @var Zym_Navigation_Container
     */
    protected $_container;
    
    /**
     * ACL role to use when iterating pages
     * 
     * @var string|array|null
     */
    protected $_role;
    
    /**
     * ACL to use when iterating pages
     * 
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * Whether translator should be used
     * 
     * @var boolean
     */
    protected $_useTranslator = true;
    
    /**
     * Translator
     * 
     * @var Zend_Translate_Adapter
     */
    protected $_translator;
    
    /**
     * Proxy to the navigation container
     *
     * @param  string $method     method in the container to call
     * @param  array  $arguments  [optional] arguments to pass
     * @throws BadMethodCallException  if method does not exist in container
     */
    public function __call($method, $arguments = null)
    {
        $this->getNavigation();
        if (method_exists($this->_container, $method)) {
            return call_user_func(array($this->_container, $method), $arguments);
        } else {
            $msg = "Method '$method' does not exst in container";
            throw new BadMethodCallException($msg);
        }
    }
    
    /**
     * Sets navigation container to operate on
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              operate on, default is
     *                                              null, meaning it will be
     *                                              reset
     * @return Zym_View_Helper_NavigationAbstract
     */
    public function setNavigation(Zym_Navigation_Container $container = null)
    {
        $this->_container = $container;
        return $this;
    }
    
    /**
     * Returns navigation container
     *
     * @return Zym_Navigation_Container
     */
    public function getNavigation()
    {
        if (null === $this->_container) {
            $this->_retrieveDefaultNavigation();
        }
        
        return $this->_container;
    }
    
    /**
     * Retrieves default navigation container
     *
     * @return void
     */
    protected function _retrieveDefaultNavigation()
    {
        // try to fetch from registry first
        require_once 'Zend/Registry.php';
        if (Zend_Registry::isRegistered('Zym_Navigation')) {
            $nav = Zend_Registry::get('Zym_Navigation');
            if ($nav instanceof Zym_Navigation_Container) {
                $this->_container = $nav;
                return;
            }
        }
        
        // nothing found, create new container
        $this->_container = new Zym_Navigation();
    }
    
    /**
     * Returns HTML anchor for the given pages
     *
     * @param  Zym_Navigation_Page $page  page to get anchor for
     * @return string
     */
    public function getPageAnchor(Zym_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();
        
        if ($this->_useTranslator && $t = $this->_getTranslator()) {
            $label = $t->translate($label);
            $title = $t->translate($title);
        }
        
        // get attribs for anchor element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass(),
            'href'   => $page->getHref(),
            'target' => $page->getTarget()
        );
        
        return '<a ' . $this->_htmlAttribs($attribs) . '>'
             . $label
             . '</a>';
    }
    
    /**
     * Sets boolean flag indicating whether translator should be used
     * 
     * @param bool $useTranslator  [optional] defaults to true
     * @return Zym_View_Helper_NavigationAbstract
     */
    public function setUseTranslator($useTranslator = true)
    {
        $this->_useTranslator = (bool) $useTranslator;
    }
    
    /**
     * Sets translator object to use
     * 
     * @param Zend_Translate|Zend_Translate_Adapter|null $translator
     */
    public function setTranslator($translator)
    {
        if (null === $translator || $translator instanceof Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        }
    }
    
    /**
     * Returns translator or null
     * 
     * @return Zend_Translate_Adapter|null
     */
    protected function _getTranslator()
    {
        if (null === $this->_translator) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $t = Zend_Registry::get('Zend_Translate');
                if ($t instanceof Zend_Translate) {
                    return $t->getAdapter();
                } elseif ($t instanceof Zend_Translate_Adapter) {
                    return $t;
                }
            }
        }
        
        return $this->_translator;
    }
    
    /**
     * Sets ACL to use when iterating pages
     * 
     * @param  Zend_Acl $acl  [optional] ACL object, defaults to null which
     *                        sets no ACL object
     * @return Zym_View_Helper_Navigation
     */
    public function setAcl(Zend_Acl $acl = null)
    {
        $this->_acl = $acl;
    }
    
    /**
     * Returns ACL or null if it isn't set
     *
     * @return Zend_Acl|null
     */
    public function getAcl()
    {
        return $this->_acl;
    }
    
    /**
     * Sets ACL role(s) to use when iterating pages
     * 
     * @param  null|string|array $role   a single role, or an array of roles,
     *                                   defaults to null, which sets no role
     * @throws InvalidArgumentException  if $role is not null|string|array
     * @return Zym_Navigation_Page
     */
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) || is_array($role)) {
            $this->_role = $role;
        } else {
            $msg = '$role must be null|string|array';
            throw new InvalidArgumentException($msg);
        }
        
        return $this;
    }
    
    /**
     * Returns ACL role(s) to use when iterating pages
     * 
     * @return array|null  returns null if no role is set 
     */
    public function getRole()
    {
        if (null === $this->_role || is_array($this->_role)) {
            return $this->_role;
        }
        
        return (array) $this->_role;
    }
    
    /**
     * Determines whether a page should be accepted when iterating using ACL
     * 
     * Validates that the role set in helper inherits or is the same as
     * the role(s) found in the page
     * 
     * @return bool
     */
    protected function _acceptAcl(Zym_Navigation_Page $page, $recursive = true)
    {
        // do not accept by default
        $accept = false;
        
        if (!$helperRole = $this->getRole()) {
            // don't accept if helper has no role
            return false;
        }
        
        if (!$pageRole = $page->getRole()) {
            // accept it if page has no role
            $accept = true;
        } else {   
            // loop all roles
            foreach ($helperRole as $hRole) {
                foreach ($pageRole as $pRole) {
                    if ($hRole == $pRole ||
                        $this->_acl->inheritsRole($hRole, $pRole)) {
                        $accept = true;
                        break;
                    }
                }
                    
                if ($accept) {
                    break;
                }
            }
        }
        
        // loop parent(s) recursively if page is accepted and recurisve is true
        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof Zym_Navigation_Page) {
                $accept = $this->_acceptAcl($parent, true);
            }
        }
        
        return $accept;
    }
    
    /**
     * Determines whether a page should be accepted when iterating
     *
     * @param Zym_Navigation_Page $page  page to verify
     */
    protected function _accept(Zym_Navigation_Page $page, $recursive = true)
    {
        if (!$page->isVisible($recursive)) {
            // don't accept invisible pages
            return false;
        }
        
        if (null !== $this->_acl) {
            // determine using ACL
            return $this->_acceptAcl($page, $recursive);
        }
        
        // accept by default
        return true;
    }
    
    /**
     * Renders helper
     * 
     * @param string|int $indent  [optional]
     * @return string
     */
    abstract public function toString($indent = null);
    
    /**
     * Magic method, proxy to toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
