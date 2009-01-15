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
 * File Sending helper
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_FileSender extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Content-Disposition: inline
     */
    const CONTENT_DISPOSITION_INLINE     = 'inline';
    
    /**
     * Content-Disposition: attachment
     */
    const CONTENT_DISPOSITION_ATTACHMENT = 'attachment';
    
    /**
     * Use X-SendFile header
     *
     * @var boolean
     */
    protected static $_useXSendFile = false;

    /**
     * Send as file to browser
     *
     * @param string $content     Data to send
     * @param string $filename    File name to suggest
     * @param string $disposition Content Disposition type
     */
    public function send($content, $filename, $type = 'application/octet-stream', $disposition = self::CONTENT_DISPOSITION_ATTACHMENT)
    {
        $response = $this->getRequest();
        $response->setHeader('Content-Disposition', sprintf('%s, filename="%s"', $disposition, pathinfo($file, PATHINFO_BASENAME)), true)
                 ->setHeader('Content-Type', $type, true)
                 ->setHeader('Content-Length', strlen($content), true)
                 
        // Required for IE, otherwise Content-disposition is ignored 
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', false);
        }
        
        set_time_limit(0);
        $response->setBody($content);
        
        // Disable layout
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            $this->getActionController()->getHelper('Layout')->disableLayout();
        }
        
        // Disable ViewRenderer
        if (Zend_Controller_Action_HelperBroker::hasHelper('ViewRenderer')) {
            $this->getActionController()->getHelper('ViewRenderer')->setNoRender();
        }
    }

    /**
     * Send file to browser from file on the filesystem
     *
     * @param string $file        Path to file
     * @param string $type        MIME type
     * @param string $disposition Content disposition
     */
    public function sendFromFile($file, $type = 'application/octet-stream', $disposition = self::CONTENT_DISPOSITION_ATTACHMENT)
    {
        if (!file_exists($file)) {
            /**
             * @see Zym_Controller_Action_Helper_Exception
             */
            require_once 'Zym/Controller/Action/Helper/Exception.php';
            throw new Zym_Controller_Action_Helper_Exception(sprintf('File helper could not find file "%s"', $file));
        }
        
        $response = $this->getRequest();
        $response->setHeader('Content-Disposition', sprintf('%s, filename="%s"', $disposition, pathinfo($file, PATHINFO_BASENAME)), true)
                 ->setHeader('Content-Type', $type, true);
                 
        // Required for IE, otherwise Content-disposition is ignored 
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', false);
        }
        
        if (self::isXSendFile()) {
            $response->setHeader('X-SendFile', $file, true);
        } else {
            $request  = $this->getRequest();
            $filesize = filesize($file);
            $time     = date('r', filemtime($file));
            $begin    = 0;
            $end      = $filesize - 1;
            
            if ($httpRange = $request->getServer('HTTP_RANGE')) {
                if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $httpRange, $matches)) {
                    $begin = (int) $matches[1];
                    $end   = (!empty($matches[2])) ? (int) $matches[2] : $filesize;
                    
                    if ($begin > 0 || $end < ()$filesize - 1)) {
                        // Partial Content
                        $response->setHttpResponseCode(206);
                    }
                }
            }
            
            // Only set last-modified time if none have been set
            foreach ($response->getHeaders() as $key => $header) {
                if (strcasecmp($header['name'], 'Last-Modified')) {
                    $lastModifiedExists = true;
                }
            }
            
            if (!isset($lastModifiedExists)) {
                $response->setHeader('Last-Modified', $time);
            }

            // Normal file headers
            $response->setHeader('Content-Length', $end - $begin, true)
                     ->setHeader(sprintf('Content-Range: bytes %s/%s', $begin - $end, $filesize), true)
                     ->setHeader('Accept-Ranges', 'bytes', true)
                     ->setHeader('Connection', 'close', true);
                     
            $response->sendHeaders();
            
            set_time_limit(0);
            
            if ($response->getHttpResponseCode() == 206) {
                // Handle partial response
                $pos = $begin;
                $fp = fopen($file, 'rb');
                fseek($fp, $begin, 0);

                while(!feof($fp) && $pos < $end && (connection_status() == 0)) { 
                    print fread($fp, min(1024 * 16, ($end + 1) - $pos));
                    $pos += 1024 * 16;
                }
            } else {
                readfile($file);
            }
        }
        
        // Disable layout
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            $this->getActionController()->getHelper('Layout')->disableLayout();
        }
        
        // Disable ViewRenderer
        if (Zend_Controller_Action_HelperBroker::hasHelper('ViewRenderer')) {
            $this->getActionController()->getHelper('ViewRenderer')->setNoRender();
        }
    }
    
    /**
     * Use XSendFile header instead of processing files through PHP
     *
     * Must have a server that supports the XSendFile header else
     * you would only be getting a blank page.
     *
     * @param boolean $flag
     */
    public static function useXSendFile($flag = true)
    {
        self::$_useXSendFile = $flag;
    }
    
    /**
     * Whether to use x-sendfile
     *
     * @return boolean
     */
    public static function isXSendFile()
    {
        return self::$_useXSendFile;
    }
}