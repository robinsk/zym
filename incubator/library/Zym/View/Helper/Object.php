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
 * @see Zym_View_Helper_Html_Abstract
 */
require_once 'Zym/View/Helper/Html/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Object extends Zym_View_Helper_Html_Abstract
{
    /**
     * Output an object set
     *
     * @param string $data The data file
     * @param string $type Data file type
     * @param array  $attribs Attribs for the object tag
     * @param array  $params Params for in the object tag
     * @param string $content Alternative content for object
     * @return string
     */
    public function object($data, $type, array $attribs = array(), array $params = array(), $content = null)
    {
        // Merge data and type
        $attribs = array_merge(array('data' => $data,
                                     'type' => $type), $attribs);

        // Params
        $paramHtml      = '';
        $closingBracket = $this->getClosingBracket();

        foreach ($params as $param => $options) {
            if (is_string($options)) {
                $options = array('value' => $options);
            }

            $options = array_merge($options,
                                   array('name' => $param));

            $paramHtml .= '<param ' . $this->_htmlAttribs($options) . $closingBracket;
        }

        // Content
        if (is_array($content)) {
            $content = implode(self::NEWLINE, $content);
        }

        // Object header
        $xhtml = '<object ' . $this->_htmlAttribs($attribs) . '>' . self::NEWLINE
                 . ($paramHtml ? $paramHtml . self::NEWLINE : '')
                 . ($content   ? $content . self::NEWLINE : '')
                 . '</object>';

        return $xhtml;
    }
}