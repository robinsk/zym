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
class Zym_View_Helper_Quicktime extends Zym_View_Helper_Object
{
    /**
     * Default file type for a flash applet
     *
     * @var string
     */
    protected $_type = 'video/quicktime';

    /**
     * Default attributes
     *
     * @var array
     */
    protected $_attribs = array('classid'  => 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
                                'codebase' => 'http://www.apple.com/qtactivex/qtplugin.cab');

    /**
     * Output a flash movie object tag
     *
     * @param string $data The flash file
     * @param array $attribs Attribs for the object tag
     * @param array $params Params for in the object tag
     * @return string
     */
    public function flash($data, array $attribs = array(), array $params = array())
    {
        $attribs = array_merge($this->_attribs, $attribs);

        return $this->object($data, $this->_type, $attribs, $params);
    }
}