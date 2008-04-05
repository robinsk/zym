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
 * @author     Martin Hujer
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_FileSize
{
    /**
     * Size of one Kilobyte
     */
    const SIZE_KILOBYTES = 1024;
    
    /**
     * Size of one Megabyte
     */
    const SIZE_MEGABYTES = 1048576;
    
    /**
     * Size of one Gigabyte
     */
    const SIZE_GIGABYTES = 1073741824;
    
    /**
     * Size of one Terabyte
     */
    const SIZE_TERABYTES = 1099511627776;
    
    /**
     * Formats filesize with specified precision
     * 
     * @param integer $fileSize Filesize in bytes
     * @param integer $precision Precision
     */
    public function fileSize($fileSize, $precision = 3)
    {
        if ($fileSize >= self::SIZE_TERABYTES) {
            $newFilesize = $fileSize / self::SIZE_TERABYTES;
            $sizeName = 'TB';
        } else if ($fileSize >= self::SIZE_GIGABYTES) {
            $newFilesize = $fileSize / self::SIZE_GIGABYTES;
            $sizeName = 'GB';
        } else if ($fileSize >= self::SIZE_MEGABYTES) {
            $newFilesize = $fileSize / self::SIZE_MEGABYTES;
            $sizeName = 'MB';
        } else if ($fileSize >= self::SIZE_KILOBYTES) {
            $newFilesize = $fileSize / self::SIZE_KILOBYTES;
            $sizeName = 'KB';
        } else {
            $newFilesize = $fileSize;
            $sizeName = 'B';
        }
        
        $newFilesize = round($newFilesize, $precision);
        return $newFilesize . ' ' . $sizeName;
    }
   
}
