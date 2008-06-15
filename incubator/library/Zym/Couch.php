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
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Couch_Connection
 */
require_once 'Zym/Couch/Connection.php';

/**
 * @see Zym_Couch_Db
 */
require_once 'Zym/Couch/Database.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Couch
{
    /**
     * Factory
     *
     * @param array|Zend_Config $config
     * @return Zym_Couch_Connection
     */
    public static function factory($config, $isDefault = true)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            /**
             * @see Zym_Couch_Exception
             */
            require_once 'Zym/Couch/Exception.php';

            throw new Zym_Couch_Exception('Config must be an array or instance of Zend_Config.');
        }

        $defaults = array('host'   => 'localhost',
                          'port'   => 5984);

        $config = array_merge($defaults, $config);

        foreach ($defaults as $key => $value) {
        	if (empty($config[$key])) {
        	    throw new Zym_Couch_Exception('Config entry "' . $key . '" can\'t be empty.');
        	}
        }

        $connection = new Zym_Couch_Connection($config['host'], $config['port']);

        if ($isDefault) {
            Zym_Couch_Db::setDefaultConnection($connection);
        }

        return $connection;
    }
}