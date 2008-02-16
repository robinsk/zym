<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author Jurrien Stutterheim
 * @category Zym_Db
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @author Jurrien Stutterheim
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym_Db
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Check if the column is set as identity
     *
     * @param string $column
     * @return boolean
     */
    public function isIdentity($column)
    {
        if (!isset($this->_metadata[$column])) {
            return false;
        }

        return (bool) $this->_metadata[$column]['IDENTITY'];
    }
}