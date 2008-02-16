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
 * @category   Zym_View
 * @package    Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_View
 * @package    Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_View_Helper_ViewRow
{
    /**
     * Render the Zend_Db_Table_Row_Abstract in a table
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return string
     */
    public function viewRow(Zend_Db_Table_Row_Abstract $row)
    {
        $table = $row->getTable();
        $rowData = $row->toArray();

        $xhtml = '<table>';

        foreach ($rowData as $key => $value) {
            if (!$table->isIdentity($key)) {
                $xhtml .= '<tr>';

                $xhtml .= sprintf('<td><strong>%s</strong></td>', $key);
                $xhtml .= sprintf('<td>%s</td>', $value);

                $xhtml .= '</tr>';
            }
        }

        $xhtml .= '</table>';

        return $xhtml;
    }
}