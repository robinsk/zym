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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Abstract view helper
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_View_Helper_Abstract extends Zend_View_Helper_Abstract
    implements Zend_View_Helper_Interface
{
    /**
     * View Object
     *
     * @var Zend_View_Abstract
     */
    private $_view;

    /**
     * Clone view object
     *
     * @return Zend_View_Abstract
     */
    public function cloneView()
    {
        if (!$this->_view instanceof Zend_View_Abstract) {
            /**
             * @see Zym_View_Helper_Exception
             */
            require_once 'Zym/View/Helper/Exception.php';
            throw new Zym_View_Helper_Exception(
                'A view object of instance Zend_View_Abstract is not set to this helper.'
            );
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
    public function setView(Zend_View_Interface $view)
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
        if (!$this->_view instanceof Zend_View_Abstract) {
            /**
             * @see Zym_View_Helper_Exception
             */
            require_once 'Zym/View/Helper/Exception.php';
            throw new Zym_View_Helper_Exception(
                'A view object of instance Zend_View_Abstract is not set to this helper.'
            );
        }

        return $this->_view;
    }
}