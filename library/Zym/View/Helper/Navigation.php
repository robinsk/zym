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
class Zym_View_Helper_Navigation extends Zym_View_Helper_Navigation_Abstract
{
    /**
     * Navigation helper proxies
     * 
     * @var array
     */
    protected $_proxies = array(
        'breadcrumbs',
        'headLink',
        'menu',
        'sitemap'
    );
    
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
        if (in_array($method, $this->getProxies())) {
            $helper = $this->getView()->getHelper($method);
            
            if (!$helper instanceof Zym_View_Helper_Navigation_Abstract) {
                $msg = 'Proxy helper "%s" is not an instance of ' 
                     . 'Zym_View_Helper_Navigation_Abstract';
                throw new Zend_View_Exception(sprintf($msg, get_class($helper)));
            }
            
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
    
    // Accessors:
    
    /**
     * Sets an array of valid helper proxies
     * 
     * @param array $proxies               array of strings
     * @return Zym_View_Helper_Navigation  fluent interface, returns self
     */
    public function setProxies(array $proxies)
    {
        $this->_proxies = $proxies;
    }
    
    /**
     * Returns an array of valid helper proxies
     * 
     * @return array
     */
    public function getProxies()
    {
        return $this->_proxies;
    }
    
    /**
     * Sets the default proxy to use in {@link render()}
     * 
     * @param  string $proxy               default proxy 
     * @return Zym_View_Helper_Navigation  fluent interface, returns self
     * @throws Zend_View_Exception         if given proxy is invalid
     */
    public function setDefaultProxy($proxy)
    {
        if (!in_array($proxy, $this->getProxies())) {
            require_once 'Zend/View/Exception.php';
            $msg = 'Proxy "%s" is not a valid navigation proxy';
            throw new Zend_View_Exception(sprintf($msg, $proxy));
        }
        
        $this->_defaultProxy = $proxy;
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
     * @param  string|int               $indent     [optional] indentation as
     *                                              a string or number of 
     *                                              spaces. Default is null,
     *                                              which will use the indent
     *                                              registered in the helper.
     * @return string                               helper output
     * @throws Zend_View_Exception                  if the registered view is 
     *                                              not an instance of
     *                                              Zend_View_Abstract, or if 
     *                                              something goes wrong when 
     *                                              invoking the proxied helper
     */
    public function render(Zym_Navigation_Container $container = null,
                           $indent = null)
    {
        $proxy = $this->getDefaultProxy();
        $helper = $this->getView()->getHelper($proxy);
            
        if (!$helper instanceof Zym_View_Helper_Navigation_Abstract) {
            $msg = 'Proxy helper "%s" is not an instance of ' 
                 . 'Zym_View_Helper_Navigation_Abstract';
            throw new Zend_View_Exception(sprintf($msg, get_class($helper)));
        }
        
        // inject container?
        if ($this->getInjectContainer() &&
            !$helper->hasContainer() &&
            null === $container) {
            $container = $this->getContainer();
        }
        
        return $helper->render($container, $indent);
    }
}
