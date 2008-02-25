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
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_Search
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Search_Lucene_Index
{
    /**
     * Record ID key
     *
     */
	const RECORDID = 'ZSLPKID';

	/**
	 * Record ID cant be null exception
	 *
	 */
	const RECORD_ID_CANT_BE_NULL_EXCEPTION = 'Record ID can\'t be null';

	/**
	 * Exception
	 *
	 */
	const DOCUMENT_NOT_ZSLD_EXCEPTION = 'The document is not an instance of Zend_Search_Lucene_Document, so it can\'t be indexed.';

	/**
	 * Default resultset limit
	 *
	 */
	const RESULTSET_LIMIT = 1000;

    /**
     * The search index
     *
     * @var Zend_Search_Lucene_Interface
     */
    protected $_searchIndex;

    /**
     * A collection of Indexables for batch processing
     *
     * @var array
     */
    protected $_indexables = array();

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
    public function delete($id, $searchField = self::RECORDID)
    {
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
    public function getDocumentIDsByID($id, $searchField = self::RECORDID)
    {
        $term = new Zend_Search_Lucene_Index_Term($id, $searchField);
        $docIds = $this->_searchIndex->termDocs($term);

        return $docIds;
    }

    /**
     * Index an Zym_Search_Lucene_Indexable_Interface
     *
     * @throws Zym_Search_Lucene_Exception
     * @param Zym_Search_Lucene_Indexable_Interface $indexable
     * @param boolean $update
     * @param string $searchField
     * @return Zym_Search_Lucene_Index
     */
	public function index(Zym_Search_Lucene_Indexable_Interface $indexable, $update = true, $searchField = self::RECORDID)
	{
		if ($update) {
			$recordId = $indexable->getRecordID();

			if (!$recordId) {
			    $this->_throwException(self::RECORD_ID_CANT_BE_NULL_EXCEPTION);
			}

			$this->delete($recordId, $searchField);
		}

		$document = $indexable->getSearchDocument();

		if (!$document instanceof Zend_Search_Lucene_Document) {
			$this->_throwException(self::DOCUMENT_NOT_ZSLD_EXCEPTION);
		}

		$this->_searchIndex->addDocument($document);

		return $this;
	}

	/**
	 * Add an indexable to the list
	 *
	 * @param Zym_Search_Lucene_Indexable_Interface $indexable
	 * @return Zym_Search_Lucene_Index
	 */
	public function addIndexable(Zym_Search_Lucene_Indexable_Interface $indexable)
	{
		$this->_indexables[] = $indexable;

		return $this;
	}

	/**
	 * Process the list of indexables
	 *
	 * @param boolean $update
	 * @return Zym_Search_Lucene_Index
	 */
	public function processBatch($update = true)
	{
		foreach ($this->_indexables as $indexable) {
			$this->index($indexable, $update);
		}

		return $this;
	}

	/**
	 * Execute the query
	 *
	 * @param Zym_Search_Lucene_Query_Interface $query
	 * @param int $resultSetLimit
	 * @param Zend_Cache_Core $cache
	 * @return array
	 */
    public function search(Zym_Search_Lucene_Query_Interface $query, $resultSetLimit = self::RESULTSET_LIMIT, Zend_Cache_Core $cache = null)
    {
        if ($resultSetLimit > 0) {
            Zend_Search_Lucene::setResultSetLimit($resultSetLimit);
        }

        $results = null;
        $queryString = $query->toString();

        $cacheName = md5($queryString);

        if ($cache != null) {
            $results = $cache->load($cacheName);
        }

        if (empty($results)) {
            $results = $this->_searchIndex->find($queryString);
        }

        if ($cache != null && !empty($results)) {
            $cache->clean(Zend_Cache::CLEANING_MODE_OLD);
            $cache->save($results);
        }

        return $results;
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