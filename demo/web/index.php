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
define('PATH_PROJECT', realpath(dirname(dirname(__FILE__))) . '/');

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
require_once 'Zym/Debug.php';
require_once 'Zym/App.php';
require_once 'Zym/App/Registry.php';
require_once 'Zend/Cache.php';
require_once 'Zend/Config.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Exception.php';
require_once 'Zend/Controller/Exception.php';
require_once 'Zend/Controller/Action/Exception.php';
require_once 'Zend/Controller/Action/Helper/Abstract.php';
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Response/Abstract.php';
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Plugin/Broker.php';
require_once 'Zend/Log/Writer/Abstract.php';
require_once 'Zend/Log/Filter/Priority.php';
require_once 'Zend/Log/Exception.php';
require_once 'Zym/Log/Writer/Debug.php';
require_once 'Zym/Timer.php';
require_once 'Zym/Timer/Manager.php';
require_once 'Zend/Debug.php';
require_once 'Zend/Log.php';
require_once 'Zym/Debug.php';
require_once 'Zend/Config/Xml.php';
require_once 'Zend/Cache/Core.php';
require_once 'Zend/Cache/Backend.php';
require_once 'Zend/Cache/Backend/Apc.php';
require_once 'Zym/App/Resource/Abstract.php';
require_once 'Zym/App/Resource/Autoload.php';
require_once 'Zym/App/Resource/Php.php';
require_once 'Zym/App/Resource/Cache.php';
require_once 'Zym/Cache.php';
require_once 'Zym/App/Resource/Locale.php';
require_once 'Zend/Locale.php';
require_once 'Zym/App/Resource/Session.php';
require_once 'Zend/Session/Abstract.php';
require_once 'Zend/Session/Namespace.php';
require_once 'Zend/Session.php';
require_once 'Zym/App/Resource/View.php';
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
require_once 'Zend/View/Abstract.php';
require_once 'Zend/View.php';
require_once 'Zym/App/Resource/Layout.php';
require_once 'Zend/Layout.php';
require_once 'Zym/App/Resource/Controller.php';
require_once 'Zym/App/Resource/Route.php';
require_once 'Zend/Cache/Backend/File.php';
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Controller/Dispatcher/Abstract.php';
require_once 'Zend/Controller/Dispatcher/Standard.php';
require_once 'Zend/Controller/Response/Http.php';
require_once 'Zym/Controller/Response/Http.php';
require_once 'Zend/Controller/Plugin/ErrorHandler.php';
require_once 'Zym/Controller/Plugin/ErrorHandler.php';
require_once 'Zym/App/Resource/Controller/Plugin/ErrorHandler.php';
require_once 'Zym/View/Abstract.php';
require_once 'Zym/View/Stream/Wrapper.php';
require_once 'Zym/View.php';
require_once 'Zend/View/Helper/Doctype.php';
require_once 'Zend/Registry.php';
require_once 'Zend/View/Helper/Placeholder/Registry.php';
require_once 'Zend/View/Helper/Placeholder/Container/Abstract.php';
require_once 'Zend/View/Helper/Placeholder/Container.php';
require_once 'Zend/Layout/Controller/Plugin/Layout.php';
require_once 'Zend/Layout/Controller/Action/Helper/Layout.php';
require_once 'Zend/Session/Exception.php';
require_once 'Zend/Controller/Router/Abstract.php';
require_once 'Zend/Controller/Router/Route.php';
require_once 'Zend/Controller/Router/Route/Static.php';
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Request/Exception.php';
require_once 'Zend/Uri.php';
require_once 'Zend/Controller/Router/Route/Module.php';
require_once 'Zym/Controller/Action/Abstract.php';
require_once 'Zend/Filter.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Filter/Inflector.php';
require_once 'Zend/Filter/PregReplace.php';
require_once 'Zend/Filter/Word/SeparatorToSeparator.php';
require_once 'Zend/Filter/Word/UnderscoreToSeparator.php';
require_once 'Zend/Filter/Word/Separator/Abstract.php';
require_once 'Zend/Filter/Word/CamelCaseToSeparator.php';
require_once 'Zend/Filter/Word/CamelCaseToDash.php';
require_once 'Zend/Filter/StringToLower.php';
require_once 'Zend/Controller/Router/Interface.php';
require_once 'Zend/Controller/Dispatcher/Interface.php';
require_once 'Zend/Log/Filter/Interface.php';
require_once 'Zend/Cache/Backend/Interface.php';
require_once 'Zend/Session/SaveHandler/Interface.php';
require_once 'Zend/View/Interface.php';
require_once 'Zym/App/Resource/Controller/Plugin/Interface.php';
require_once 'Zend/Controller/Router/Route/Interface.php';
require_once 'Zend/Filter/Interface.php';
require_once 'Zend/Loader/PluginLoader/Interface.php';
Zym_Debug::getTimer()->start();
Zym_App::run(PATH_PROJECT . 'config/bootstrap.xml', Zym_App::ENV_DEVELOPMENT);

Zend_Controller_Front::getInstance()->dispatch();
Zym_Debug::getTimer()->stop();

print_r(Zym_Debug::getTimer()->getRun());