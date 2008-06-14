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
 * @license http://www.zym-project.com/license New BSD License
 */
 
/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * Setup translation
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Translate extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_PRODUCTION => array(
            'cache' => true
        ),
        
        Zym_App::ENV_DEFAULT => array(
            'cache'   => false,
            'adapter' => 'tmx',
            'data'    => array(),
            'locale'  => null,
            'options' => array(),
        
            'registry' => 'Zend_Translate'
        )
    );

    /**
     * Setup
     *
     * @return void
     */
    public function setup(Zend_Config $config)
    {
        // Setup Cache
        if ($config->get('cache')) {
            $cache = Zym_Cache::factory('Core');
            Zend_Translate::setCache($cache);
        }
    }
}