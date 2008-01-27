<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category Zym
 * @package Zym_Controller
 * @subpackage Dispatcher
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @link http://spotsec.com
 */

/**
 * Zend_Controller_Dispatcher_Standard
 */
require_once('Zend/Controller/Dispatcher/Standard.php');

/**
 * ErrorHandler that allows module-based error handling
 * 
 * If an errorController is not found inside the current module, then
 * the error is forwarded to the default module's errorController
 * 
 * All exceptions that occur during the dispatch of the module error controller
 * is thrown.
 * 
 * Usage: 
 * <pre>
 * Zend_Controller_Front::getInstance()->registerPlugin(new Zym_Controller_Plugin_ErrorHandler(), 98);
 * </pre>
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @category Zym
 * @package Zym_Controller
 * @subpackage Dispatcher
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
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
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $file . '" in directory "' . $dir . '"');
        }

        if (!class_exists($finalClass)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
        }

        return $finalClass;
    }
}