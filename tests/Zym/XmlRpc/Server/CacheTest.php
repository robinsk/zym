<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym_Tests
 * @package    Zym_XmlRpc
 * @subpackage Server
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_XmlRpc_Server_Cache
 */
require_once 'Zym/XmlRpc/Server/Cache.php';

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Zend_XmlRpc_Server
 */
require_once 'Zend/XmlRpc/Server.php';

/**
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym_Tests
 * @package   Zym_XmlRpc
 * @package   Server
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_XmlRpc_Server_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_XmlRpc_Server object
     *
     * @var Zend_XmlRpc_Server
     */
    private $_server;

    /**
     * Zend_Cache_Core
     *
     * @var Zend_Cache_Core
     */
    private $_cache;

    /**
     * Setup environment
     *
     * @return void
     */
    public function setUp()
    {
        $this->_server = new Zend_XmlRpc_Server();
        $this->_server->setClass('Zym_XmlRpc_Server_Cache', 'cache');

        $this->_cache = Zend_Cache::factory('Core', 'File', array(), array('cache_dir' => dirname(__FILE__)));
    }

    /**
     * Teardown environment
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_cache->clean();
        unset($this->_server, $this->_cache);
    }

    /**
     * Tests functionality of both get() and save()
     *
     * @return void
     */
    public function testGetSave()
    {
        if (!is_writeable(dirname(__FILE__))) {
            $this->markTestIncomplete('Directory no writable');
        }

        $this->assertTrue(Zym_XmlRpc_Server_Cache::save('cache', $this->_cache, $this->_server));
        $expected = $this->_server->listMethods();

        $server   = new Zend_XmlRpc_Server();
        $this->assertTrue(Zym_XmlRpc_Server_Cache::get('cache', $this->_cache, $server));

        $actual = $server->listMethods();
        $this->assertSame($expected, $actual);
    }

    /**
     * Zend_XmlRpc_Server_Cache::delete() test
     *
     * @return void
     */
    public function testDelete()
    {
        if (!is_writeable(dirname(__FILE__))) {
            $this->markTestIncomplete('Directory no writable');
        }

        $this->assertTrue(Zym_XmlRpc_Server_Cache::save('cache', $this->_cache, $this->_server));
        $this->assertTrue(Zym_XmlRpc_Server_Cache::delete('cache', $this->_cache));
    }

    /**
     * Invalid cache
     *
     * @return void
     */
    public function testShouldReturnFalseWithInvalidCache()
    {
        if (!is_writeable(dirname(__FILE__))) {
            $this->markTestIncomplete('Directory no writable');
        }

        $this->_cache->save('asdf', 'cache');

        $server = new Zend_XmlRpc_Server();
        $this->assertFalse(Zym_XmlRpc_Server_Cache::get('cache', $this->_cache, $server));

        $server = new Zend_XmlRpc_Server();
        $this->assertFalse(Zym_XmlRpc_Server_Cache::get('fakeId', $this->_cache, $server));
    }
}