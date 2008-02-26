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
 * @package Zym_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * @see Zend_Controller_Dispatcher_Standard
 */
require_once 'Zend/Controller/Dispatcher/Standard.php';

/**
 * Dispatcher
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Standard
{
    /**
     * Format the module name.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatModuleName($unformatted)
    {
        if ($this->_defaultModule == $unformatted && !$this->getParam('prefixDefaultModule')) {
            return $unformatted;
        }

        return ucfirst($this->_formatName($unformatted));
    }

    /**
     * Load a controller class
     *
     * Attempts to load the controller class file from
     * {@link getControllerDirectory()}.  If the controller belongs to a
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className
     * @return string Class name loaded
     * @throws Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className)
    {
        $finalClass  = $className;
        if ($this->_defaultModule != $this->_curModule || $this->getParam('prefixDefaultModule')) {
            $finalClass = $this->formatModuleName($this->_curModule) . '_' . $className;
        }
        if (class_exists($finalClass)) {
            return $finalClass;
        }

        $dispatchDir = $this->getDispatchDirectory();
        $loadFile    = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);
        $dir         = dirname($loadFile);
        $file        = basename($loadFile);

        try {
            Zend_Loader::loadFile($file, $dir, true);
        } catch (Zend_Exception $e) {
            /**
             * @see Zend_Controller_Dispatcher_Exception
             */
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $file . '" in directory "' . $dir . '"');
        }

        if (!class_exists($finalClass)) {
            /**
             * @see Zend_Controller_Dispatcher_Exception
             */
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
        }

        return $finalClass;
    }
}