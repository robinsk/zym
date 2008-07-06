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
 * @package    Zym_Search_Lucene
 * @subpackage Indexable
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Search_Lucene
 * @subpackage Indexable
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
interface Zym_Search_Lucene_IIndexable
{
    /**
     * Returns the unique identifier for the search index
     *
     * @return int|string
     */
    public function getRecordId();

    /**
     * Gets a complete search document used for indexing.
     *
     * @return Zend_Search_Lucene_Document
     */
    public function getSearchDocument();
}