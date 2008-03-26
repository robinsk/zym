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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Search_Lucene_Index_Term
 */
require_once 'Zend/Search/Lucene/Index/Term.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Search_Lucene
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Search_Lucene_Index
{
    /**
     * Record ID key
     *
     * @var string
     */
	protected $_recordId = 'ZSLPKID';

    /**
     * The search index
     *
     * @var Zend_Search_Lucene_Interface
     */
    protected $_searchIndex = null;

    /**
     * Construct the indexer
     *
     * @param Zend_Search_Lucene_Interface $index
     */
    public function __construct(Zend_Search_Lucene_Interface $searchIndex)
    {
    	$this->_searchIndex = $searchIndex;
    }

    /**
     * Get the search index
     *
     * @return Zend_Search_Lucene_Interface
     */
    public function getSearchIndex()
    {
        return $this->_searchIndex;
    }

    /**
     * Remove a record from the search index
     *
     * @param string $value
     * @param string $searchField
     * @return Zym_Search_Lucene_Index
     */
    public function delete($id, $searchField = null)
    {
        if (!$searchField) {
            $searchField = $this->_recordId;
        }

		$docIds = $this->getDocumentIDsByID($id, $searchField);

		foreach ($docIds as $id) {
		    $this->_searchIndex->delete($id);
		}

		return $this;
    }

    /**
     * Get the lucene doc ids by a search
     *
     * @param string $id
     * @param string $searchField
     * @return array
     */
    public function getDocumentIDsByID($id, $searchField = null)
    {
        if (!$searchField) {
            $searchField = $this->_recordId;
        }

        $term = new Zend_Search_Lucene_Index_Term($id, $searchField);
        $docIds = $this->_searchIndex->termDocs($term);

        return $docIds;
    }

    /**
     * Index an Zym_Search_Lucene_Indexable_Interface
     *
     * @throws Zym_Search_Lucene_Exception
     * @param Zym_Search_Lucene_Indexable_Interface|array $indexables
     * @param boolean $update
     * @param string $searchField
     * @return Zym_Search_Lucene_Index
     */
	public function index($indexables, $update = true, $searchField = null)
	{
	    $indexables = (array) $indexables;

	    foreach ($indexables as $indexable) {
	        if (!$indexable instanceof Zym_Search_Lucene_Indexable_Interface) {
	            $this->_throwException('The object needs to have Zym_Search_Lucene_Indexable_Interface implemented.');
	        }

	    	if (!$searchField) {
                $searchField = $this->_recordId;
            }

            if ($update) {
                $recordId = $indexable->getRecordID();

                if (!$recordId) {
                    $this->_throwException('Record ID can\'t be null');
                }

                $this->delete($recordId, $searchField);
            }

            $document = $indexable->getSearchDocument();

            if (!$document instanceof Zend_Search_Lucene_Document) {
                $this->_throwException('The document is not an instance of Zend_Search_Lucene_Document, so it can\'t be indexed.');
            }

            $this->_searchIndex->addDocument($document);
	    }

		return $this;
	}

	/**
	 * Execute the query
	 *
	 * @param Zym_Search_Lucene_Query_Interface $query
	 * @param int $resultSetLimit
	 * @return array
	 */
    public function search(Zym_Search_Lucene_Query_Interface $query, $resultSetLimit = 0)
    {
        Zend_Search_Lucene::setResultSetLimit((int) $resultSetLimit);

        return $this->_searchIndex->find($query->toString());
    }

    /**
     * Throw the exception
     *
     * @param string $message
     * @throws Zym_Search_Lucene_Exception
     */
    protected function _throwException($message)
    {
        /**
         * @see Zym_Search_Lucene_Exception
         */
        require_once 'Zym/Search/Lucene/Exception.php';

        throw new Zym_Search_Lucene_Exception($message);
    }
}