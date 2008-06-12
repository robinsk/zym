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
 * Base class for navigation related helpers
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */ 
abstract class Zym_View_Helper_Html_Navigation extends Zym_View_Helper_Html_Abstract
{
    /**
     * Container to operate on
     * 
     * @var Zym_Navigation_Container
     */
    protected $_container;
    
    /**
     * Proxy to the navigation container
     *
     * @param  string $method     method in the container to call
     * @param  array  $arguments  [optional] arguments to pass
     * @return Zym_View_Helper_Navigation
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
     * @param  Zym_Navigation_Container $container  container to operate on
     * @return void
     */
    public function setNavigation(Zym_Navigation_Container $container)
    {
        $this->_container = $container;
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
        // get attribs for anchor element
        $attribs = array(
            'href'   => $page->getHref(),
            'id'     => $page->getId(),
            'title'  => $page->getTitle(),
            'class'  => $page->getClass(),
            'target' => $page->getTarget()
        );
        
        return "<a {$this->_htmlAttribs($attribs)}>{$page->getLabel()}</a>";
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
