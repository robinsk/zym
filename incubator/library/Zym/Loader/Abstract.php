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
 * @package    Zym_Loader
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Loader
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Loader_Abstract
{
    /**
     * @var string
     */
    protected $_modelDirectory = null;

    /**
     * @var string
     */
    protected $_modelPrefix = null;

    /**
     * @var Zend_Controller_Dispatcher_Standard
     */
    protected $_dispatcher = null;

    /**
     * @var Zend_Controller_Request
     */
    protected $_request = null;

    /**
     * Constructor.
     * Set dispatcher and request
     */
    public function __construct()
    {
        if (!$this->_modelDirectory) {
            throw new Exception('Model directory must be set.');
        }

        $frontController = Zend_Controller_Front::getInstance();
        $this->_dispatcher = $frontController->getDispatcher();
        $this->_request = $frontController->getRequest();
    }

    /**
     * Load the model
     * 
     * @throws Exception
     * @param string $modelName
     * @param string $module
     * @param string $modelPrefix
     */
    public function loadModel($modelName, $module = null, $modelPrefix = null)
    {
        $modelName = ucfirst($modelName);
        $fileName = $modelName . '.php';

        if (!$module) {
            $module = $this->_request->getModuleName();
        }

        if (!$modelPrefix) {
            $modelPrefix = $this->_modelPrefix;
        }

        if ($modelPrefix) {
            $modelName = ucfirst($modelPrefix) . '_' . $modelName;
        }

        $modelName = str_ireplace('_', '/', $modelName);
        
        if (class_exists($modelName, false) || interface_exists($modelName, false)) {
            return true;
        }
        
        $controllerDirectory = $this->_dispatcher->getControllerDirectory($module);
        $moduleDirectory = dirname($controllerDirectory);
        $modelDirectory = $moduleDirectory . '/' . $this->_modelDirectory;
        
        Zend_Loader::loadFile($fileName, $modelDirectory, true);
        
        if (!class_exists($modelName, false)) {
            throw new Zym_Loader_Exception('Failed to load class "' . $modelName . '"');
        }
        
        return true;
    }
}