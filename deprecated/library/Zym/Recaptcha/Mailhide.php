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
 * @see Zym_Recaptcha_Exception
 */
require_once 'Zym/Recaptcha/Exception.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Recaptcha
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Recaptcha_Mailhide
{
    /**
     * Mailhide server address
     *
     * @var string
     */
    const SERVER = 'http://mailhide.recaptcha.net';

    /**
     * Public key
     *
     * @var string
     */
    protected $_publicKey;

    /**
     * Private key
     *
     * @var string
     */
    protected $_privateKey;

    /**
     * Email address
     *
     * @var string
     */
    protected $_email;

    /**
     * Constructor
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param string $email
     */
    public function __construct($publicKey, $privateKey, $email)
    {
        if (!function_exists('mcrypt_encrypt')) {
            throw new Zym_Recaptcha_Exception('Mcrypt module not installed.');
        }

        if (empty($publicKey) || empty($privateKey))
        {
            throw new Zym_Recaptcha_Exception('No public and/or private key specified.');
        }

        $this->_publicKey = $publicKey;
        $this->_privateKey = $privateKey;
        $this->_email = $email;
    }

    /**
     * Gets the reCAPTCHA Mailhide url for a given email, public key and private key
     *
     * @return string
     */
    public function getUrl()
    {
        $key = pack('H*', $this->_privateKey);
        $encryptedMail = $this->_aesEncrypt($this->_email, $key);

        return self::SERVER . "d?k=" . $this->_publicKey . "&c=" . $this->_urlBase64($encryptedMail);
    }

    /**
     * Gets the email address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Encode in Base64 and replace +/
     *
     * @param string $string
     * @return string
     */
    protected function _urlBase64($string)
    {
        return strtr(base64_encode($string), '+/', '-_');
    }

    /**
     * AES Pad
     *
     * @param string $value
     * @return string
     */
    protected function _aesPad($value)
    {
        $blockSize = 16;
        $numpad = $blockSize - (strlen($value) % $blockSize);

        return str_pad($value, strlen($value) + $numpad, chr($numpad));
    }

    /**
     * AES Encrypt
     *
     * @param string $value
     * @param string $key
     * @return string
     */
    protected function _aesEncrypt($value, $key)
    {
        $mode = MCRYPT_MODE_CBC;
        $encoding = MCRYPT_RIJNDAEL_128;
        $value = $this->_aesPad($value);

        return mcrypt_encrypt($encoding, $key, $value, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }
}