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
class Zym_View_Helper_ObjectQuicktime extends Zym_View_Helper_Object
{
    /**
     * Default file type for a movie applet
     *
     */
    const TYPE = 'video/quicktime';

    /**
     * Object classid
     *
     */
    const ATTRIB_CLASSID  = 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B';
    
    /**
     * Object Codebase
     *
     */
    const ATTRIB_CODEBASE = 'http://www.apple.com/qtactivex/qtplugin.cab';
    
    /**
     * Default attributes
     *
     * @var array
     */
    protected $_attribs = array('classid'  => self::ATTRIB_CLASSID,
                                'codebase' => self::ATTRIB_CODEBASE);

    /**
     * Output a quicktime movie object tag
     *
     * @param string $data The quicktime file
     * @param array  $attribs Attribs for the object tag
     * @param array  $params Params for in the object tag
     * @param string $content Alternative content
     * @return string
     */
    public function objectQuicktime($data, array $attribs = array(), array $params = array(), $content = null)
    {
        // Attrs
        $attribs = array_merge($this->_attribs, $attribs);
        
        // Params
        $params = array_merge(array(
            'src' => $data
        ), $params);
        
        return $this->object(null, self::TYPE, $attribs, $params, $content);
    }
}