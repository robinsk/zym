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
 * Helper for setting and retrieving base element for HTML head
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */ 
class Zym_View_Helper_HeadBase extends Zym_View_Helper_Html_Abstract
{
    /**
     * @var string
     */
    protected $_href;
    
    /**
     * @var string
     */
    protected $_target;
    
    /**
     * Retrieves helper for base element and optionally sets href and target
     * 
     * If no href is given, this helper will get the base URI from the front
     * controller, and append this to the base host (scheme, hostname and port)
     * for the current request. This means that unless you want to point the base
     * to another host, you should set the base url in the front controller.
     * 
     * @see Zend_Controller_Front::setBaseUrl()
     * 
     * @param  string $href  [optional]
     * @param  string $target  [optional]
     * @return Zym_View_Helper_HeadBase
     */
    public function headBase($href = null, $target = null)
    {
        $this->_href   = $href;
        $this->_target = $target;
        
        return $this;
    }
    
    /**
     * Determines the base URL/href for using in html
     *
     * @return string
     */
    public function getHost()
    {
        // determine full url for base href
        if (empty($_SERVER['HTTPS']) || strcasecmp($_SERVER['HTTPS'], 'off') == 0) {
            $host = 'http://';
            $host .= $_SERVER['SERVER_NAME'];
            if ((int) $_SERVER['SERVER_PORT'] != 80) {
                $host .= ':' . $_SERVER['SERVER_PORT']; 
            }
        } else {
            $host = 'https://';
            $host .= $_SERVER['SERVER_NAME'];
            if ((int) $_SERVER['SERVER_PORT'] != 443) {
                $host .= ':' . $_SERVER['SERVER_PORT']; 
            }
        }
        
        return $host . '/';
    }
    
    /**
     * Returns base href
     * 
     * @return string
     */
    public function getHref()
    {
        if (null === $this->_href) {
            $front       = Zend_Controller_Front::getInstance();
            $this->_href = $this->getHost() . ltrim($front->getBaseUrl(), '/');
        }
        
        return rtrim($this->_href, '/') . '/';
    }
    
    /**
     * Returns target
     * 
     * @return string|null
     */
    public function getTarget()
    {
        return $this->_target;
    }
    
    /**
     * Renders base element for HTML head
     * 
     * @param string|int $indent  [optional]
     * @return string
     */
    public function toString($indent = null)
    {
        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();
        
        // Attrs
        $attribs = array(
            'href' => $this->getHref(),
            'target' => $this->getTarget()
        );
        
        return $indent . "<base {$this->_htmlAttribs($attribs)} {$this->getClosingBracket()}\n";
    }
    
    /**
     * Magic method: renders base element for HTML head
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
