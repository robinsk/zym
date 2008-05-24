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
     * Array of units available
     * 
     * @var array
     */
    protected $_units;

    public function __construct()
    {
        require_once 'Zend/Measure/Binary.php';

        $m = new Zend_Measure_Binary(0);
        $this->_units = $units = $m->getConversionList();

    }

    /**
     * Formats filesize with specified precision
     *
     * @param integer $fileSize Filesize in bytes
     * @param integer $precision Precision
     * @param string $type Defined export type
     * @param boolean $iec Use SI units?
     */
    public function fileSize($fileSize, $precision = 0, $type = null, $iec = false)
    {

        $m = new Zend_Measure_Binary($fileSize);


        if ($type !== null) {
            $m->setType($type);
        } elseif ($iec === false) {
            if ($fileSize >= $this->_getUnitSize('TERABYTE')) {
                $m->setType(Zend_Measure_Binary::TERABYTE);
            } elseif ($fileSize >= $this->_getUnitSize('GIGABYTE')) {
                $m->setType(Zend_Measure_Binary::GIGABYTE);
            } elseif ($fileSize >= $this->_getUnitSize('MEGABYTE')) {
                $m->setType(Zend_Measure_Binary::MEGABYTE);
            } elseif ($fileSize >= $this->_getUnitSize('KILOBYTE')) {
                $m->setType(Zend_Measure_Binary::KILOBYTE);
            }
        } elseif ($iec === true) {
            if ($fileSize >= $this->_getUnitSize('TERABYTE_SI')) {
                $m->setType(Zend_Measure_Binary::TERABYTE_SI);
            } elseif ($fileSize >= $this->_getUnitSize('GIGABYTE_SI')) {
                $m->setType(Zend_Measure_Binary::GIGABYTE_SI);
            } elseif ($fileSize >= $this->_getUnitSize('MEGABYTE_SI')) {
                $m->setType(Zend_Measure_Binary::MEGABYTE_SI);
            } elseif ($fileSize >= $this->_getUnitSize('KILOBYTE_SI')) {
                $m->setType(Zend_Measure_Binary::KILOBYTE_SI);
            }
        }

        $value = $m->getValue($precision);
        $value = $this->_round($value, $precision);

        return $value . ' ' . $this->_getUnitAbr($m->getType());
    }

    /**
     * Round $number with set $precision
     * 
     * @param float $number Number to round
     * @param integer $precision Precision
     */
    protected function _round($number, $precision)
    {
        return Zend_Locale_Math::round($number, $precision);
    }

    /**
     * Get size of $unit in bytes
     * 
     * @param string $unit
     */
    protected function _getUnitSize($unit)
    {
        if (array_key_exists($unit, $this->_units)) {
            return $this->_units[$unit][0];
        }
        return 0;
    }

    /**
     * Get unit abbreviation of $unit
     * 
     * @param string $unit
     */
    protected function _getUnitAbr($unit)
    {
        if (array_key_exists($unit, $this->_units)) {
            return $this->_units[$unit][1];
        }
        return '';
    }

}