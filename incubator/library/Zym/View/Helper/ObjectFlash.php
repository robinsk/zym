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
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_View_Helper_Object
 */
require_once 'Zym/View/Helper/Object.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_ObjectFlash extends Zym_View_Helper_Object
{
    /**
     * Default file type for a flash applet
     * 
     */
    const TYPE = 'application/x-shockwave-flash';
    
    /**
     * Object classid
     *
     */
    const ATTRIB_CLASSID  = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
    
    /**
     * Object Codebase
     *
     */
    const ATTRIB_CODEBASE = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab';
    
    /**
     * Default attributes
     *
     * @var array
     */
    protected $_attribs = array('classid'  => self::ATTRIB_CLASSID,
                                'codebase' => self::ATTRIB_CODEBASE);
    /**
     * Output a flash movie object tag
     *
     * @param string $data The flash file
     * @param array  $attribs Attribs for the object tag
     * @param array  $params Params for in the object tag
     * @param string $content Alternative content
     * @return string
     */
    public function objectFlash($data, array $attribs = array(), array $params = array(), $content = null)
    {
        // Attrs
        $attribs = array_merge($this->_attribs, $attribs);
        
        // Params
        $params = array_merge(array(
            'movie' => $data
        ), $params);

        return $this->object(null, self::TYPE, $attribs, $params, $content);
    }
}