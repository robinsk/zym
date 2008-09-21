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
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_ViewRow
{
    /**
     * Render the Zend_Db_Table_Row_Abstract in a table
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @param array $options
     * @return string
     */
    public function viewRow(Zend_Db_Table_Row_Abstract $row, array $options = array())
    {
        $table = $row->getTable();
        $rowData = $row->toArray();

        $ucFirst    = isset($options['ucfirst']) ? (bool) $options['ucfirst'] : true;
        $showEmpty  = isset($options['showEmpty']) ? (bool) $options['showEmpty'] : false;
        $tableClass = isset($options['class']) ? $options['class'] : 'ZVHViewRowTable';
        $columns    = isset($options['columns']) ? (array) $options['columns'] : null;
        
        $xhtml = '<table class="' . $tableClass . '">';

        if (isset($options['header'])) {
            $xhtml .= '<thead>';
            $xhtml .= '    <tr><td colspan="2">' . $options['header'] . '</td></tr>';
            $xhtml .= '</thead>';
        }

        $xhtml .= '<tbody>';

        foreach ($rowData as $key => $value) {
            if (!$table->isIdentity($key) &&
                ($columns == null || ($columns != null && in_array($key, $columns))) &&
                (!empty($value) || (empty($value) && $showEmpty))) {
                    $xhtml .= '<tr>';
                    $xhtml .= '    <td><strong>' . ($ucFirst ? ucfirst($key) : $key) . '</strong></td>';
                    $xhtml .= '    <td>' . $value . '</td>';
                    $xhtml .= '</tr>';
            }
        }

        $xhtml .= '</tbody></table>';

        return $xhtml;
    }
}