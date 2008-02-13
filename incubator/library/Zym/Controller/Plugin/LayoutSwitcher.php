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
 * @category   Zym_Controller
 * @package    Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Layout
 */
require_once 'Zend/Layout.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_Controller
 * @package    Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_Controller_Plugin_LayoutSwitcher extends Zend_Controller_Plugin_Abstract
{
    /**
     * Default layout name
     *
     * @var string
     */
    protected $_defaultLayout = null;

    /**
     * Layout config
     *
     * @var array
     */
    protected $_layouts = array();

    /**
     * Zend_Layout instance
     *
     * @var Zend_Layout
     */
    protected $_layout = null;

    /**
     * Router
     *
     * @var Zend_Controller_Router_Rewrite
     */
    protected $_router = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_layout = Zend_Layout::getMvcInstance();
        $this->_defaultLayout = $this->_layout->getLayout();

        $this->_router = Zend_Controller_Front::getInstance()->getRouter();
    }

    /**
     * Add a layout
     *
     * @param string $layoutName
     * @param string|array $routeName
     * @return Zym_Controller_Plugin_LayoutSwitcher
     */
    public function addRoute($layoutName, $routeNames)
    {
        if (!is_array($routeNames)) {
            $routeNames = (array) $routeNames;
        }

        foreach ($routeNames as $routeName) {
            $this->_layouts[$routeName] = $layoutName;
        }

        return $this;
    }

    /**
     * Switch layout depending on the current route
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $currentRouteName = $this->_router->getCurrentRouteName();

        if (array_key_exists($currentRouteName, $this->_layouts)) {
            $this->_layout->setLayout($this->_layouts[$currentRouteName]);
        } else {
            $this->_layout->setLayout($this->_defaultLayout);
        }
    }
}