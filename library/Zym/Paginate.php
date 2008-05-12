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
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Paginate_Array
 */
require_once 'Zym/Paginate/Array.php';

/**
 * @see Zym_Paginate_DbSelect
 */
require_once 'Zym/Paginate/DbSelect.php';

/**
 * @see Zym_Paginate_Iterator
 */
require_once 'Zym/Paginate/Iterator.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Paginate
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Paginate
{
    /**
     * Factory
     *
     * @param Zend_Db_Select|Iterator|array $data
     * @return Zym_Paginate_Abstract
     * @throws Zym_Paginate_Exception
     */
    public static function factory($data)
    {
        $paginate = null;

        if ($data instanceof Zend_Db_Select) {
            $paginate = new Zym_Paginate_DbSelect($data);
        } else if ($data instanceof Iterator) {
            $paginate = new Zym_Paginate_Iterator($data);
        } else if (is_array($data)) {
            $paginate = new Zym_Paginate_Array($data);
        } else {
            $type = null;

            if (is_object($data)) {
                $type = get_class($data);
            } else {
                $type = gettype($data);
            }

            throw new Zym_Paginate_Exception('Can\'t paginate data of type "' . $type . '".');
        }

        return $paginate;
    }
}