<?php
class Zym_View_Helper_ViewRow
{
    public function viewRow(Zend_Db_Table_Row_Abstract $row)
    {
        $tableInfo = $row->getTable()->info();
        $metaData = $tableInfo[Zend_Db_Table_Abstract::METADATA];

        $rowData = $row->toArray();

        $xhtml = '<table>';

        foreach ($rowData as $key => $value) {
            if (!(bool) $metaData[$key]['IDENTITY']) {
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