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
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Mail
 */
require_once 'Zend/Mail.php';

/**
 * Mail component configuration
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Mail extends Zym_App_Resource_Abstract
{
    /**
     * Default transport adapter prefix
     *
     */
    const DEFAULT_TRANSPORT_PREFIX = 'Zym_App_Resource_Mail_Transport';
    
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'default_transport' => null,        
            'transport'         => array(),
            'transport_map'     => array()
        )
    );


    /**
     * Setup mail
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {
        // Don't do anything if we already have our default obj
        if ($config->default_transport instanceof Zend_Mail_Transport_Abstract) {
            Zend_Mail::setDefaultTransport($config->default_transport);
            return;
        }
        
        // Do we skip?
        if (!$config->default_transport) {
            return;
        }
        
        // Transport Config
        $transport = $this->_loadTransport($config);
        
        // Set default mail transport
        Zend_Mail::setDefaultTransport($transport);
    }
    
    /**
     * Load transport settings
     *
     * @param Zend_Config $config
     * @return Zend_Mail_Transport_Abstract
     */
    protected function _loadTransport(Zend_Config $config)
    {
        // Make lowercase
        $defaultTransport = strtolower($config->default_transport);
        $transportMap     = array_change_key_case($config->transport_map->toArray(), CASE_LOWER);
        
        // Load transport
        $transportConfig = null;
        if ($config->transport->$defaultTransport instanceof Zend_Config) {
            $transportConfig = $config->transport->$defaultTransport;
        }
        
        $transportClass = $this->_parseTransportMap($defaultTransport, $transportMap);
        $transport = call_user_func(array($transportClass, 'getTransport'), $transportConfig);

        if (!$transport instanceof Zend_Mail_Transport_Abstract) {
            /**
             * @see Zym_App_Resource_Mail_Exception
             */
            require_once 'Zym/App/Resource/Mail/Exception.php';
            throw new Zym_App_Resource_Mail_Exception(
                'Could not load mail transport "' . $defaultTransport . '"'
            );
        }
        
        return $transport;
    }
    
    
    protected function _parseTransportMap($item, array $map)
    {
        $path = null;
        
        if (array_key_exists($item, $map)) {
            $mapItem = $map[$item];
            if (isset($mapItem['prefix']) && isset($mapItem['path'])) {
                $namespace = $prefix;
                $path = $mapItem['path'];
            } else if (is_string($mapItem)) {
                // Assume prefix was given
                $namespace = $mapItem;
            }
            
            // Make sure we have a class prefix
            if (!$namespace) {
                /**
                 * @see Zym_App_Resource_Mail_Exception
                 */
                require_once 'Zym/App/Resource/Mail/Exception.php';
                throw new Zym_App_Resource_Mail_Exception(
                    'Could not determine transport map classname of "' . $item . '"'
                );
            }
        } else {
            $namespace = self::DEFAULT_TRANSPORT_PREFIX;
        }
        
        $classname = rtrim($namespace, '_') . '_' . ucfirst($item);
        Zend_Loader::loadClass($classname, $path);
        return $classname;
    }
}