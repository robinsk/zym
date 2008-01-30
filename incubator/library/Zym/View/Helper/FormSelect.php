<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';

/**
 * @author     Jurri‘n Stutterheim
 * @category   Zym
 * @package    View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_View_Helper_FormSelect extends Zend_View_Helper_FormElement
{
    /**
     * Generates 'select' list of options.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The option value to mark as 'selected'; if an
     * array, will mark all values in the array as 'selected' (used for
     * multiple-select elements).
     *
     * @param array|string $attribs Attributes added to the 'select' tag.
     *
     * @param array $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     *
     * @param array An array with the name of items that need to be disabled.
     *
     * @param string $listsep When disabled, use this list separator string
     * between list values.
     *
     * @return string The select tag and options XHTML.
     */
    public function formSelect($name, $value = null, $attribs = null,
        $options = null, $disabled = null, $listsep = "<br />\n")
    {
        $id = '';
        $disable = false;

        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, id, value, attribs, options, listsep, disable

        // force $value to array so we can compare multiple values
        // to multiple options.
        settype($value, 'array');
        settype($disabled, 'array');

        $isArrayElement = (substr($name, -2) == '[]');

        // check for multiple attrib and change name if needed
        if (isset($attribs['multiple']) &&
            $attribs['multiple'] == 'multiple' &&
            !$isArrayElement) {
            $name .= '[]';
        }

        // check for multiple implied by the name and set attrib if
        // needed
        if ($isArrayElement) {
            $attribs['multiple'] = 'multiple';
        }

        // now start building the XHTML.
        if ($disable) {

            // disabled.
            // generate a plain list of selected options.
            // show the label, not the value, of the option.
            $list = array();
            foreach ((array) $options as $optValue => $optLabel) {
                if (in_array($optValue, $value, 0 === $optValue)) {
                    // add the hidden value
                    $option = $this->_hidden($name, $optValue);
                    // add the display label
                    $option .= $this->view->escape($optLabel);
                    // add to the list
                    $list[] = $option;
                }
            }
            $xhtml = implode($listsep, $list);

        } else {

            // enabled.
            // the surrounding select element first.
            $xhtml = '<select'
                   . sprintf(' name="%s"', $this->view->escape($name))
                   . sprintf(' id="%s"', $this->view->escape($id))
                   . $this->_htmlAttribs($attribs)
                   . ">\n\t";

            // build the list of options
            $list = array();
            foreach ((array) $options as $optValue => $optLabel) {

                if (is_array($optLabel)) {
                    $list[] = '<optgroup '
                            . sprintf('label="%s">',  $this->view->escape($optValue));
                    foreach ($optLabel as $val => $lab) {
                        $list[] = $this->_build($val, $lab, $value, $disabled);
                    }
                    $list[] = '</optgroup>';
                } else {
                    $list[] = $this->_build($optValue, $optLabel, $value, $disabled);
                }
            }

            // add the options to the xhtml and close the select
            $xhtml .= implode("\n\t", $list) . "\n</select>";

        }

        return $xhtml;
    }

    /**
     * Builds the actual <option> tag
     *
     * @param string $value Options Value
     * @param string $label Options Label
     * @param array  $selected The option value(s) to mark as 'selected'
     * @param array  $disabled The option value(s) to mark as 'disabled'
     * @return string Option Tag XHTML
     */
    protected function _build($value, $label, $selected, $disabled)
    {
        $option = '<option'
             . sprintf(' value="%s"', $this->view->escape($value))
             . sprintf(' label="%s"', $this->view->escape($label));

        // selected?
        if (in_array($value, $selected, 0 === $value)) {
            $option .= ' selected="selected"';
        }

        // disabled?
        if (in_array($value, $disabled, 0 === $value)) {
            $option .= ' disabled="disabled"';
        }

        $option .= sprintf('>%s</option>', $this->view->escape($label));

        return $option;
    }
}