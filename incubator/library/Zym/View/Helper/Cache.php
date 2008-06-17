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
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Cache view helper
 * 
 * This helper will prefix the cache id with the current file
 * 
 * <code>
 * <? if ($module = $this->cache('module')) : ?>
 *     <?= $module; ?>
 * <? else : ?>
 *     <?= $module = $this->action('module', 'foo'); ?>
 *     <? $this->cache()->save($module); ?>
 * <? endif; ?>
 * </code>
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_Cache extends Zym_View_Helper_Abstract
{
    /**
     * Cache Instance
     *
     * @var Zend_Cache_Core
     */
    protected $_cache;
    
    /**
     * Cache id prefix
     *
     * @var string
     */
    protected $_cachePrefix = 'Zym_View_Helper_Cache_';
    
    /**
     * Get
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true) => for internal use
     * 
     * @return mixed|false Cached datas
     */
    public function cache($id = null, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {   
        // Return self
        if ($id === null) {
            return $this;
        }
        
        $id = $this->getCachePrefix() . $id;
        
        return $this->getCache()->load($id, $doNotTestCacheValidity, $doNotUnserialize);
    }
    
    /**
     * Save cache entry
     *
     * @param  mixed $data           Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param  string $id            Cache id (if not set, the last cache id will be used)
     * @param  array $tags           Cache tags
     * @param  int $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * 
     * @throws Zend_Cache_Exception
     * @return boolean
     */
    public function save($data, $id = null, array $tags = array(), $specificLifetime = false)
    {
        if ($id !== null) {
            $id = $this->getCachePrefix() . $id;
        }
        
        return $this->getCache()->save($data, $id, $tags, $specificLifetime);
    }
    
    /**
     * Get cache obj
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if ($this->_cache === null) {
            /**
             * @see Zym_Cache
             */
            require_once 'Zym/Cache.php';
            $cache = Zym_Cache::factory('Core');
            $this->setCache($cache);
        }
        
        return $this->_cache;
    }
    
    /**
     * Set cache obj
     *
     * @param Zend_Cache_Core $cache
     * @return Zym_View_Helper_Cache
     */
    public function setCache(Zend_Cache_Core $cache)
    {        
        $this->_cache = $cache;
        
        return $this;
    }
    
    /**
     * Get cache Id prefix
     *
     * @return string
     */
    public function getCachePrefix()
    {
        // Cheat: hack to retrieve private view variable _file
        $view = (array) $this->getView();
        $file = isset($view['\0Zend_View_Abstract\0_file']) 
                    ? $view['\0Zend_View_Abstract\0_file'] : null;
        
        $script = ($file !== null) ? md5($file) : null;
        
        return $this->_cachePrefix . $script . '_';
    }
    
    /**
     * Set cache id prefix
     *
     * @param string $prefix
     * @return Zym_View_Helper_Cache
     */
    public function setCachePrefix($prefix)
    {   
        $this->_cachePrefix = $prefix . '_';  
        
        return $this;
    }   
}