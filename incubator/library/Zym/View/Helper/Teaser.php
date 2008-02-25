<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com//License New BSD License
 */

/**
 * Teaser generator
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com//License New BSD License
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_Teaser
{
    /**
     * Generate teaser text
     *
     * @param string $passage
     * @param integer $length
     * @param string $continueIndicator
     */
    public function teaser($passage, $length = 2000, $continueIndicator = ' [...]')
    {
        if (strlen($passage) > $length) {
            $return = substr($passage, 0, (int) $length) . $continueIndicator;
        } else {
            $return = $passage;
        }

        return $return;
    }
}
