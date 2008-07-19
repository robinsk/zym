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
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_View_Helper_Abstract
 */
require_once 'Zym/View/Helper/Abstract.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Recaptcha extends Zym_View_Helper_Abstract
{
    /**
     * Gets the challenge HTML (javascript and non-javascript version).
     * This is called from the browser, and the resulting reCAPTCHA HTML widget
     * is embedded within the HTML form it was called from.
     * @param string $pubkey A public key for reCAPTCHA
     * @param string $error The error given by reCAPTCHA (optional, default is null)
     * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
     * @return string - The HTML to be embedded in the user's form.
     */
    public function recaptcha($pubkey, $error = null, $use_ssl = false)
    {
        if ($pubkey == null || $pubkey == '') {
            die ("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
        }

        if ($use_ssl) {
                    $server = RECAPTCHA_API_SECURE_SERVER;
            } else {
                    $server = RECAPTCHA_API_SERVER;
            }

            $errorpart = "";
            if ($error) {
               $errorpart = "&amp;error=" . $error;
            }
            return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

        <noscript>
            <iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
            <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
            <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
        </noscript>';
    }
}