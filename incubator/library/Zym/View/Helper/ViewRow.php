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
     * @return string
     */
    public function viewRow(Zend_Db_Table_Row_Abstract $row, $header = null, $columns = null)
    {
        $table = $row->getTable();
        $rowData = $row->toArray();

        $xhtml = '<table class="ZVHViewRowTable">';

        if ($header) {
            $xhtml .= '<thead>';

            $xhtml .= sprintf('<tr><td colspan="2">%s</td></tr>', $header);

            $xhtml .= '</thead>';
        }

        $xhtml .= '<tbody>';

        foreach ($rowData as $key => $value) {
            if (!$table->isIdentity($key) && ($columns != null && in_array($key, $columns))) {
                $xhtml .= '<tr>';

                $xhtml .= sprintf('<td><strong>%s</strong></td>', $key);
                $xhtml .= sprintf('<td>%s</td>', $value);

                $xhtml .= '</tr>';
            }
        }

        $xhtml .= '</tbody></table>';

        return $xhtml;
    }
}