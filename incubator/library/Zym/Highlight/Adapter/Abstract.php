<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Highlight
 * @subpackage Adapter
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * Zym_Highlight_Adapter_Abstract
 * 
 * Base class for highlight adapters.
 *
 * @category   Zym
 * @package    Zym_Highlight
 * @subpackage Adapter
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Highlight_Adapter_Abstract
{
    /**
     * Contains supported categories, their types and the type descriptions
     * 
     * Example:
     * <code>
     * protected $_types = array(
     *     'Text' => array(
     *         'plain'      => 'Plain text',
     *         'postscript' => 'PostScript'),
     * 
     *     'XML' => array(
     *         'xml'    => 'XML',
     *         'xslt1'  => 'XSLT 1.0',
     *         'xslt2'  => 'XSLT 2.0',
     *         'xquery' => 'XQuery 1.0),
     * 
     *     'Compiled languages' => array(
     *         'c'      => 'C',
     *         'csharp' => 'C#.NET,
     *         'java'   => 'Java'),
     * 
     *     'Scripted languages' => array(
     *         'python' => 'Python',
     *         'perl'   => 'Perl',
     *         'shell'  => 'Shell script')
     * );
     * </code>
     *
     * @var array
     */
    protected $_types = array();
    
    /**
     * Default type to use when highlighting
     *
     * @var string
     */
    protected $_defaultType = 'default';
    
    /**
     * Creates an instance of the highlighter
     *
     * @param array $options
     * @throws Zym_Highligh_Exception on invalid option
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }
    
    /**
     * Sets highlighter options
     *
     * @param array $options
     * @throws Zym_Highligh_Exception on invalid option
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }
    
    /**
     * Sets the given option
     *
     * @param string $name
     * @param mixed $value
     * @throws Zym_Highligh_Exception on invalid option
     */
    public function setOption($name, $value)
    {
        switch (strtolower($name)) {
            case 'default_type':
            case 'defaulttype':
                $this->setDefaultType($value);
                break;
            default:
                require_once 'Zym/Highlight/Exception.php';
                $msg = "Invalid highlighter option '$name'";
                throw new Zym_Highlight_Exception($msg);
        }
    }
    
    /**
     * Highlights the given string
     *
     * @param string $str
     * @param string $type  [optional] if not given, defaultType should be used
     * @return array  highlighted string with each line as an element
     */
    abstract public function highlight($str, $type = null);
    
    /**
     * Returns suppported categories
     *
     * @return array
     */
    public function getCategories()
    {
        return array_keys($this->_types);
    }
    
    /**
     * Returns default type
     *
     * @return string
     */
    public function getDefaultType()
    {
        return $this->_defaultType;
    }
    
    /**
     * Returns supported types
     *
     * @param bool $categories  [optional] return types in categories,
     *                          default is true
     * @return array
     */
    public function getTypes($categories = true)
    {
        if ($categories) {
            return $this->_types;
        } else {
            $types = array();
            foreach ($this->_types as $categoryTypes) {
                foreach ($categoryTypes as $type) {
                    $types[] = $type;
                }
            }
            return $types;
        }
    }
    
    /**
     * Returns description for the given type
     *
     * @param string $type
     * @return string
     * @throws Zym_Highlight_Exception if type isn't supported
     */
    public function getTypeDescription($type)
    {
        $type = (string) $type;
        foreach ($this->_types as $categoryTypes) {
            if (array_key_exists($type, $categoryTypes)) {
                return $categoryTypes[$type];
            }
        }
        
        require_once 'Zym/Highlight/Exception.php';
        $msg = "Type '$type' is not supported in highlight adapter";
        throw new Zym_Highlight_Exception($msg);
    }
    
    /**
     * Checks if highlighter has the given $category
     *
     * @param string $category
     */
    public function hasCategory($category)
    {
        return array_key_exists((string) $category, $this->_types);
    }
    
    /**
     * Checks if the highlighter supports the given $type
     *
     * @param string $type
     */
    public function hasType($type)
    {
        $type = (string) $type;
        foreach ($this->_types as $categoryTypes) {
            if (array_key_exists($type, $categoryTypes)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Sets default type
     *
     * @param string $type
     */
    public function setDefaultType($type)
    {
        if (!$this->hasType($type)) {
            require_once 'Zym/Highlight/Exception.php';
            $msg = "Type '$type' is not supported in highlight adapter";
            throw new Zym_Highlight_Exception($msg);
        }
        
        $this->_defaultType = $type;
    }
}
