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
 * @package Zym_Timer
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Cache
 */
require_once 'Zym/Cache.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Timer
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_CacheTest extends PHPUnit_Framework_TestCase
{        
    /**
     * Make sure getDefaultBackend() throws exception 
     *
     */
    public function testGetDefaultBackendShouldThrowExceptionWhenConfigNotSet()
    {
        $this->setExpectedException('Zym_Cache_Exception');
        Zym_Cache::setDefaultBackend(null);
        Zym_Cache::getDefaultBackend();
    }
    
    /**
     * Make sure set config works
     *
     */
    public function testSetConfigShouldWork()
    {
        $config = new Zend_Config(array(
           'default_backend' => 'File',
           
           'frontend' => array(
               'Core' => array('caching' => false)
           ),
           
           'backend' => array(
               'APC' => array(),
               'File' => array('cache_dir' => '/tmp'),
               'Sqlite' => array(
                   'cache_db_complete_path' => 'foo/bar.sqlite'
               )
           )
        ));
        
        Zym_Cache::setConfig($config);
        
        $this->assertEquals(Zym_Cache::getDefaultBackend(), 'File');
        $this->assertEquals(Zym_Cache::getBackendOptions('Apc'), array());
        $this->assertEquals(Zym_Cache::getBackendOptions('sqlite'), array(
           'cache_db_complete_path' => 'foo/bar.sqlite'
        ));
        $this->assertEquals(Zym_Cache::getFrontendOptions('Core'), array('caching' => false));
    }
    
    /**
     * Make sure factory returns core
     *
     */
    public function testFactoryReturnsCore()
    {
        $core = Zym_Cache::factory('Core', 'file', array('caching' => false));
        $this->assertEquals('Zend_Cache_Core', get_class($core));
        
        $core = Zym_Cache::factory();
        $this->assertEquals('Zend_Cache_Core', get_class($core));
    }
}