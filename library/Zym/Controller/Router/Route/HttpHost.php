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
 * @subpackage Router
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Controller_Router_Route
 */
require_once 'Zend/Controller/Router/Route.php';

/**
 * Router using $_SERVER['HTTP_HOST']...
 *
 * This router can be used for routing subdomains; however, it does have limitations
 * as it cannot generate full urls or match urls that do not have the subdomain
 * in the HTTP_HOST
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Router_Route_HttpHost extends Zend_Controller_Router_Route
{
    /**
     * Domain namespace separator
     *
     */
    const DOMAIN_SEPARATOR = '.';

    /**
     * Domain wildcaard character
     *
     */
    const DOMAIN_WILDCARD  = '*';

    /**
     * Host port separator
     */
    const PORT_SEPARATOR   = ':';

    /**
     * Host cache
     *
     * @var array
     */
    protected $_host            = array();

    /**
     * Host parts
     *
     * @var array
     */
    protected $_hostParts       = array();

    /**
     * Host vars
     *
     * @var array
     */
    protected $_hostVars        = array();

    /**
     * Host count part
     *
     * @var integer
     */
    protected $_hostStaticCount = 0;
    
    /**
     * Holds the cache for match()
     * 
     * Allows skipping of the match process if a route was pre-determined to
     * fail
     *
     * @var array
     */
    protected static $_matchCache = array();

    /**
     * Route allowing matching of the HTTP_HOST
     *
     * $host => ':user.*.*' would match SpotSec.Foo.Com
     *
     * @param string $host
     * @param string $route
     * @param array $defaults
     * @param array $reqs
     */
    public function __construct($host, $route, array $defaults = array(), array $reqs = array())
    {
        $host = trim($host, $this->_urlDelimiter);

        if (!empty($host)) {
            // Separate foo.com into array('com', 'foo')
            $domains = array_reverse(explode(self::DOMAIN_SEPARATOR, $host));

            // Parse out url variables
            foreach ($domains as $pos => $part) {
                // Check if :var
                if (substr($part, 0, 1) == $this->_urlVariable) {
                    $name  = substr($part, 1);
                    $regex = isset($reqs[$name]) ? $reqs[$name] : $this->_defaultRegex;

                    // Set to be evaluated later
                    $this->_hostParts[$pos] = array('name' => $name, 'regex' => $regex);
                    $this->_hostVars[]      = $name;
                } else {
                    $this->_hostParts[$pos] = array('regex' => $part);

                    // Increment if not a *.*
                    if ($part !== self::DOMAIN_WILDCARD) {
                        $this->_hostStaticCount++;
                    }
                }
            }
        }

        parent::__construct($route, $defaults, $reqs);
    }

    /**
     * Instantiates the route based on Zend_Config
     *
     * @param Zend_Config $config
     * @return Zym_Controller_Router_HttpHost
     */
    public static function getInstance(Zend_Config $config)
    {
        $reqs = ($config->reqs instanceof Zend_Config) ? $config->reqs->toArray() : array();
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->host, $config->route, $defs, $reqs);
    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        if (!count($this->_host)) {
            // We need a host
            if (!isset($_SERVER['HTTP_HOST'])) {
                return false;
            }

            // Get host
            $requestHost = trim($_SERVER['HTTP_HOST']);

            // Assign host and remove any :42 port indicators
            $requestHost = preg_replace('/' . self::PORT_SEPARATOR . '\d+/', '', $requestHost);
            $this->_host = array_reverse(explode(self::DOMAIN_SEPARATOR, $requestHost));

        }
        
        // If cache checked, continue match
        $matchKey = serialize($this->_hostParts);
        if (isset(self::$_matchCache[$matchKey]) && self::$_matchCache[$matchKey] === true) {
            return parent::match($path);
        } else if (isset(self::$_matchCache[$matchKey]) && self::$_matchCache[$matchKey] === false) {
            return false;
        }

        $hostStaticCount = 0;

        if (!empty($requestHost)) {
            $defaults = $this->_defaults;

            if (count($defaults)) {
                $unique = array_combine(array_keys($defaults), array_fill(0, count($defaults), true));
            } else {
                $unique = array();
            }

            foreach ($this->_host as $pos => $part) {
                // Make sure required parts exist (eg foo.com -> foo and com)
                if (!isset($this->_hostParts[$pos])) {
                    self::$_matchCache[$matchKey] = false;
                    return false;
                }

                // Don't match if wildcard
                if ($this->_hostParts[$pos]['regex'] === self::DOMAIN_WILDCARD) {
                    continue;
                }

                $hostPart = $this->_hostParts[$pos];
                $name     = isset($hostPart['name']) ? $hostPart['name'] : null;

                if ($name === null) {
                    if ($hostPart['regex'] != $part) {
                        self::$_matchCache[$matchKey] = false;
                        return false;
                    }
                } else if ($hostPart['regex'] === null) {
                    if (strlen($part) == 0) {
                        self::$_matchCache[$matchKey] = false;
                        return false;
                    }
                } else {
                    $regex = $this->_regexDelimiter . '^' . $hostPart['regex'] . '$' . $this->_regexDelimiter . 'iu';
                    if (!preg_match($regex, $part)) {
                        self::$_matchCache[$matchKey] = false;
                        return false;
                    }
                }

                if ($name !== null) {
                    // It's a variable. Setting a value
                    $this->_values[$name] = $part;
                    $unique[$name] = true;
                } else {
                    $hostStaticCount++;
                }
            }
        }

        $return = $this->_values + $this->_defaults;

        // Check if all static mappings have been met
        if ($this->_hostStaticCount != $hostStaticCount) {
            self::$_matchCache[$matchKey] = false;
            return false;
        }

        // Check if all map variables have been initialized
        foreach ($this->_hostVars as $var) {
            if (!array_key_exists($var, $return)) {
                self::$_matchCache[$matchKey] = false;
                return false;
            }
        }

        // Set route match cache
        self::$_matchCache[$matchKey] = true;
        
        return array_merge($return, parent::match($path));
    }
    
    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * We cannot generate http://subdomain.domain.com/ due to the fact
     * that the url helper adds the baseUrl in front of it... *sigh*
     * 
     * @todo figure out a workaround
     * 
     * @param  array $data An array of variable and value pairs used as parameters
     * @param  boolean $reset Whether or not to set route defaults with those provided in $data
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false)
    {
        return parent::assemble($data, $reset);
    }
    
    /**
     * __sleep()
     *
     * Runs before serialization
     *
     */
    public function __sleep()
    {
        // Remove $this->_host because the request may be different if this route is cached
        $this->_host = array();
    }
}
