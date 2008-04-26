<?php
class Zym_Recaptcha_Response
{
    protected $_isValid;
    protected $_error;

    public function __construct($isValid = false, $error = '')
    {
        $this->_isValid = $isValid;
        $this->_error = $error;
    }

    public function setIsValid($isValid = true)
    {
        $this->_isValid = $isValid;

        return $this;
    }

    public function isValid()
    {
        return $this->_isValid;
    }

    public function setError($error)
    {
        $this->_error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->_error;
    }
}