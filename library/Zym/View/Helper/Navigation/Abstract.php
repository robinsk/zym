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
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Navigation
 */
require_once 'Zym/Navigation.php';

/**
 * @see Zym_View_Helper_Navigation_Interface
 */
require_once 'Zym/View/Helper/Navigation/Interface.php';

/**
 * @see Zend_View_Helper_HtmlElement
 */
require_once 'Zend/View/Helper/HtmlElement.php';

/**
 * Base class for navigation related helpers
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_View_Helper_Navigation_Abstract
    extends Zend_View_Helper_HtmlElement
    implements Zym_View_Helper_Navigation_Interface
{
    /**
     * Container to operate on by default
     *
     * @var Zym_Navigation_Container
     */
    protected $_container;

    /**
     * Indentation string
     *
     * @var string
     */
    protected $_indent = '';

    /**
     * Translator
     *
     * @var Zend_Translate_Adapter
     */
    protected $_translator;

    /**
     * Whether translator should be used for page labels and titles
     *
     * @var bool
     */
    protected $_useTranslator = true;

    /**
     * ACL to use when iterating pages
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * ACL role to use when iterating pages
     *
     * @var string|Zend_Acl_Role_Interface
     */
    protected $_role;

    /**
     * Default ACL to use when iterating pages if not explicitly set in the
     * instance by calling {@link setAcl()}
     *
     * @var Zend_Acl
     */
    protected static $_defaultAcl;

    /**
     * Default ACL role to use when iterating pages if not explicitly set in the
     * instance by calling {@link setRole()}
     *
     * @var string|Zend_Acl_Role_Interface
     */
    protected static $_defaultRole;

    // Accessors:

    /**
     * Sets navigation container the helper operates on by default
     *
     * Implements {@link Zym_View_Helper_Navigation_Interface::setContainer()}.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              operate on. Default is
     *                                              null, meaning container will
     *                                              be reset.
     * @return Zym_View_Helper_Navigation_BasicNavigationHelper  fluent
     *                                                           interface,
     *                                                           returns self
     */
    public function setContainer(Zym_Navigation_Container $container = null)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Returns the navigation container helper operates on by default
     *
     * Implements {@link Zym_View_Helper_Navigation_Interface::getContainer()}.
     *
     * If a helper is not explicitly set in this helper instance by calling
     * {@link setContainer()} or by passing it through the helper entry point,
     * this method will look in {@link Zend_Registry} for a container by using
     * the key 'Zym_Navigation'.
     *
     * If no container is set, and nothing is found in Zend_Registry, a new
     * container will be instantiated and stored in the helper.
     *
     * @return Zym_Navigation_Container  navigation container
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            // try to fetch from registry first
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zym_Navigation')) {
                $nav = Zend_Registry::get('Zym_Navigation');
                if ($nav instanceof Zym_Navigation_Container) {
                    return $this->_container = $nav;
                }
            }

            // nothing found in registry, create new container
            $this->_container = new Zym_Navigation();
        }

        return $this->_container;
    }

    /**
     * Set the indentation string for using in {@link render()}, optionally a
     * number of spaces to indent with
     *
     * @param  string|int $indent  indentation string or number of spaces
     * @return Zym_View_Helper_Navigation_BasicNavigationHelper  fluent
     *                                                           interface,
     *                                                           returns self
     */
    public function setIndent($indent)
    {
        $this->_indent = $this->_getWhitespace($indent);
        return $this;
    }

    /**
     * Returns indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->_indent;
    }

    /**
     * Sets translator to use in helper
     *
     * @param  mixed $translator  [optional] translator. Expects an object of
     *                            type {@link Zend_Translate_Adapter} or
     *                            {@link Zend_Translate}, or null. Default is
     *                            null, which sets no translator.
     * @return Zym_View_Helper_Navigation_BasicNavigationHelper  fluent
     *                                                           interface,
     *                                                           returns self
     */
    public function setTranslator($translator = null)
    {
        if (null == $translator ||
            $translator instanceof Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        }

        return $this;
    }

    /**
     * Returns translator used in helper
     *
     * @return Zend_Translate_Adapter|null  translator or null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $this->setTranslator(Zend_Registry::get('Zend_Translate'));
            }
        }

        return $this->_translator;
    }

    /**
     * Sets whether translator should be used
     *
     * @param  bool $useTranslator  [optional] wheter translator should be used.
     *                              Default is true.
     * @return Zym_View_Helper_Navigation_BasicNavigationHelper  fluent
     *                                                           interface,
     *                                                           returns self
     */
    public function setUseTranslator($useTranslator = true)
    {
        $this->_useTranslator = (bool) $useTranslator;
        return $this;
    }

    /**
     * Returns whether translator should be used
     *
     * @return bool  whether translator should be used
     */
    public function getUseTranslator()
    {
        return $this->_useTranslator;
    }

    /**
     * Sets ACL to use when iterating pages
     *
     * @param  Zend_Acl $acl  [optional] ACL object. Default is null, which
     *                                   means ACL will not be used.
     * @return Zym_View_Helper_Navigation_BasicNavigationHelper  fluent
     *                                                           interface,
     *                                                           returns self
     */
    public function setAcl(Zend_Acl $acl = null)
    {
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or
     * {@link setDefaultAcl()}
     *
     * @return Zend_Acl|null  ACL object or null
     */
    public function getAcl()
    {
        if ($this->_acl === null && self::$_defaultAcl !== null) {
            return self::$_defaultAcl;
        }

        return $this->_acl;
    }

    /**
     * Sets ACL role(s) to use when iterating pages
     *
     * @param  mixed $role          [optional] role to set. Expects a string, an
     *                              instance of type
     *                              {@link Zend_Acl_Role_Interface}, or null.
     *                              Default is null, which will set no role.
     * @throws Zend_View_Exception  if $role is a not string, a
     *                              {@link Zend_Acl_Role_Interface}, or null
     * @return Zym_View_Helper_Navigation_BasicNavigationHelper  fluent
     *                                                           interface,
     *                                                           returns self
     */
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) ||
            $role instanceof Zend_Acl_Role_Interface) {
            $this->_role = $role;
        } else {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(sprintf(
                    '$role must be a string, null, or an instance of ' .
                            'Zend_Acl_Role_Interface; %s given',
                    gettype($role)));
        }

        return $this;
    }

    /**
     * Returns ACL role to use when iterating pages, or null if it isn't set
     * using {@link setRole()} or {@link setDefaultRole()}
     *
     * @return string|Zend_Acl_Role_Interface|null  role or null
     */
    public function getRole()
    {
        if ($this->_role === null && self::$_defaultRole !== null) {
            return self::$_defaultRole;
        }

        return $this->_role;
    }

    // Magic overloads:

    /**
     * Magic overload: Proxy calls to the navigation container
     *
     * @param  string $method            method name in container
     * @param  array  $arguments         [optional] arguments to pass
     * @return mixed                     returns what the container returns
     * @throws Zym_Navigation_Exception  if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(
                array($this->getContainer(), $method),
                $arguments);
    }

    /**
     * Magic overload: Proxy to {@link render()}.
     *
     * This method will trigger an E_USER_ERROR if rendering the helper causes
     * an exception to be thrown.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            trigger_error($msg, E_USER_ERROR);
            return '';
        }
    }

    // Public methods:

    /**
     * Finds the deepest active page in the given container
     *
     * @param  Zym_Navigation_Container $container  container to search
     * @return array                                an associative array with
     *                                              the values 'depth' and
     *                                              'page', or an empty array
     *                                              if not found
     */
    public function findActive(Zym_Navigation_Container $container)
    {
        $found = array();
        $depth = -1;
        $iterator = new RecursiveIteratorIterator($container,
                RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $page) {
            if (!$this->accept($page)) {
                // page is not accepted
                continue;
            }

            if ($page->isActive() && $iterator->getDepth() > $depth) {
                // found an active page at a deeper level than before
                $depth = $iterator->getDepth();
                $found['page'] = $page;
                $found['depth'] = $depth;
            }
        }

        return $found;
    }

    /**
     * Checks if the helper has a container
     *
     * @return bool  whether the helper has a container or not
     */
    public function hasContainer()
    {
        return null !== $this->_container;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page
     *
     * @param  Zym_Navigation_Page $page  page to generate HTML for
     * @return string                     HTML string for the given page
     */
    public function htmlify(Zym_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for anchor element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass(),
            'href'   => $page->getHref(),
            'target' => $page->getTarget()
        );

        return '<a' . $this->_htmlAttribs($attribs) . '>'
             . $this->view->escape($label)
             . '</a>';
    }

    // Iterator filter methods:

    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible, it is not accepted
     * - If helper has no ACL, page is accepted
     * - If helper has ACL, but no role, page is not accepted
     * - If helper has ACL and role:
     *  - Page is accepted if it has no resource or privilege
     *  - Page is accepted if ACL allows page's resource or privilege
     * - If page is accepted by the rules above and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page.
     *
     * @param  Zym_Navigation_Page $page       page to check
     * @param  bool                $recursive  [optional] if true, page will not
     *                                         be accepted if it is the
     *                                         descendant of a page that is not
     *                                         accepted. Default is true.
     * @return bool                            whether page should be accepted
     */
    public function accept(Zym_Navigation_Page $page, $recursive = true)
    {
        // accept by default
        $accept = true;

        if (!$page->isVisible(false)) {
            // don't accept invisible pages
            $accept = false;
        } elseif (!$this->_acceptAcl($page)) {
            // acl is not amused
            $accept = false;
        }

        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof Zym_Navigation_Page) {
                $accept = $this->accept($parent, true);
            }
        }

        return $accept;
    }

    /**
     * Determines whether a page should be accepted by ACL when iterating
     *
     * Rules:
     * - If helper has no ACL, page is accepted
     * - If helper has ACL, but no role, page is not accepted
     * - If helper has ACL and role:
     *  - Page is accepted if it has no resource or privilege
     *  - Page is accepted if ACL allows page's resource or privilege
     *
     * @param  Zym_Navigation_Page $page  page to check
     * @return bool                       whether page should be accepted by ACL
     */
    protected function _acceptAcl(Zym_Navigation_Page $page)
    {
        if (!$acl = $this->getAcl()) {
            // no acl registered means don't use acl
            return true;
        }

        // do not accept by default
        $accept = false;

        // do not accept if helper has no role
        if ($role = $this->getRole()) {
            $resource = $page->getResource();
            $privilege = $page->getPrivilege();

            if ($resource || $privilege) {
                // determine using helper role and page resource/privilege
                $accept = $acl->isAllowed($role, $resource, $privilege);
            } else {
                // accept if page has no resource or privilege
                $accept = true;
            }
        }

        return $accept;
    }

    // Util methods:

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param  int|string $indent
     * @return string
     */
    protected function _getWhitespace($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string) $indent;
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * Overloads {@link Zend_View_Helper_HtmlElement::_htmlAttribs()}.
     *
     * @param  array $attribs  an array where each key-value pair is converted
     *                         to an attribute name and value
     * @return string          an attribute string
     */
    protected function _htmlAttribs($attribs)
    {
        foreach ($attribs as $key => $value) {
            if ($value === null || (is_string($value) && !strlen($value))) {
                unset($attribs[$key]);
            }
        }

        return parent::_htmlAttribs($attribs);
    }

    /**
     * Normalize an ID
     *
     * Overrides {@link Zend_View_Helper_HtmlElement::_normalizeId()}.
     *
     * @param  string $value
     * @return string
     */
    protected function _normalizeId($value)
    {
        $prefix = get_class($this);
        $prefix = strtolower(trim(substr($prefix, strrpos($prefix, '_')), '_'));

        return $prefix . '-' . $value;
    }

    // Static methods:

    /**
     * Sets default ACL to use if another ACL is not explicitly set
     *
     * @param  Zend_Acl $acl  [optional] ACL object. Default is null, which
     *                        sets no ACL object.
     * @return void
     */
    public static function setDefaultAcl(Zend_Acl $acl = null)
    {
        self::$_defaultAcl = $acl;
    }

    /**
     * Sets default ACL role(s) to use when iterating pages if not explicitly
     * set later with {@link setRole()}
     *
     * @param  midex $role               [optional] role to set. Expects null,
     *                                   string, or an instance of
     *                                   {@link Zend_Acl_Role_Interface}.
     *                                   Default is null, which sets no default
     *                                   role.
     * @throws Zend_View_Exception       if role is invalid
     * @return void
     */
    public static function setDefaultRole($role = null)
    {
        if (null === $role ||
            is_string($role) ||
            $role instanceof Zend_Acl_Role_Interface) {
            self::$_defaultRole = $role;
        } else {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(
                    '$role must be null|string|Zend_Acl_Role_Interface');
        }
    }
}