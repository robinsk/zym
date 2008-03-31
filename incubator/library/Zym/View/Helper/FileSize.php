<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Martin Hujer
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author      Martin Hujer
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_FileSize
{
    private $_sizeKilobytes = 1024;
    
    private $_sizeMegabytes = 1048576;
    
    private $_sizeGigabytes = 1073741824;
    
    private $_sizeTerabytes = 1099511627776;
    
    /**
     * Formats filesize with specified precision
     * 
     * @param integer $filesize Filesize in bytes
     * @param integer $precision Precision
     */
    public function fileSize($filesize, $precision = 3)
    {
        if ($filesize >= $this->_sizeTerabytes) {
            $newFilesize = round(($filesize/$this->_sizeTerabytes), $precision);
            return $newFilesize . ' TB';
        } elseif ($filesize >= $this->_sizeGigabytes) {
            $newFilesize = round(($filesize/$this->_sizeGigabytes), $precision);
            return $newFilesize . ' GB';
        } elseif ($filesize >= $this->_sizeMegabytes) {
            $newFilesize = round(($filesize/$this->_sizeMegabytes), $precision);
            return $newFilesize . ' MB';
        } elseif ($filesize >= $this->_sizeKilobytes) {
            $newFilesize = round(($filesize/$this->_sizeKilobytes), $precision);
            return $newFilesize . ' KB';
        } else {
            return $filesize . ' B';
        }
    }
    
   
}
