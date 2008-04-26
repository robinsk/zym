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
 * @see Zym_Recaptcha_Response
 */
require_once 'Zym/Recaptcha/Response.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Recaptcha
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Recaptcha
{
    const SERVER_API = 'http://api.recaptcha.net';
    const SERVER_API_SECURE = 'https://api-secure.recaptcha.net';
    const SERVER_VERIFY = 'api-verify.recaptcha.net';
    const CRLF = "\r\n";

    protected $_privKey;
    protected $_remoteIp;

    public function __construct($privKey, $remoteIp)
    {
        if (empty($privKey)) {
            throw new Zym_Recaptcha_Exception('No private key specified.');
        }

        if (empty($remoteIp)) {
            throw new Zym_Recaptcha_Exception('No remote IP specified.');
        }

        $this->_privKey = $privKey;
        $this->_remoteIp = $remoteIp;
    }

    /**
     * Encodes the given data into a query string format
     * @param $data - array of string elements to be encoded
     * @return string - encoded request
     */
    protected function _encodeQueryString($data)
    {
        $request = '';

        foreach ($data as $key => $value) {
            $request .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }

        $request = substr($request, 0, strlen($request) - 1);

        return $request;
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     * @return array response
     */
    protected function _httpPost($host, $path, $data, $port = 80)
    {
            $requestData = $this->_encodeQueryString($data);

            $httpRequest  = 'POST ' . $path . ' HTTP/1.0' . self::CRLF;
            $httpRequest .= 'Host: ' . $host . self::CRLF;
            $httpRequest .= 'Content-Type: application/x-www-form-urlencoded;' . self::CRLF;
            $httpRequest .= 'Content-Length: ' . strlen($requestData) . self::CLRF;
            $httpRequest .= 'User-Agent: reCAPTCHA/PHP' . self::CLRF;
            $httpRequest .= self::CRLF;
            $httpRequest .= $requestData;

            $response = '';

            $errno = null;
            $errstr = null;

            $socket = @fsockopen($host, $port, $errno, $errstr, 10);

            if (!$socket) {
                $message = sprintf('Could not open socket. Error %s: "%s."', $errno, $errstr);
                throw new Zym_Recaptcha_Exception($message);
            }

            fwrite($socket, $httpRequest);

            while (!feof($socket)) {
                $response .= fgets($socket, 1160);
            }

            fclose($socket);
            $response = explode(self::CRLF . self::CRLF, $response, 2);

            return $response;
    }

    /**
      * Calls an HTTP POST function to verify if the user's guess was correct
      *
      * @param string $challenge
      * @param string $response
      * @param array $params
      * @return Zym_Recaptcha_Response
      */
    public function checkAnswer($challenge, $response, $params = array())
    {
            if (empty($challenge) || empty($response)) {
                return new Zym_Recaptcha_Response(false, 'incorrect-captcha-sol');
            }

            $defaultParams = array('privatekey' => $this->_privkey,
                                   'remoteip'   => $this->_remoteip,
                                   'challenge'  => $challenge,
                                   'response'   => $response);

            $serverResponse = $this->_httpPost(self::SERVER_VERIFY, '/verify',
                                               array_merge($defaultParams, $params));

            $answers = explode ("\n", $serverResponse[1]);
            $response = new Zym_Recaptcha_Response();

            if (trim($answers[0]) == 'true') {
                $response->setIsValid();
            } else {
                $response->setError($answers[1]);
            }

            return $response;
    }

    /**
     * gets a URL where the user can sign up for reCAPTCHA. If your application
     * has a configuration page where you enter a key, you should provide a link
     * using this function.
     * @param string $domain The domain where the page is hosted
     * @param string $appname The name of your application
     */
    public function getSignupUrl($domain = null, $appname = null)
    {
        return self::SERVER_API . '/getkey?' . $this->_encodeQueryString(array('domain' => $domain,
                                                                               'app'    => $appname));
    }
}