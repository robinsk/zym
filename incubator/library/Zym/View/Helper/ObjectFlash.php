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
     * @var string
     */
    protected $_type = 'application/x-shockwave-flash';

    /**
     * Output a flash movie object tag
     *
     * @param string $data The flash file
     * @param array $attribs Attribs for the object tag
     * @param array $params Params for in the object tag
     * @return string
     */
    public function objectFlash($data, array $attribs = array(), array $params = array())
    {
        $params['movie'] = $data;

        return $this->object($data, $this->_type, $attribs, $params);
    }
}