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
 * File helper
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Controller_Action_Helper_File extends Zend_Controller_Action_Helper_Abstract
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
        
        $response->sendHeaders();
        
        set_time_limit(0);
        
        echo $content;
        
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
        
        if ($this->isXSendFile()) {
            $response->setHeader('X-SendFile', $file, true);
        } else {
            $request  = $this->getRequest();
            $filesize = filesize($file);
            $time     = date('r', filemtime($file));
            $begin    = 0;
            $end      = $filesize;
            
            if ($httpRange = $request->getServer('HTTP_RANGE')) {
                if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $httpRange, $matches)) {
                    $begin = (int) $matches[0];
                    $end   = (!empty($matches[1])) ? (int) $matches[1] : $filesize;
                    
                    if ($begin > 0 || $end < $filesize) {
                        // Partial Content
                        $response->setHttpResponseCode(206);
                    }
                }
            }
            
            $response->setHeader('Content-Length', $end - $begin, true)
                     ->setHeader(sprintf('Content-Range: bytes %s/%s', $begin - $end, $filesize), true)
                     ->setHeader('Last-Modified', $time, true)
                     ->setHeader('Accept-Ranges', 'bytes', true)
                     ->setHeader('Connection', 'close', true);
                     
            $response->sendHeaders();
            
            set_time_limit(0);
            
            if ($response->getHttpResponseCode() == 206) {
                // Handle partial response
                $pos = $begin;
                fseek($fp, $begin, 0);

                while(! feof($fp) && $pos < $end && (connection_status() == 0)) { 
                    print fread($fp, min(1024 * 16, $end - $pos));
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
     * @param boolean $flag
     * @return Zym_Controller_Action_Helper_File
     */
    public function useXSendFile($flag = true)
    {
        $this->_useXSendFile = $flag;
        return $this;
    }
    
    /**
     * Whether to use x-sendfile
     *
     * @return boolean
     */
    public function isXSendFile()
    {
        return $this->_useXSendFile;
    }
}