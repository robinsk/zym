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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Sitemap_Site
 */
require_once 'Zym/Sitemap/Site.php';

/**
 * Zym_Sitemap
 * 
 * This class represents a sitemap tree structure, with extra functionality
 * for getting the active site in the map for the current request, if any, etc.
 * 
 * It's generally a good idea to define your sitemap in a configuration file,
 * then using Zend_Cache with a 'File' frontend that depends on your config
 * file.
 * 
 * Example configuration file (INI):
 * <code>
 * [production]
 * ; home page
 * home.order = -100 ; make sure home is the first site 
 * home.main = true  ; and that it is a main site
 * home.name = "My home"
 * home.module = "default"
 * home.controller = "index"
 * home.action = "index"
 * 
 * ; an "about page" that is hidden from the main sitemap
 * about.main = true
 * about.hidden = true
 * about.name = "sitemap.default.about.index" ; relies on auto-translation
 * about.module = "default"
 * about.controller = "about"
 * about.action = "index"
 * 
 * ;
 * feed.main = true
 * feed.name = "sitemap.default.feed.index"
 * feed.module = "default"
 * feed.controller = "feed"
 * feed.action = "index"
 * 
 * ; rss feed as a sub site of feed
 * feed.sub.rss.name = "RSS feed"
 * feed.sub.rss.title = "RSS 2.0"
 * feed.sub.rss.module = "default"
 * feed.sub.rss.controller = "feed"
 * feed.sub.rss.action = "rss"
 * 
 * ; atom feed as a sub site of feed
 * feed.sub.atom.name = "Atom feed"
 * feed.sub.atom.title = "Atom 1.0"
 * feed.sub.atom.module = "default"
 * feed.sub.atom.controller = "feed"
 * feed.sub.atom.action = "atom"
 * </code>
 * 
 * A sitemap may be rendered using the Sitemap view helper. Following the
 * "locale-aware" principle of Zend Framework, where locale-aware components
 * will try to fetch the translator from Zend_Registry using 'Zend_Translate',
 * this component uses the same for making an application "sitemap-aware",
 * meaning you could put your sitemap in Zend_Registry using the key
 * 'Zym_Sitemap'.
 * 
 * @see Zym_View_Helper_Sitemap
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Sitemap
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Sitemap implements Iterator, Countable
{
    /**
     * Associative array containing Zym_Sitemap_Site objects
     * 
     * Keys in the array refer to the id of the Zym_Sitemap_Site.
     * 
     * @var array
     */
    protected $_sites = array();

    /**
     * Order in which to display and iterate sites
     * 
     * @var array
     */
    protected $_order = array();

    /**
     * Whether internal order has been updated
     * 
     * @var bool
     */
    protected $_orderUpdated = false;

    /**
     * Creates a sitemap
     * 
     * @param array|Zend_Config $map  [optional]
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set sitemap from options array
     * 
     * @param  array $options 
     * @return Zym_Sitemap
     */
    public function setOptions(array $options)
    {
        foreach ($options as $id => $site) {
            if (!isset($site['id'])) {
                $site['id'] = $id;
            }
            $this->addSite(new Zym_Sitemap_Site($site));
        }
        //exit(Zend_Debug::dump($this->_sites, 'sites', false));
        return $this;
    }

    /**
     * Set form state from config object
     * 
     * @param  Zend_Config $config 
     * @return Zym_Sitemap
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }
    
    /**
     * Adds a site to the sitemap
     * 
     * @return Zym_Sitemap
     */
    public function addSite(Zym_Sitemap_Site $site)
    {
        $this->_sites[$site->id] = $site;

        $this->_order[$site->id] = $site->order;
        $this->_orderUpdated = true;
        
        return $this;
    }
    
    /**
     * Returns active site
     * 
     * @return Zym_Sitemap_Site|null
     */
    public function getActiveSite()
    {
        return $this->_getActiveSite($this);
    }
    
    /**
     * Returns active main site, if any
     * 
     * @return Zym_Sitemap_Site|null
     */
    public function getActiveMainSite()
    {
        return $this->_getActiveSite($this, true);
    }
    
    /**
     * Returns active site from a given sitemap, or null
     * 
     * @param bool $onlyMain  [optional] defaults to false
     * @return Zym_Sitemap_Site|null
     */
    public function _getActiveSite($sitemap, $onlyMain = false)
    {
        foreach ($sitemap as $id => $site) {
            if ($onlyMain && !$site->main) {
                continue;
            }
            
            if ($site->isActive($onlyMain)) {
                return $site;
            }
            
            if ($site->hasSubSites()) {
                $sub = $this->_getActiveSite($site->getSubSites(), $onlyMain);
                if ($sub) {
                    return $sub;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Returns site with the given id, or null
     * 
     * @param string $id
     * @return Zym_Sitemap_Site|null
     */
    public function getSite($id)
    {
        if (!is_string($id) || empty($id)) {
            return null;
        }
        
        if (isset($this->_sites[$id])) {
            return $this->_sites[$id];
        } else {
            return null;
        }
    }
    
    /**
     * Removes site with the given id
     * 
     * @param string $id
     * @return void
     */
    public function removeSite($id)
    {
        if (!is_string($id) || empty($id)) {
            return;
        }
        
        if (isset($this->_sites[$id])) {
            unset($this->_sites[$id]);
            unset($this->_order[$id]);
            $this->_orderUpdated = true;
        }
    }
    
    // Magic overloads:
    
    /**
     * Adds/sets a site in the sitemap
     * 
     * @param string $id
     * @param Zym_Sitemap_Site $sitee 
     */
    public function __set($id, Zym_Sitemap_Site $site)
    {
        $site->id = $id;
        return $this->addSite($site);
    }
    
    /**
     * Retrieves site with the given id
     * 
     * @param string $id
     * @return Zym_Sitemap_Site|null
     */
    public function __get($id)
    {
        if (isset($this->_sites[$id])) {
            return $this->_sites[$id];
        }
        
        return null;
    }
    
    /**
     * Checks if site with the given id exists
     * 
     * @param string $id
     * @return bool
     */
    public function __isset($id)
    {
        return isset($this->_sites[$id]);
    }
 
    // Interfaces: Iterator, Countable

    /**
     * Current site
     * 
     * @return Zym_Sitemap_Site
     */
    public function current()
    {
        $this->_sort();
        current($this->_order);
        $key = key($this->_order);
        
        if (isset($this->_sites[$key])) {
            return $this->_sites[$key];
        } else {
            require_once 'Zym/Sitemap/Exception.php';
            $msg = 'Corruption detected in sitemap; invalid key found in internal iterator';
            throw new Zym_Sitemap_Exception($msg);
        }
    }

    /**
     * Current site id
     * 
     * @return string
     */
    public function key()
    {
        $this->_sort();
        return key($this->_order);
    }

    /**
     * Move pointer to next site group
     * 
     * @return void
     */
    public function next()
    {
        $this->_sort();
        next($this->_order);
    }

    /**
     * Move pointer to beginning of site loop
     * 
     * @return void
     */
    public function rewind()
    {
        $this->_sort();
        reset($this->_order);
    }

    /**
     * Determine if current site is valid
     * 
     * @return bool
     */
    public function valid()
    {
        $this->_sort();
        return (current($this->_order) !== false);
    }

    /**
     * Count of sites that are iterable
     * 
     * @return int
     */
    public function count()
    {
        return count($this->_order);
    }

    /**
     * Sort items according to their order
     * 
     * @return void
     */
    protected function _sort()
    {
        if ($this->_orderUpdated) {
            $items = array();
            $index = 0;
            foreach ($this->_order as $key => $order) {
                if (null === $order) {
                    if (array_search($index, $this->_order, true)) {
                        ++$index;
                    }
                    $items[$index] = $key;
                    ++$index;
                } else {
                    $items[$order] = $key;
                }
            }

            $items = array_flip($items);
            asort($items);
            $this->_order = $items;
            $this->_orderUpdated = false;
        }
    }
}
