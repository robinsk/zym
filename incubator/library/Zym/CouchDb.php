<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_CouchDb_Connection
 */
require_once 'Zym/CouchDb/Connection.php';

/**
 * @see Zym_CouchDb_Database
 */
require_once 'Zym/CouchDb/Database.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_CouchDb
{
    /**
     * Factory
     *
     * @param array|Zend_Config $config
     * @return Zym_CouchDb_Connection
     */
    public static function factory($config, $isDefault = true)
    {
        $defaults = array('host'   => 'localhost',
                          'port'   => 5984);

        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            /**
             * @see Zym_CouchDb_Exception
             */
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Config must be an array or instance of Zend_Config.');
        }

        $config = array_merge($defaults, $config);

        foreach ($config as $key => $value) {
        	if (empty($value)) {
        	    throw new Zym_CouchDb_Exception('Config entry "' . $key . '" can\'t be empty.');
        	}
        }

        $connection = new Zym_CouchDb_Connection($config['host'], $config['port']);

        if ($isDefault) {
            Zym_CouchDb_Database::setDefaultConnection($connection);
        }

        return $connection;
    }
}