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
 * @category   Zym_Search
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_Search
 * @copyright  Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
interface Zym_Search_Lucene_Indexable_Interface
{
    /**
     * Returns the unique identifier for the search index
     *
     * @return int|string
     */
    public function getRecordID();

    /**
     * Gets a complete search document used for indexing.
     *
     * @return Zend_Search_Lucene_Document
     */
    public function getSearchDocument();
}