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
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Model_IModel
 */
require_once 'Zym/Model/IModel.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Model
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
interface Zym_Model_IAttributeModel extends Zym_Model_IModel
{
    /**
     * Get an attribute
     *
     * @param  string $key The user-specified attribute name.
     * @return string The corresponding attribute value.
     */
    public function __get($key);
    
    /**
     * Set an attribute
     *
     * @param  string $key The attribute key.
     * @param  mixed  $value The value for the attribute.
     * @return void
     */
    public function __set($key, $value);
    
    /**
     * Checks if the attribute is set
     *
     * @param  string $key The user-specified attribute name.
     * @return bool
     */
    public function __isset($key);
    
    /**
     * Unsets the attribute
     *
     * @param  string $key The user-specified attribute name.
     */
    public function __unset($key);
}