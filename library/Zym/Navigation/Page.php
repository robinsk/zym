<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Navigation
 * @subpackage Zym_Navigation_Page
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Navigation_Container
 */
require_once 'Zym/Navigation/Container.php';

/**
 * Zym_Navigation_Page
 *
 * Base class for Zym_Navigation_Page pages.
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Navigation
 * @subpackage Zym_Navigation_Page
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Navigation_Page extends Zym_Navigation_Container
{
    /**
     * Page label
     *
     * @var string|null
     */
    private $_label;

    /**
     * Page id
     *
     * @var string|null
     */
    private $_id;

    /**
     * Style class for this page (CSS)
     *
     * @var string|null
     */
    private $_class;

    /**
     * A more descriptive title for this page
     *
     * @var string|null
     */
    private $_title;

    /**
     * This page's target
     *
     * @var string|null
     */
    private $_target;

    /**
     * Page order used by parent container
     *
     * @var int|null
     */
    private $_order;

    /**
     * ACL resource associated with this page
     *
     * @var string|Zend_Acl_Resource_Interface|null
     */
    private $_resource;

    /**
     * ACL privilege associated with this page
     *
     * @var string|null
     */
    private $_privilege;

    /**
     * Whether this page should be considered active
     *
     * @var bool
     */
    private $_active = false;

    /**
     * Whether this page should be considered visible
     *
     * @var bool
     */
    private $_visible = true;

    /**
     * Parent container
     *
     * @var Zym_Navigation_Container|null
     */
    private $_parent;

    /**
     * Custom page properties, used by __set(), __get() and __isset()
     *
     * @var array
     */
    private $_properties = array();

    // Initialization:

    /**
     * Factory for Zym_Navigation_Page classes
     *
     * A specific type to construct can be specified by specifying the key
     * 'type' in $options. If type is 'uri' or 'mvc', the type will be resolved
     * to Zym_Navigation_Page_Uri or Zym_Navigation_Page_Mvc. Any other value
     * for 'type' will be considered the full name of the class to construct.
     * A valid custom page class must extend Zym_Navigation_Page.
     *
     * If 'type' is not given, the type of page to construct will be determined
     * by the following rules:
     * - If $options contains either of the keys 'action', 'controller',
     *   'module', or 'route', a Zym_Navigation_Page_Mvc page will be created.
     * - If $options contains the key 'uri', a Zym_Navigation_Page_Uri page
     *   will be created.
     *
     * @param  array|Zend_Config $options  options used for creating page
     * @return Zym_Navigation_Page         a page instance
     * @throws Zym_Navigation_Exception    if $options is not array/Zend_Config
     * @throws Zend_Exception              if 'type' is specified and
     *                                     Zend_Loader is unable to load the
     *                                     class
     * @throws Zym_Navigation_Exception    if something goes wrong during
     *                                     instantiation of the page
     * @throws Zym_Navigation_Exception    if 'type' is given, and the specified
     *                                     type does not extend this class
     * @throws Zym_Navigation_Exception    if unable to determine which class
     *                                     to instantiate
     */
    public static function factory($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                'Invalid argument: $options must be an array or Zend_Config');
        }

        if (isset($options['type'])) {
            $type = $options['type'];
            if (is_string($type) && !empty($type)) {
                switch (strtolower($type)) {
                    case 'mvc':
                        $type = 'Zym_Navigation_Page_Mvc';
                        break;
                    case 'uri':
                        $type = 'Zym_Navigation_Page_Uri';
                        break;
                }

                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($type);

                $page = new $type($options);
                if (!$page instanceof Zym_Navigation_Page) {
                    require_once 'Zym/Navigation/Exception.php';
                    throw new Zym_Navigation_Exception(sprintf(
                            'Invalid argument: Detected type "%s", which ' .
                            'is not an instance of Zym_Navigation_Page',
                            $type));
                }
                return $page;
            }
        }

        $hasUri = isset($options['uri']);
        $hasMvc = isset($options['action']) || isset($options['controller']) ||
                  isset($options['module']) || isset($options['route']);

        if ($hasMvc) {
            require_once 'Zym/Navigation/Page/Mvc.php';
            return new Zym_Navigation_Page_Mvc($options);
        } elseif ($hasUri) {
            require_once 'Zym/Navigation/Page/Uri.php';
            return new Zym_Navigation_Page_Uri($options);
        } else {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                'Invalid argument: Unable to determine class to instantiate');
        }
    }

    /**
     * Final page constructor
     *
     * @param  array|Zend_Config $options  [optional] page options. Default is
     *                                     null, which should set defaults.
     * @throws InvalidArgumentException    if invalid options are given
     */
    public  function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($config);
        }

        // do custom initialization
        $this->_init();
    }

    /**
     * Initializes page (used by subclasses)
     *
     * @return void
     */
    protected function _init()
    {
    }

    /**
     * Sets page properties using a Zend_Config object
     *
     * @param  Zend_Config $config       config object to get properties from
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws InvalidArgumentException  if invalid options are given
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Sets page properties using options from an associative array
     *
     * Each key in the array corresponds to the according set*() method, and
     * each word is separated by underscores, e.g. the option 'target'
     * corresponds to setTarget(), and the option 'reset_params' corresponds to
     * the method setResetParams().
     *
     * @param  array $options            associative array of options to set
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws InvalidArgumentException  if invalid options are given
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    // Accessors:

    /**
     * Sets page label
     *
     * @param  string $label             new page label
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if empty/no string is given
     */
    public function setLabel($label)
    {
        if (null !== $label && !is_string($label)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $label must be a string or null');
        }

        $this->_label = $label;
        return $this;
    }

    /**
     * Returns page label
     *
     * @return string  page label or null
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Sets page id
     *
     * @param  string|null $id           [optional] id to set. Default is null,
     *                                   which sets no id.
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if not given string or null
     */
    public function setId($id = null)
    {
        if (null !== $id && !is_string($id) && !is_numeric($id)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $id must be a string, number or null');
        }

        $this->_id = null === $id ? $id : (string) $id;

        return $this;
    }

    /**
     * Returns page id
     *
     * @return string|null  page id or null
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets page CSS class
     *
     * @param  string|null $class        [optional] CSS class to set. Default
     *                                   is null, which sets no CSS class.
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if not given string or null
     */
    public function setClass($class = null)
    {
        if (null !== $class && !is_string($class)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $class must be a string or null');
        }

        $this->_class = $class;
        return $this;
    }

    /**
     * Returns page class (CSS)
     *
     * @return string|null  page's CSS class or null
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Sets page title
     *
     * @param  string $title             [optional] page title. Default is null,
     *                                   which sets no title.
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if not given string or null
     */
    public function setTitle($title = null)
    {
        if (null !== $title && !is_string($title)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $title must be a non-empty string');
        }

        $this->_title = $title;
        return $this;
    }

    /**
     * Returns page title
     *
     * @return string|null  page title or null
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Sets page target
     *
     * @param  string|null $target       [optional] target to set. Default is
     *                                   null, which sets no target.
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if target is not string or null
     */
    public function setTarget($target = null)
    {
        if (null !== $target && !is_string($target)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $target must be a string or null');
        }

        $this->_target = $target;
        return $this;
    }

    /**
     * Returns page target
     *
     * @return string|null  page target or null
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Sets page order to use in parent container
     *
     * @param  int $order                [optional] page order in container.
     *                                   Default is null, which sets no specific
     *                                   order.
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if order is not integer or null
     */
    public function setOrder($order = null)
    {
        if (is_string($order)) {
            $temp = (int) $order;
            if ($temp < 0 || $temp > 0 || $order == '0') {
                $order = $temp;
            }
        }

        if (null !== $order && !is_int($order)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $order must be an integer or null, ' .
                    'or a string that casts to an integer');
        }

        $this->_order = $order;

        // notify parent, if any
        if (isset($this->_parent)) {
            $this->_parent->notifyOrderUpdated();
        }

        return $this;
    }

    /**
     * Returns page order used in parent container
     *
     * @return int|null  page order or null
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Sets ACL resource assoicated with this page
     *
     * @param  string|Zend_Acl_Resource_Interface $resource  [optional] resource
     *                                                       to associate with
     *                                                       page. Default is
     *                                                       null, which sets no
     *                                                       resource.
     * @throws Zym_Navigation_Exception                      if $resource if
     *                                                       invalid
     * @return Zym_Navigation_Page                           fluent interface,
     *                                                       returns self
     */
    public function setResource($resource = null)
    {
        if (null === $resource || is_string($resource) ||
            $resource instanceof Zend_Acl_Role_Interface) {
            $this->_resource = $resource;
        } else {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $resource must be null, a string, ' .
                    ' or an instance of Zend_Acl_Resource_Interface');
        }

        return $this;
    }

    /**
     * Returns ACL resource assoicated with this page
     *
     * @return string|Zend_Acl_Resource_Interface|null  ACL resource or null
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Sets ACL privilege associated with this page
     *
     * @param  string|null $privilege  [optional] ACL privilege to associate
     *                                 with this page. Default is null, which
     *                                 sets no privilege.
     * @return Zym_Navigation_Page     fluent interface, returns self
     */
    public function setPrivilege($privilege = null)
    {
        $this->_privilege = is_string($privilege) ? $privilege : null;
        return $this;
    }

    /**
     * Returns ACL privilege assigned to this page
     *
     * @return string|null  ACL privilege or null
     */
    public function getPrivilege()
    {
        return $this->_privilege;
    }

    /**
     * Sets whether page should be considered active or not
     *
     * @param  bool $active         [optional] whether page should be considered
     *                              active or not. Default is true.
     * @return Zym_Navigation_Page  fluent interface, returns self
     */
    public function setActive($active = true)
    {
        $this->_active = (bool) $active;
        return $this;
    }

    /**
     * Returns whether page should be considered active or not
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          active if any child pages are active. Default is
     *                          false.
     * @return bool             whether page should be considered active
     */
    public function isActive($recursive = false)
    {
        if ($recursive) {
            if ($this->_active) {
                return true;
            } else {
                foreach ($this as $page) {
                    if ($page->isActive(true)) {
                        return true;
                    }
                }
                return false;
            }
        } else {
            return $this->_active;
        }
    }

    /**
     * Proxy to isActive()
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          active if any child pages are active. Default
     *                          is false.
     * @return bool             whether page should be considered active
     */
    public function getActive($recursive = false)
    {
        return $this->isActive($recursive);
    }

    /**
     * Sets whether the page should be visible or not
     *
     * @param  bool $visible        [optional] whether page should be considered
     *                              visible or not. Default is true.
     * @return Zym_Navigation_Page  fluent interface, returns self
     */
    public function setVisible($visible = true)
    {
        $this->_visible = (bool) $visible;
        return $this;
    }

    /**
     * Returns a boolean value indicating whether the page is visible
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          invisible if parent is invisible. Default is
     *                          false.
     * @return bool             whether page should be considered visible
     */
    public function isVisible($recursive = false)
    {
        if ($recursive && isset($this->_parent) &&
            $this->_parent instanceof Zym_Navigation_Page) {
            if (!$this->_parent->isVisible(true)) {
                return false;
            }
        }

        return $this->_visible;
    }

    /**
     * Proxy to isVisible()
     *
     * Returns a boolean value indicating whether the page is visible
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          invisible if parent is invisible. Default is
     *                          false.
     * @return bool             whether page should be considered visible
     */
    public function getVisible($recursive = false)
    {
        return $this->isVisible($recursive);
    }

    /**
     * Sets parent container
     *
     * @param  Zym_Navigation_Container $parent  [optional] new parent to set.
     *                                           Default is null which will set
     *                                           no parent.
     * @return Zym_Navigation_Page               fluent interface, returns self
     */
    public function setParent(Zym_Navigation_Container $parent = null)
    {
        // return if the given parent already is parent
        if ($parent === $this->_parent) {
            return $this;
        }

        // remove from old parent
        if (null !== $this->_parent) {
            $this->_parent->removePage($this);
        }

        // set new parent
        $this->_parent = $parent;

        // add to parent if page and not already a child
        if (null !== $this->_parent && !$this->_parent->hasPage($this, false)) {
            $this->_parent->addPage($this);
        }

        return $this;
    }

    /**
     * Returns parent container
     *
     * @return Zym_Navigation_Container|null  parent container or null
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Sets the given property
     *
     * If the given property is native (id, class, title, etc), the matching
     * set method will be used. Otherwise, it will be set as a custom property.
     *
     * @param  string $property          property name
     * @param  mixed  $value             value to set
     * @return Zym_Navigation_Page       fluent interface, returns self
     * @throws Zym_Navigation_Exception  if property name is invalid
     */
    public function set($property, $value)
    {
        if (!is_string($property) || empty($property)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $property must be a non-empty string');
        }

        $method = 'set' . self::_normalizePropertyName($property);

        if ($method != 'setOptions' && $method != 'setConfig' &&
            method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->_properties[$property] = $value;
        }

        return $this;
    }

    /**
     * Returns the value of the given property
     *
     * If the given property is native (id, class, title, etc), the matching
     * get method will be used. Otherwise, it will return the matching custom
     * property, or null if not found.
     *
     * @param  string $property          property name
     * @return mixed                     the property's value or null
     * @throws Zym_Navigation_Exception  if property name is invalid
     */
    public function get($property)
    {
        if (!is_string($property) || empty($property)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(
                    'Invalid argument: $property must be a non-empty string');
        }

        $method = 'get' . self::_normalizePropertyName($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (isset($this->_properties[$property])) {
            return $this->_properties[$property];
        }

        return null;
    }

    // Magic overloads:

    /**
     * Sets a custom property
     *
     * Magic overload for enabling <code>$page->propname = $value</code>.
     *
     * @param  string $name              property name
     * @param  mixed  $value             value to set
     * @return void
     * @throws InvalidArgumentException  if property name is invalid
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Returns a property, or null if it doesn't exist
     *
     * Magic overload for enabling <code>$page->propname</code>.
     *
     * @param  string $name              property name
     * @return mixed                     property value or null
     * @throws InvalidArgumentException  if property name is invalid
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Checks if a property is set
     *
     * Magic overload for enabling <code>isset($page->propname)</code>.
     *
     * Returns true if the property is native (id, class, title, etc), and
     * true or false if it's a custom property (depending on whether the
     * property actually is set).
     *
     * @param  string $name  property name
     * @return bool          whether the given property exists
     */
    public function __isset($name)
    {
        $method = 'get' . self::_normalizePropertyName($name);
        if (method_exists($this, $method)) {
            return true;
        }

        return isset($this->_properties[$name]);
    }

    /**
     * Unsets the given custom property
     *
     * Magic overload for enabling <code>unset($page->propname)</code>.
     *
     * @param  string $name              property name
     * @return void
     * @throws InvalidArgumentException  if the property is native
     */
    public function __unset($name)
    {
        $method = 'set' . self::_normalizePropertyName($name);
        if (method_exists($this, $method)) {
            require_once 'Zym/Navigation/Exception.php';
            throw new Zym_Navigation_Exception(sprintf(
                    'Unsetting native property "%s" is not allowed',
                    $name));
        }

        if (isset($this->_properties[$name])) {
            unset($this->_properties[$name]);
        }
    }

    /**
     * Returns page label
     *
     * Magic overload for enabling <code>echo $page</code>.
     *
     * @return string  page label
     */
    public function __toString()
    {
        return $this->_label;
    }

    // Public methods:

    /**
     * Returns custom properties as an array
     *
     * @return array  an array containing custom properties
     */
    public function getCustomProperties()
    {
        return $this->_properties;
    }

    /**
     * Returns a hash code value for the page
     *
     * @return string  a hash code value for this page
     */
    public final function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * Determines whether the page is a descendant of the given container
     *
     * @param  Zym_Navigation_Container $container  container
     * @return bool                                 true or false
     */
    public function isDescendentOf(Zym_Navigation_Container $container)
    {
        $intermediate = $this;
        while ($parent = $intermediate->getParent()) {
            if ($parent === $container) {
                return true;
            }
            if ($parent instanceof Zym_Navigation_Page) {
                $intermediate = $parent;
            } else {
                break;
            }
        }

        return false;
    }

    /**
     * Returns an array representation of the page
     *
     * @return array  associative array containing all page properties
     */
    public function toArray()
    {
        return array_merge(
            $this->getCustomProperties(),
            array(
                'label'     => $this->getlabel(),
                'id'        => $this->getId(),
                'class'     => $this->getClass(),
                'title'     => $this->getTitle(),
                'target'    => $this->getTarget(),
                'order'     => $this->getOrder(),
                'resource'  => $this->getResource(),
                'privilege' => $this->getPrivilege(),
                'active'    => $this->isActive(),
                'visible'   => $this->isVisible(),
                'type'      => get_class($this),
                'pages'     => parent::toArray()
            ));
    }

    // Private methods:

    /**
     * Normalizes a property name
     *
     * @param  string $property  property name to normalize
     * @return string            normalized property name
     */
    private static function _normalizePropertyName($property)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
    }

    // Abstract methods:

    /**
     * Returns href for this page
     *
     * @return string  the page's href
     */
    abstract public function getHref();
}