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
 * Zym_View_Helper_Sitemap
 * 
 * View helper for rendering Zym_Sitemap sitemaps.
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Sitemap extends Zym_View_Helper_Html_Abstract
{
    /**
     * @var Zym_Sitemap
     */
    protected $_sitemap;
    
    /**
     * CSS class to use for list element when rendering as main sitemap
     *
     * @var string
     */
    protected $_cssMain = 'dropdown';
    
    /**
     * CSS class to use for list element when not rendering as a main sitemap
     *
     * @var string
     */
    protected $_css = '';

    /**
     * Retrieves sitemap helper
     *
     * @return Zym_View_Helper_Sitemap
     */
    public function sitemap()
    {
        return $this;
    }
    
    /**
     * Sets sitemap
     * 
     * @param Zym_Sitemap $sitemap
     * @return Zym_View_Helper_Sitemap
     */
    public function setSitemap(Zym_Sitemap $sitemap)
    {
        $this->_sitemap = $sitemap;
        return $this;
    }
    
    /**
     * Returns sitemap
     * 
     * @return Zym_Sitemap|null
     */
    public function getSitemap()
    {
        if (null === $this->_sitemap) {
            return $this->_getDefaultSitemap();
        }
        
        return $this->_sitemap;
    }
    
    /**
     * Returns default sitemap
     * 
     * @return Zym_Sitemap|null
     */
    protected function _getDefaultSitemap()
    {
        if (Zend_Registry::isRegistered('Zym_Sitemap')) {
            $sitemap = Zend_Registry::get('Zym_Sitemap');
            if ($sitemap instanceof Zym_Sitemap) {
                return $sitemap;
            }
        }
        
        return null;
    }
    
    /**
     * Returns active site
     * 
     * @return Zym_Sitemap_Site|null
     */
    public function getActiveSite()
    {
        $sitemap = $this->getSitemap();
        return null === $sitemap ? null : $sitemap->getActiveSite();
    }
    
    /**
     * Returns active main site, if any
     * 
     * @return Zym_Sitemap_Site|null
     */
    public function getActiveMainSite()
    {
        $sitemap = $this->getSitemap();
        return null === $sitemap ? null : $sitemap->getActiveMainSite();
    }
    
    /**
     * Renders navigation list for a sitemap
     * 
     * @param  Zym_Sitemap $sitemap   sitemap to render
     * @param  string|int  $indent    [optional] initial indentation
     * @param  bool        $main      [optional] whether sitemap should be
     *                                considered as main map, defaults to false
     * @return string
     */
    public function renderSitemap(Zym_Sitemap $sitemap,
                                  $indent = null,
                                  $main = false)
    {
        if (count($sitemap) < 1) {
            return '';
        }
        
        // determine indentation
        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();
        
        // view is required for escaping/translating
        $view = $this->getView();

        // force main to be a boolean
        $main = $main === true;
        
        // determine css class for list element 
        $ulCss = $main ? $this->_cssMain : $this->_css;
        
        // init html string with list element
        $html = "$indent<ul class=\"$ulCss\">\n";
        
        // loop sitemap
        foreach ($sitemap as $id => $site) {
            if ($main && (!$site->main || $site->hidden)) {
                // when rendering as main, sites that are not main sites
                // should not be rendered, and neither should hidden ones
                continue;
            }
            
            // translate site name
            if ($site->translate) {
                $name = $view->translate($site->name);
            }
            
            $name = $view->escape($name);
            
            // see if site has a custom title
            if (isset($site->title)) {
                $title = $site->translate
                       ? $view->translate($site->title)
                       : $site->title;
                $title = $view->escape($title);
            } else {
                $title = $name;
            }
            
            if ($main) {
                $name = "<strong>$name</strong>";
            }
            
            // determine css class for list item
            $liCss = $site->isActive($main) ? 'active' : 'default';
            
            // make list item element for the site
            $html .= "$indent    <li class=\"$liCss\">\n";
            $html .= "$indent        <a href=\"{$site->getHref()}\"";
            $html .= " title=\"$title\">$name</a>\n";
            
            // render sub sites, if any
            if ($site->hasSubSites()) {
                $html .= $this->renderSitemap($site->getSubSites(),
                    $indent . '        ', false);
            }
            
            // end list item element for the site
            $html .= "$indent    </li>\n";
        }
        
        // end html string
        $html .= "$indent</ul>\n";
        
        return $html;
    }
    
    /**
     * Renders navigation list for the active site in the sitemap
     * 
     * @param string|int  $indent       [optional] defaults to no indenting
     * @param bool        $strict       [optional] if set true, sitemap
     *                                  will not be rendered if there is no
     *                                  active site, or there is no subsites
     * @param bool        $includeSelf  [optional] whether to include the
     *                                  active site, or only render subsites
     * @param Zym_Sitemap $sitemap      [optional] specify a sitemap to render
     * @return string
     */
    public function renderActiveSitemap($indent = null,
                                        $strict = false,
                                        $includeSelf = true,
                                        Zym_Sitemap $sitemap = null)
    {
        if (null === $sitemap && !$sitemap = $this->getSitemap()) {
            // no sitemap
            return '';
        }
        
        if (!$site = $sitemap->getActiveMainSite()) {
            // no active site in sitemap
            return '';
        }
        
        if ($site->hasSubSites()) {
            // render sub sites
            $subs = $site->getSubSites();
            if ($includeSelf) {
                // a little hack to include self
                $clone = clone $site;
                $clone->removeSubSites();
                $clone->order = -1337;
                $id = $clone->id;
                if ($subs->isset($id)) {
                    // id already exists in subs, lets hope this works
                    $clone->id = crc32($id);
                }
                $subs->addSite($clone);
            }
            
            return $this->renderSitemap($subs, $indent, false);
        } elseif ($strict) {
            // site has no subsites, and strict is given, return nothing
            return '';
        } elseif ($includeSelf) {
            // site has no subsites, but include self
            $selfmap = new Zym_Sitemap();
            $selfmap->addSite($site);
            return $this->renderSitemap($selfmap, $indent, false);
        }
        
        return '';
    }

    /**
     * Renders sitemap registered in helper
     * 
     * @param string|int $indent  [optional] defaults to no initial indenting
     * @return string
     */
    public function toString($indent = null)
    {
        $sitemap = $this->getSitemap();
        if (null === $sitemap || count($sitemap) < 1) {
            return '';
        }
        
        return $this->renderSitemap($sitemap, $indent, true);
    }
    
    /**
     * Magic: Returns string representation of helper
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
