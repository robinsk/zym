<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * Formats a date as the time since that date (e.g., “4 weeks ago”).
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_TimeSince extends Zym_View_Helper_Abstract
{
    /**
     * Time chunks in seconds => string format
     *
     * Order is FIFO largest to smallest
     *
     * @var array
     */
    protected $_dateChucks = array(
        31536000 => 'year',
        8748000  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
        1        => 'second'
    );

    /**
     * Formats a date as the time since that date (e.g., “4 weeks ago”).
     *
     * @param integer $timestamp
     * @param integer $time      Timestamp to use instead of time()
     */
    public function timeSince($timestamp, $time = null)
    {
        if ($time === null) {
            $time = time();
        }

        // Seconds since
        $since = $time - $timestamp;


        foreach ($this->_dateChucks as $seconds => $name) {
            if (!isset($largestChunk)) {
                $chunk = floor($since / $seconds);
            }

            if ($chunk != 0  && !isset($largestChunk)) {
                $largestChunk        = $chunk;
                $largestChunkName    = ($chunk == 1) ? $name: $name . 's';
                $largestChunkSeconds = $seconds;
            } else if (isset($largestChunk)) {
                $chunk = floor(($since - ($largestChunkSeconds * $largestChunk)) / $seconds);

                if ($chunk != 0) {
                    $secondChunk     = $chunk;
                    $secondChunkName = ($chunk == 1) ? $name : $name . 's';
                }

                break;
            }
        }

        $output     = '';
        $translator = $this->getView()->getHelper('translate');

        if (isset($secondChunk)) {
            $output = $translator->translate("%d $largestChunkName, %d $secondChunkName ago", $largestChunk, $secondChunk);
        } else {
            $output = $translator->translate("%d $largestChunkName ago", $largestChunk);
        }

        return $output;
    }
}