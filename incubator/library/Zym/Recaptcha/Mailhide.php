<?php
class Zym_Recaptcha_Mailhide
{
    const SERVER = 'http://mailhide.recaptcha.net';

    protected $_pubKey;
    protected $_privKey;
    protected $_email;

    public function __construct($pubKey, $privKey, $email)
    {
        if (!function_exists('mcrypt_encrypt')) {
            throw new Zym_Recaptcha_Exception('Mcrypt module not installed.');
        }

        if (empty($pubKey) || empty($privKey))
        {
            throw new Zym_Recaptcha_Exception('No public and/or private key specified.');
        }

        $this->_pubKey = $pubKey;
        $this->_privKey = $privKey;
        $this->_email = $email;
    }

    /**
     * Ggets the reCAPTCHA Mailhide url for a given email, public key and private key
     */
    public function getUrl()
    {
        $key = pack('H*', $this->_privKey);
        $encryptedMail = $this->_aesEncrypt($this->_email, $key);

        return self::SERVER . "d?k=" . $this->_pubKey . "&c=" . $this->_urlBase64($encryptedMail);
    }

    public function getEmail()
    {
        return $this->_email;
    }

    protected function _urlBase64($x)
    {
        return strtr(base64_encode($x), '+/', '-_');
    }

    protected function _aesPad($val)
    {
        $block_size = 16;
        $numpad = $block_size - (strlen ($val) % $block_size);
        return str_pad($val, strlen ($val) + $numpad, chr($numpad));
    }

    protected function _aesEncrypt($val, $ky)
    {
        $mode = MCRYPT_MODE_CBC;
        $enc = MCRYPT_RIJNDAEL_128;
        $val = $this->_aesPad($val);

        return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }
}