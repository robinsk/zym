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
 * @package    Zym_Sitemap
 * @subpackage Site
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Sitemap
 */
require_once 'Zym/Sitemap.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * Zym_Sitemap_Site
 * 
 * This class represents a site in a Zym_Sitemap.
 * 
 * @property string $id   site id
 * @property string $name  site name
 * @property string $title  site title
 * @property int|null $order  in sitemap
 * @property bool $main  whether site is a main site
 * @property bool $hidden  whether side should be hidden
 * @property string $uri  URI of site (off-site)
 * @property string $module
 * @property string $controller
 * @property string $action
 * @property string $route  route name to use when assembling URI
 * @property bool $resetParam  whether params should be reset when assembling URI
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Sitemap
 * @subpackage Site
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Sitemap_Site
{
    /**
     * Used if no module name is given
       * 
     * @see Zym_Sitemap_Site::resetDefaultModule()
     * @var string  default module name
     */
    protected static $_defaultModule = 'default';
    
    /**
     * Used for assembling URIs
     * 
     * @see Zym_Sitemap_Site::getHref()
     * @var Zend_Controller_Action_Helper_Url
     */
    protected static $_urlHelper;
    
    /**
     * Used for determining if site is active
     * 
     * @see Zym_Sitemap_Site::isActive()
     * @var Zend_Controller_Request_Http
     */
    protected static $_request;
    
    /**
     * Contains site data
     * 
     * @see Zym_Sitemap_Site::__get()
     * @see Zym_Sitemap_Site::__set()
     * @see Zym_Sitemap_Site::__isset()
     * @var array
     */
    protected $_data = array(
        'id'         => null, // string, required
        'name'       => null, // string, required
        'title'      => null, // string
        'order'      => null, // int
        'main'       => false, // bool
        'hidden'     => false, // bool
    
        // either uri or controller and action must be given
        'uri'        => null, // string, required if not 'controller'
                              // and 'action' is given
                              
        'module'     => null, // string
        'controller' => null, // string, required if 'uri' is not given
        'action'     => null, // string, required if 'uri' is not given
        
        // route options to use when assembling url in getHref()
        'route'       => 'default', // string|null
        'resetParams' => true       // bool|null
    );
    
    /**
     * contains sub sites of this site
     * 
     * @var Zym_Sitemap
     */
    protected $_subSites = null;
    
    /**
     * Cntains key => value pairs for custom data
     * 
     * @var array 
     */
    protected $_customData = array();
    
    /**
     * Creates a site representation
     * 
     * @param array|Zend_Config $options
     * @return void
     * @throws Zym_Sitemap_Site_Exception if id, uri or name is not set
     */
    public function __construct($options)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
        
        if ($this->_data['uri']) {
            // uri is given, blank out mvc fields
            $this->_data['module'] = null;
            $this->_data['controller'] = null;
            $this->_data['action'] = null;
        } else {
            // uri is not given, this is an internal site
            $this->_data['uri'] = null;
            if (!$this->_data['module']) {
                $this->_data['module'] = self::$_defaultModule;
            }
        }
        
        $error = false;
        if (null === $this->_data['id']) {
            $error = "Required value 'id' is not given.";
        } elseif (null === $this->_data['name']) {
            $error = "Required value 'name' is not given.";
        } elseif (null === $this->_data['uri'] &&
                  (null === $this->_data['controller'] ||
                  null === $this->_data['action'])) {
            $error = "'uri' or 'controller' and 'action' values are required.";
        }
        
        if ($error) {
            require_once 'Zym/Sitemap/Site/Exception.php';
            throw new Zym_Sitemap_Site_Exception($error);
        }
    }

    /**
     * Sets site options from array
     * 
     * @param  array $options  
     * @return Zym_Sitemap_Site
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if ($key == 'sub' && is_array($value)) {
                $this->_subSites = new Zym_Sitemap($value);
            } else {
                $this->__set($key, $value);
            }
        }
        
        return $this;
    }

    /**
     * Sets site options from config object
     * 
     * @param  Zend_Config $config 
     * @return Zym_Sitemap_Site
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }
    
    /**
     * Checks if site is active for the current request
     * 
     * @param bool $checkSubs  [optional] whether site should be considered
     *                         active if a subsite is active, default is false
     * @return bool
     */
    public function isActive($checkSubs = false)
    {
        if (isset($this->uri)) {
            // when uri is set, it is considered to be off-site
            return false;
        }
        
        if (null == self::$_request) {
            self::$_request = Zend_Controller_Front::getInstance()->getRequest();
        }
        
        $reqModule = self::$_request->getModuleName();
        $reqController = self::$_request->getControllerName();
        $reqAction = self::$_request->getActionName();
        
        if ($this->module == $reqModule &&
            $this->controller == $reqController &&
            $this->action == $reqAction) {
                return true;
        }
        
        if ($checkSubs && $this->hasSubSites()) {
            foreach ($this->_subSites as $id => $subSite) {
                if ($subSite->isActive(true)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Retrieves URI/href for the given site
     * 
     * @return string
     */
    public function getHref()
    {
        if (isset($this->uri)) {
            return $this->uri;
        }
        
        if (null === self::$_urlHelper) {
            self::$_urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
        }
        
        return self::$_urlHelper->url(array(
            'module' => $this->module,
            'controller' => $this->controller,
            'action' => $this->action
        ), $this->route, $this->resetParams);
    }
    
    /**
     * Checks if this site has sub sites
     * 
     * @return bool
     */
    public function hasSubSites()
    {
        return $this->_subSites instanceof Zym_Sitemap &&
               count($this->_subSites) > 0;
    }
    
    /**
     * Returns sub sites
     * 
     * @return Zym_Sitemap
     */
    public function getSubSites()
    {
        return $this->_subSites;
    }
    
    /**
     * Returns sub site with the given id, or null
     * 
     * @param string $id
     * @return Zym_Sitemap_Site|null
     */
    public function getSubSite($id)
    {
        if (null === $this->_subSites) {
            return null;
        }
        
        return $this->_subSites->getSite($id);
    }
    
    /**
     * Adds a sub site
     * 
     * @return void
     */
    public function addSubSite(Zym_Sitemap_Site $site)
    {
        if (null === $this->_subSites) {
            $this->_subSites = new Zym_Sitemap();
        }
        
        $this->_subSites->addSite($site);
    }
    
    /**
     * Removes sub site with the given id
     * 
     * @param string $id
     * @return void
     */
    public function removeSubSite($id)
    {
        if (null !== $this->_subSites) {
            $this->_subSites->removeSite($id);
        }
    }
    
    /**
     * Resets default module to whatever is found in front
     * 
     * @return void
     */
    public static function resetDefaultModule()
    {
        $front = Zend_Controller_Front::getInstance();
        self::$_defaultModule = $front->getDefaultModule();
    }
    
    // Magic overloads:
    
    /**
     * Sets value in site
     * 
     * The following values for $name are recognized as site data:
     *   - id         string
     *   - name       string
     *   - title      string|null
     *   - order      int|null
     *   - main       bool
     *   - hidden     bool
     * 
     *   - uri        string
     *   - module     string
     *   - controller string
     *   - action     string
     * 
     *   - route       string|null
     *   - resetParams bool|null
     * 
     * If any other $name is given, the value will be set as custom data.
     * 
     * @param string $name
     * @param mixed $value
     * @return Zym_Sitemap_Site
     */
    public function __set($name, $value)
    {
        // try to recognize $name
        switch ($name) {
            case 'id':
            case 'name':
                if (is_string($value) && !empty($value)) {
                    $this->_data[$name] = $value;
                }
            case 'uri':
            case 'module':
            case 'controller':
            case 'action':
                if (is_string($value)) {
                    $this->_data[$name] = $value;
                }
                break;
                
            case 'main':
            case 'hidden':
                $this->_data[$name] = (bool) $value;
                break;
                
            case 'title':
            case 'route':
                if (null === $value || (is_string($value) && !empty($value))) {
                    $this->_data[$name] = $value;
                }
                break;
                
            case 'resetParams':
                if (null === $value || (is_bool($value))) {
                    $this->_data[$name] = $value;
                }
                break;
                
            case 'order':
                if (null === $value || (int) $value !== 0) {
                    $this->_data[$name] = (int) $value;
                }
                break;
                
            default:
                // $name not recognized, set as custom data
                $this->_customData[$name] = $value;
                break;
        }
        
        return $this;
    }
    
    /**
     * Retrieves a site value
     * 
     * The following values for $name are recognized as site data:
     *   - id         string
     *   - name       string
     *   - title      string|null
     *   - order      int|null
     *   - main       bool
     *   - hidden     bool
     * 
     *   - uri        string
     *   - module     string
     *   - controller string
     *   - action     string
     * 
     *   - route       string|null
     *   - resetParams bool|null
     * 
     * If any other $name is given, it will be considered as custom data.
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } elseif (isset($this->_customData[$name])) {
            return $this->_customData[$name];
        } else {
            return null;
        }
    }
    
    /**
     * Checks if a site value is set
     * 
     * The following values for $name are recognized as site data:
     *   - id         string
     *   - name       string
     *   - title      string|null
     *   - order      int|null
     *   - main       bool
     *   - hidden     bool
     * 
     *   - uri        string
     *   - module     string
     *   - controller string
     *   - action     string
     * 
     *   - route       string|null
     *   - resetParams bool|null
     * 
     * If any other $name is given, it will be considered as custom data.
     * 
     * @param string $name
     * @return mixed
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]) || isset($this->_customData[$name]);
    }
}
