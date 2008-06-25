<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Project Path
 *
 */
define('PATH_PROJECT', dirname(dirname(__FILE__)) . '/');

/**
 * Include paths
 */
include_once PATH_PROJECT . 'config/paths.php';

/**
 * @see Zym_App
 */
require_once 'Zym/App.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

Zym_App::run(PATH_PROJECT . 'config/bootstrap.xml', Zym_App::ENV_DEVELOPMENT);
Zend_Controller_Front::getInstance()->dispatch();