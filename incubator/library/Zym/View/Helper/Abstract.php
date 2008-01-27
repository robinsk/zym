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
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * Abstract view helper
 *
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
abstract class Zym_View_Helper_Abstract {

    /**
     * View Object
     *
     * @var Zend_View_Abstract
     */
    protected $_view;
    
    /**
     * Clone view object
     *
     * @return Zend_View_Abstract
     */
    public function cloneView() 
    {
        if (!$this->_view) {
            throw new Zym_Exception("A view object of instance Zend_View_Abstract is not set.");
        }
        
        $clonedView = clone $this->getView();
        return $clonedView;
    }
    
    /**
     * Set view object
     *
     * @param Zend_View_Abstract $view
     * @return Zym_View_Helper_Abstract
     */
    public function setView(Zend_View_Abstract $view) 
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Get view object
     *
     * @return Zend_View_Abstract
     */
    public function getView() 
    {
        return $this->_view;
    }
}