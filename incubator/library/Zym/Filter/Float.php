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
 * @package Zym_Filter
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * Converts values to floats
 *
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_Filter
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Filter_Float implements Zym_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns (float) $value
     *
     * @param  string $value
     * @return float
     */
    public function filter($value)
    {
        $locale = localeconv();

        $valueFiltered = str_replace($locale['decimal_point'], '.', (string) $value);
        $valueFiltered = str_replace($locale['thousands_sep'], '', $valueFiltered);

        return floatval($valueFiltered);
    }
}