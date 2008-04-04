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
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Object
{
    /**
     * Output an object set
     *
     * @param string $data The data file
     * @param string $type Data file type
     * @param array $attribs Attribs for the object tag
     * @param array $params Params for in the object tag
     * @return string
     */
    public function object($data, $type, array $attribs = array(), array $params = array())
    {
        $xhtml = '<object data="' . $data . '" type="' . $type . '"';

        foreach ($attribs as $attrib => $value) {
        	$xhtml .= ' ' . $attrib . '="' . $value . '"';
        }

        $xhtml .= '>';

        foreach ($params as $param => $options) {
        	$xhtml .= '<param name="' . $param . '"';

        	if (is_string($options)) {
        	    $options = array('value' => $options);
        	}

        	foreach ($options as $key => $value) {
        		$xhtml .= ' ' . $key . '="' . $value . '"';
        	}

        	$xhtml .= ' />';
        }

        $xhtml .= '</object>';

        return $xhtml;
    }
}