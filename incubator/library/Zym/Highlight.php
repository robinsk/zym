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
 * @package    Zym_Highlight
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zym_Highlight_Adapter_Abstract
 */
require_once 'Zym/Highlight/Adapter/Abstract.php';

/**
 * Zym_Highlight
 * 
 * This class contains a factory for Zym_Highlight adapters.
 * 
 * The Zym_Highlight package defines adapters for performing syntax highlighting
 * of code and other sorts of texts. 
 *
 * @category   Zym
 * @package    Zym_Highlight
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Highlight
{
    /**
     * Factory for Zym_Highlight_Adapter_Abstract classes
     *
     * First argument may be a string containing the base of the adapter class 
     * name, e.g. 'Colorer' corresponds to class Zym_Highlight_Adapter_Colorer.
     * This is case-insensitive.
     *
     * First argument may alternatively be an object of type Zend_Config.
     * The adapter class base name is read from the 'adapter' property.
     * The adapter config parameters are read from the 'options' property.
     *
     * Second argument is optional and may be an associative array of key-value 
     * pairs.  This is used as the argument to the adapter constructor.  
     *
     * If the first argument is of type Zend_Config, it is assumed to contain 
     * all parameters, and the second argument is ignored.
     *
     * @param string|Zend_Config $adapter
     * @param array $options  [optional] associative array of options
     * @return Zym_Highlight_Adapter_Abstract
     */
    public static function factory($adapter, $options = array())
    {
        // convert Zend_Config argument to plain string and separate options
        if ($adapter instanceof Zend_Config) {
            if (isset($adapter->options)) {
                $options = $adapter->options->toArray();
            } else {
                $options = array();
            }
            if (isset($adapter->adapter)) {
                $adapter = (string) $adapter->adapter;
            } else {
                $adapter = null;
            }
        }

        // verify that adapter parameters are in an array
        if (!is_array($options)) {
            require_once 'Zym/Highlight/Exception.php';
            $msg = 'Highlight options must be in an array or a Zend_Config object';
            throw new Zym_Highlight_Exception($msg);
        }

        // verify that an adapter name has been specified.
        if (!is_string($adapter) || empty($adapter)) {
            require_once 'Zym/Highlight/Exception.php';
            $msg = 'Adapter name must be specified in a string';
            throw new Zym_Highlight_Exception($msg);
        }

        // form full adapter class name
        $adapterNamespace = 'Zym_Highlight_Adapter';
        if (isset($options['adapterNamespace'])) {
            $adapterNamespace = $options['adapterNamespace'];
            unset($options['adapterNamespace']);
        }
        $adapterName = $adapterNamespace . '_' . $adapter;
        $adapterName = str_replace(' ', '_', ucwords(str_replace('_', ' ', $adapterName)));
        
        // load the adapter class (might throw an exception)
        Zend_Loader::loadClass($adapterName);

        // create an instance of the adapter class
        $handlerAdapter = new $adapterName($options);

        // verify that the object created is a descendent of adapter abstract
        if (! $handlerAdapter instanceof Zym_Highlight_Adapter_Abstract) {
            require_once 'Zym/Highlight/Exception.php';
            $msg = "Adapter class '$adapterName' does not extend ";
            $msg .= 'Zym_Highlight_Adapter_Abstract';
            throw new Zym_Highlight_Exception($msg);
        }

        return $handlerAdapter;
    }
}
