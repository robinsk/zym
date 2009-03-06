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
 * @see Zym_View_Helper_Navigation_Abstract
 */
require_once 'Zym/View/Helper/Navigation/Abstract.php';

/**
 * Base class for navigation related helpers with proxies to helpers in 
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */ 
class Zym_View_Helper_Navigation
    extends Zym_View_Helper_Navigation_Abstract
{
    /**
     * View helper namespace
     * 
     * @var string
     */
    const NS = 'Zym_View_Helper_Navigation';
    
    /**
     * Plugin loader for navigational view helpers
     * 
     * @var Zend_Loader_PluginLoader_Interface
     */
    protected $_loader;
    
    /**
     * Default proxy to use in {@link render()}
     * 
     * @var string
     */
    protected $_defaultProxy = 'menu';
    
    /**
     * Whether container should be injected when proxying
     * 
     * @var bool
     */
    protected $_injectContainer = true;
    
    /**
     * Helper entry point
     * 
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              operate on
     * @return Zym_View_Helper_Navigation           fluent interface, returns 
     *                                              self
     */
    public function navigation(Zym_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }
        
        return $this;
    }
    
    /**
     * Magic overload: Proxy to other navigation helpers or the container
     * 
     * Examples of usage from a view script or layout:
     * <code>
     * // proxy to Menu helper and render container:
     * echo $this->navigation()->menu();
     * 
     * // proxy to Breadcrumbs helper and set indentation:
     * $this->navigation()->breadcrumbs()->setIndent(8);
     * 
     * // proxy to container and find all pages with 'blog' route:
     * $blogPages = $this->navigation()->findAllByRoute('blog');
     * </code>
     *
     * @param  string $method          helper name or method name in container
     * @param  array  $arguments       [optional] arguments to pass
     * @return mixed                   returns what the proxy returns
     * @throws Zend_View_Exception     if proxying to a helper, and the helper
     *                                 is not an instance of 
     *                                 Zym_View_Helper_Navigation_Abstract
     * @throws BadMethodCallException  if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        // check if call should proxy to another helper
        if ($helper = $this->findHelper($method, false)) {
            // inject container?
            if ($this->getInjectContainer() &&
                !$helper->hasContainer() &&
                reset($arguments) === false) {
                $helper->setContainer($this->getContainer());
            }
            
            return call_user_func_array(array($helper, $method), $arguments);
        }
        
        // default behaviour: proxy call to container
        return parent::__call($method, $arguments);
    }
    
    /**
     * Returns the helper matching $proxy
     * 
     * The helper must implement the interface
     * {@link Zym_View_Helper_Navigaiton_NavigationHelper}.
     * 
     * @param string $proxy                                 proxy name
     * @param bool   $strict                                [optional] whether
     *                                                      an exception should 
     *                                                      be thrown if helper 
     *                                                      cannot be found. 
     *                                                      Default is true.
     * @return Zym_View_Helper_Navigation_NavigationHelper  helper instance
     * @throws Zend_Loader_PluginLoader_Exception           if $strict is true
     *                                                      and helper cannot
     *                                                      be found
     * @throws Zend_View_Exception                          if $strict is true
     *                                                      and helper does not
     *                                                      implement the
     *                                                      specified interface
     */
    public function findHelper($proxy, $strict = true)
    {
        if (!$this->view->getPluginLoader('helper')->getPaths(self::NS)) {
            $this->view->addHelperPath(
                    str_replace('_', '/', self::NS),
                    self::NS);
        }
        
        if ($strict) {
            $helper = $this->view->getHelper($proxy);
        } else {
            try {
                $helper = $this->view->getHelper($proxy);
            } catch (Zend_Loader_PluginLoader_Exception $e) {
                return null;
            }
        }
        
        if (!$helper instanceof Zym_View_Helper_Navigation_Interface) {
            if ($strict) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf(
                        'Proxy helper "%s" is not an instance of ' .
                        'Zym_View_Helper_Navigation_Interface',
                        get_class($helper)));
            }
            
            $helper = null;
        }
        
        return $helper;
    }
    
    // Accessors:
    
    /**
     * Sets the plugin loader that is used for loading navigational helpers
     * 
     * @param Zend_Loader_PluginLoader_Interface $loader  plugin loader
     * @return Zym_View_Helper_Navigation                 fluent interface,
     *                                                    returns self
     */
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader)
    {
        $this->_loader = $loader;
        return $this;
    }
    
    /**
     * Returns the plugin loader that is used for loading navigational helpers
     *  
     * @return Zend_Loader_PluginLoader_Interface  plugin loader that is used
     *                                             for loading navigational
     *                                             helpers
     */
    public function getPluginLoader()
    {
        if (!$this->_loader) {
            /**
             * @see Zend_Loader_PluginLoader
             */
            require_once 'Zend/Loader/PluginLoader.php';
            $this->_loader = new Zend_Loader_PluginLoader();
            $this->_loader->addPrefixPath(self::NS,
                                          str_replace('_', '/', self::NS));
            
        }
        
        return $this->_loader;
    }
    
    /**
     * Sets the default proxy to use in {@link render()}
     * 
     * @param  string $proxy               default proxy 
     * @return Zym_View_Helper_Navigation  fluent interface, returns self
     */
    public function setDefaultProxy($proxy)
    {
        $this->_defaultProxy = (string) $proxy;
        return $this;
    }
    
    /**
     * Returns the default proxy to use in {@link render()}
     * 
     * @return string
     */
    public function getDefaultProxy()
    {
        return $this->_defaultProxy;
    }
    
    /**
     * Sets whether container should be injected when proxying
     * 
     * @param bool $injectContainer        [optional] whether container should 
     *                                     be injected when proxying. Default
     *                                     is true.
     * @return Zym_View_Helper_Navigation  fluent interface, returns self
     */
    public function setInjectContainer($injectContainer = true)
    {
        $this->_injectContainer = (bool) $injectContainer;
    }
    
    /**
     * Returns whether container should be injected when proxying
     * 
     * @return bool  whether container should be injected when proxying
     */
    public function getInjectContainer()
    {
        return $this->_injectContainer;
    }
    
    // Zym_View_Helper_Navigation_Abstract:

    /**
     * Renders helper
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is to render
     *                                              the container registered in
     *                                              the helper.
     * @return string                               helper output
     * @throws Zend_Loader_PluginLoader_Exception   if helper cannot be found
     * @throws Zend_View_Exception                  if helper does not implement
     *                                              the interface specified in
     *                                              {@link findHelper()}
     */
    public function render(Zym_Navigation_Container $container = null)
    {
        $helper = $this->findHelper($this->getDefaultProxy());
        
        // inject container?
        if ($this->getInjectContainer() &&
            !$helper->hasContainer() &&
            null === $container) {
            $container = $this->getContainer();
        }
        
        return $helper->render($container);
    }
}
