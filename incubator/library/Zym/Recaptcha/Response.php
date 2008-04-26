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
 * @package    Zym_Recaptcha
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Recaptcha
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Recaptcha_Response
{
    /**
     * Is the response valid?
     *
     * @var boolean
     */
    protected $_isValid;

    /**
     * Error message
     *
     * @var string
     */
    protected $_error;

    /**
     * Constructor
     *
     * @param boolean $isValid
     * @param string $error
     */
    public function __construct($isValid, $error = '')
    {
        $this->_isValid = $isValid;
        $this->_error = $error;
    }

    /**
     * Check if the response is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }
}