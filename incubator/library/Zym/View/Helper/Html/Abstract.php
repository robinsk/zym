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
 * @subpackage Helper_Html
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_View_Helper_Abstract
 */
require_once 'Zym/View/Helper/Abstract.php';

/**
 * Abstract view helper
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Helper_Html
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_View_Helper_Html_Abstract extends Zym_View_Helper_Abstract 
{
    
    /**
     * Is doctype XHTML?
     * 
     * @return boolean
     */
    protected function _isXhtml()
    {
        $doctype = $this->getView()->doctype();
        return $doctype->isXhtml();
    }
    
    /**
     * Converts an associative array to a string of tag attributes.
     *
     * @access public
     *
     * @param array $attribs From this array, each key-value pair is
     * converted to an attribute name and value.
     *
     * @return string The XHTML for the attributes.
     */
    protected function _htmlAttribs(array $attribs)
    {
        $view = $this->getView();
        
        $xhtml = '';
        foreach ($attribs as $key => $val) {
            $key = $view->escape($key);
            
            if (is_array($val)) {
                $val = implode(' ', $val);
            } else if ($val === null) {
                continue;
            }
            
            $val = $view->escape($val);
            
            $xhtml .= " $key=\"$val\"";
        }

        return substr($xhtml, 1);
    }
}