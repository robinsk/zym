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
interface Zym_Model_IReadOnlyModel extends Zym_Model_IModel
{
    /**
     * Test the read-only status of the model.
     *
     * @return boolean
     */
    public function isReadOnly();

    /**
     * Set the read-only status of the model.
     *
     * @param boolean $flag
     * @return boolean
     */
    public function setReadOnly($flag);
}