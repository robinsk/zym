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
     * Retrieve row field value
     *
     * @param  string $key The user-specified column name.
     * @return string The corresponding column value.
     */
    public function __get($key);
    
    /**
     * Set row field value
     *
     * @param  string $key The column key.
     * @param  mixed  $value The value for the property.
     * @return void
     */
    public function __set($key, $value);
}