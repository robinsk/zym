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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Json resource
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Json extends Zym_App_Resource_Abstract
{   
    /**
     * Default Config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'class' => 'Zend_Json',
            'use_builtin_encoder_decoder' => null,
            'max_recursion_depth_allowed' => null
        )
    );

    /**
     * Json parser class
     *
     * @var string
     */
    protected $_jsonClass;
    
    /**
     * PreSetup
     *
     * @param Zend_Config $config
     */
    public function preSetup(Zend_Config $config)
    {
        $class = $config->get('class');
        Zend_Loader::loadClass($class);
        
        
//        if (!(new $class) instanceof Zend_Json) {
//            /**
//             * @see Zym_App_Resource_Exception
//             */
//            require_once 'Zym/App/Resource/Exception.php';
//            throw new Zym_App_Resource_Exception(
//                'Custom JSON class "' . $class . '" is not a child of Zend_Json'
//            );
//        }
        
        $this->_jsonClass = $class;
    }
    
    /**
     * Setup
     *
     * @param Zend_Config $config
     */
    public function setup(Zend_Config $config)
    {
        // Set max recursion depth
        $this->_setMaxRecursionDepthAllowed($config);
        
        // Set whether or not to use PHP's encoder/decoder
        $this->_setUseBuiltinEncoderDecoder($config);
    }
    
    /**
     * Set/force whether or not to use the builtin parser or php's
     *
     * @todo Stare at the person who put EVIL() there
     * @param Zend_Config $config
     */
    protected function _setUseBuiltinEncoderDecoder(Zend_Config $config)
    {
        if ($config->get('use_builtin_encoder_decoder') === null) {
            return;
        }
        
        $useBuiltinEncoderDecoder = (bool) $config->get('use_builtin_encoder_decoder');
        
        // Use EVIL, $this->_jsonClass should be safe since we loaded
        // it with Zend_Loader earlier
        eval($this->_jsonClass . '::$useBuiltinEncoderDecoder = $useBuiltinEncoderDecoder;');
    }
    
    /**
     * Set max recursion depth
     *
     * @param Zend_Config $config
     */
    protected function _setMaxRecursionDepthAllowed(Zend_Config $config)
    {
        if ($config->get('max_recursion_depth_allowed') === null) {
            return;
        }
        
        $maxRecursionDepthAllowed = (int) $config->get('max_recursion_depth_allowed');
        
        // Use EVIL, $this->_jsonClass should be safe since we loaded
        // it with Zend_Loader earlier
        eval($this->_jsonClass . ':: $maxRecursionDepthAllowed = $maxRecursionDepthAllowed;');
    }
}