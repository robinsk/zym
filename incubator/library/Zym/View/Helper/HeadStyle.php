<?php
class Zym_View_Helper_HeadStyle extends ArrayObject implements Countable
{
    /**
     * $_view
     *
     * @var Zend_View_Abstract
     */
    protected $_view;

    /**
     * $_scripts
     *
     * @var array
     */
    protected $_styles = array();

    /**
     * $_indent
     *
     * @var string
     */
    protected $_indent = null;

    /**
     * headStyle() - View Helper Method
     *
     * @return Zym_View_Helper_HeadStyle
     */
    public function headStyle()
    {
        return $this;
    }

    /**
     * append()
     *
     * @param array $attributes
     * @param string $content
     * @param int $index
     * @return Zym_View_Helper_HeadStyle
     */
    public function append($attributes, $content = null, $index = null)
    {
        $style = (array) $attributes;
        $style['_content'] = $content;

        if (!$index) {
            $index = $this->count();
        }

        $this->_styles[$index] = $style;
        krsort($this->_styles);
        return $this;
    }


    /**
     * appendStyle()
     *
     * @param string $script
     * @param string $type
     * @return Zym_View_Helper_HeadStyle
     */
    public function appendStyle($style, $type = 'text/css', $index = null)
    {
        $attrs = array(
            'type' => $type
            );

        if ($index === null || !is_int($index))
            $index = ($this->count()+1) * -1;

        $this->append($attrs, $style, $index);
        return $this;
    }

    /**
     * count() - Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_styles);
    }

    /**
     * setIndent()
     *
     * @param string $indent
     * @return Zym_View_Helper_HeadStyle
     */
    public function setIndent($indent)
    {
        $this->_indent = $indent;
        return $this;
    }

    /**
     * toString()
     *
     * @param string $indent
     * @return string
     */
    public function toString($indent = null)
    {
        if ($indent) {
            $this->_indent = $indent;
        }
        return $this->__toString();
    }

    /**
     * __toString()
     *
     * @return Zym_View_Helper_HeadStyle
     */
    public function __toString()
    {
        $return_string = null;

        foreach ($this->_styles as $style) {

            $return_string .= '<style ';

            $content = null;
            if (isset($style['_content'])) {
                $content = $style['_content'];
            }
            unset($style['_content']);

            foreach ($style as $name => $attr) {
                $return_string .= $name . '="' . $attr . '" ';
            }

            $return_string = rtrim($return_string) . '>';

            if ($content) {
                $return_string .= $content;
            }

            $return_string .= '</style>' . PHP_EOL . $this->_indent;

        }

        return (string) $return_string;
    }

    /**
     * setView() - used by View_Abstract to inject the current view object
     *
     * @param Zend_View_Abstract $view
     */
    public function setView($view) {
        $this->_view = $view;
    }

}