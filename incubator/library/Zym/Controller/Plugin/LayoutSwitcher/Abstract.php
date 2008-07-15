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
 * @package    Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @see Zend_Layout
 */
require_once 'Zend/Layout.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Controller_Plugin_LayoutSwitcher_Abstract extends Zend_Controller_Plugin_Abstract
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
     * Add a layout
     *
     * @param string $layoutName
     * @param string|array $ruleNames
     * @return Zym_Controller_Plugin_LayoutSwitcher
     */
    public function addLayout($layoutName, $ruleNames)
    {
        if (!is_array($ruleNames)) {
            $ruleNames = (array) $ruleNames;
        }

        foreach ($ruleNames as $rule) {
            $this->_layouts[$rule] = $layoutName;
        }

        return $this;
    }
    
    /**
     * Switch layout based on the given condition
     *
     * @param string $ruleName
     */
    protected function _switchLayout($ruleName)
    {
        if (!$this->_layout) {
            $layout = Zend_Layout::getMvcInstance();
            
            if (!$layout) {
                $layout = Zend_Layout::startMvc();
            }
            
            $this->_layout = $layout;
        }
        
        if (!$this->_defaultLayout) {
            $this->_defaultLayout = $this->_layout->getLayout();
        }
        
        if (array_key_exists($ruleName, $this->_layouts)) {
            $this->_layout->setLayout($this->_layouts[$ruleName]);
        } else {
            $this->_layout->setLayout($this->_defaultLayout);
        }
    }
}