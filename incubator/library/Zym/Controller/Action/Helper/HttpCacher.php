<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category    Zym
 * @package     Zym_Controller
 * @subpackage  Action_Helper
 * @copyright   Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license     http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Http cache headers helper
 *
 * Development notes: http://www.mnot.net/cache_docs/
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_HttpCacher extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Cache control heaer
     *
     */
    const CACHE_CONTROL = 'Cache-Control';

    /**
     * ETag header
     *
     */
    const ETAG          = 'ETag';

    /**
     * Expires header
     *
     */
    const EXPIRES       = 'Expires';

    /**
     * Last modified header
     *
     */
    const LAST_MODIFIED = 'Last-Modified';

    /**
     * Cache var for exitIfCached
     *
     * @var boolean
     */
    protected $_isCacheValid;

    /**
     * Http Cache
     *
     * @param integer $lastModifiedTimestamp
     * @param integer $expireSeconds
     * @param array $cacheControl
     * @return Zym_Controller_Action_Helper_HttpCacher
     */
    public function cache($lastModifiedTimestamp, $expireSeconds, array $cacheControl = array())
    {
        $this->_isCacheValid = false;

        $expire = $this->_getGmDate($expireSeconds);
        $index  = array_search('max-age', array_change_key_case($cacheControl, CASE_LOWER));

        if ($index !== false) {
            // Helper takes precedence
            unset($cacheControl[$index]);
        }

        $cacheControl['max-age'] = $expire;
        $cacheControlValue       = array();
        foreach ($cacheControl as $key => $value) {
            if (is_string($key)) {
                $cacheControlValue[] = "$key=$value";
            } else {
                $cacheControlValue[] = $value;
            }
        }

        $response = $this->getResponse();
        $response->setHeader(self::EXPIRES, $expire, true)
                 ->setHeader(self::CACHE_CONTROL, implode(', ', $cacheControlValue))
                 ->setHeader(self::LAST_MODIFIED, $expire)
                 ->setHeader(self::ETAG, '"'. md5($lastModifiedTimestamp) . '"');

        if ($this->_isCacheValid($lastModifiedTimestamp)) {
            $this->_sendNotModifiedStatus();
            $this->_isCacheValid = true;
        }

        return $this;
    }

    /**
     * Exit 304 if cache was valid
     *
     * Chain this with cache() to exit PHP execution instead of allowing it
     * to finish while the user is disconnected
     *
     */
    public function exitIfCached()
    {
        if ($this->_isCacheValid) {
            $this->_isCacheValid = false;
            exit();
        }
    }

    /**
     * Disable client-side caching
     *
     * @return Zym_Controller_Action_Helper_HttpCacher
     */
    public function setNoCache()
    {
        $response = $this->getResponse();
        $response->setHeader(self::EXPIRES, -1, true)
                 ->setHeader(self::CACHE_CONTROL, 'no-cache', true);

        return $this;
    }

    /**
     * Http Cache
     *
     * @param integer $lastModifiedTimestamp
     * @param integer $expireSeconds
     * @param array $cacheControl
     * @return Zym_Controller_Action_Helper_HttpCacher
     */
    public function direct($lastModifiedTimestamp, $expireSeconds, array $cacheControl = array())
    {
        return $this->cache($lastModifiedTimestamp, $expireSeconds, $cacheControl);
    }

    /**
     * Is the cache validation request valid
     *
     * @param integer $lastModifiedTimestamp
     * @return boolean
     */
    protected function _isCacheValid($lastModifiedTimestamp)
    {
        $request = $this->getRequest();
        $since   = $request->getServer('HTTP_IF_MODIFIED_SINCE');
        $eTag    = str_replace('"', '', stripslashes($request->getServer('HTTP_IF_NONE_MATCH')));
        $resultA = false;
        $resultB = false;

        if ($since && $this->_getGmDate($lastModifiedTimestamp) == preg_replace('/;.*$/', '', $since)) {
            $resultA = true;
        }

        if ($eTag && $eTag == md5($lastModifiedTimestamp)) {
            $resultB = true;
        }

        // If both headers are provided, both must be valid, else validate one
        return ($since && $eTag) ? ($resultA && $resultB) : ($resultA || $resultB);
    }

    /**
     * Send 304 Not Modified and kill the client connection
     *
     * @return void
     */
    protected function _sendNotModifiedStatus()
    {
        $this->getResponse()
             ->setHttpResponseCode(304)
             ->setRawHeader('Status: 304 Not Modified')
             ->setHeader('Content-Length', 0)
             ->setHeader('Connection', 'close')
             ->sendHeaders();

        for ($i = 0; $i <= ob_get_level(); $i++) {
            ob_flush();
        }

        flush();
    }

    /**
     * Formats a date in GMT syntax suitable for HTTP headers
     *
     * @param integer $date
     * @return string
     */
    protected function _getGmDate($date)
    {
        return gmdate('D, d M Y H:i:s', $date) . ' GMT';
    }
}