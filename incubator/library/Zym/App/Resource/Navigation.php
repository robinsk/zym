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
 * @package    Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zym_Navigation
 */
require_once 'Zym/Navigation.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * Zym_App_Resource_Navigation
 *
 * Resource class for Zym_Navigation.
 *
 * @category   Zym
 * @package    Zym_App
 * @subpackage Resource
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_App_Resource_Navigation extends Zym_App_Resource_Abstract
{
    const DEFAULT_REGISTRY_KEY = 'Zym_Navigation';

    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'registry_key' => self::DEFAULT_REGISTRY_KEY,
            'pages' => array()
        )
    );

    /**
     * Setup navigation
     *
     * @return void
     */
    public function setup(Zend_Config $config)
    {
        //Zend_Debug::dump($config->toArray());

        // determine registry key
        $confKey = $config->get('registry_key');
        $key     = is_string($confKey) && strlen($confKey)
                     ? $confKey
                     : self::DEFAULT_REGISTRY_KEY;

        $nav = new Zym_Navigation($config->get('pages'));
        Zend_Registry::set($key, $nav);
    }
}
