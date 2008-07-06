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
     * Registry key prefix
     *
     */
    const REGISTRY_PREFIX = 'lucene://';
    
    /**
     * Default path for the search index.
     * Usefull when the application has just one search index.
     *
     * Only used if set.
     *
     * @var string
     */
    protected static $_defaultIndexPath = null;
    
    /**
     * Default resultset limit
     * 
     * @var int
     */
    protected static $_defaultResultSetLimit = 0;
    
    /**
     * Record ID key
     *
     * @var string
     */
    protected $_defaultIdKey = 'zsl_record_id';

    /**
     * The search index
     *
     * @var Zend_Search_Lucene_Interface
     */
    protected $_searchIndex = null;

    /**
     * Set the default index path
     *
     * @param string $path
     */
    public static function setDefaultIndexPath($path)
    {
        self::$_defaultIndexPath = $path;
    }
    
    /**
     * Set the default resultset limit
     * 
     * @param int $limit
     */
    public static function setDefaultResultSetLimit($limit)
    {
        self::$_defaultResultSetLimit = (int) $limit;
    }
    
    /**
     * Get a Zend_Search_Lucene instance
     *
     * @param string $indexPath
     * @return Zend_Search_Lucene_Interface
     */
    public static function factory($indexPath = null, $useDefaultPath = true, $createIfNotExists = true)
    {
        if (!$indexPath && !self::$_defaultIndexPath) {
            /**
             * @see Zym_Search_Lucene_Exception
             */
            require_once 'Zym/Search/Lucene/Exception.php';
    
            throw new Zym_Search_Lucene_Exception('No index path specified');
        }

        $trimMask = '/\\';

        rtrim($indexPath, $trimMask);

        if ($useDefaultPath) {
            $indexPath = rtrim(self::$_defaultIndexPath, $trimMask) . DIRECTORY_SEPARATOR . ltrim($indexPath, $trimMask);
        }

        $registryKey = self::REGISTRY_PREFIX . $indexPath;

        if (Zend_Registry::isRegistered($registryKey)) {
            $index = Zend_Registry::get($registryKey);
        } else {
            if (file_exists($indexPath)) {
                $index = Zend_Search_Lucene::open($indexPath);
            } else {
                if (!$createIfNotExists) {
                    /**
                     * @see Zym_Search_Lucene_Exception
                     */
                    require_once 'Zym/Search/Lucene/Exception.php';
            
                    throw new Zym_Search_Lucene_Exception('Index "' . $indexPath . '" does not exists');
                }

                $index = Zend_Search_Lucene::create($indexPath);
            }

            Zend_Registry::set($registryKey, $index);
        }

        return new Zym_Search_Lucene_Index($index);
    }
    
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
     * Remove a record from the search index
     *
     * @param string $value
     * @param string $searchField
     * @return Zym_Search_Lucene_Index
     */
    public function delete($value, $searchField = null)
    {
        if (!$searchField) {
            $searchField = $this->_defaultIdKey;
        }

        $documentIds = $this->getDocumentIds($value, $searchField);

        foreach ($documentIds as $id) {
            $this->_searchIndex->delete($id);
        }

        return $this;
    }

    /**
     * Get the Lucene document IDs by search the specified search field.
     * If no search field is specified, the default ID field is used.
     *
     * @param string $value
     * @param string $searchField
     * @return array
     */
    public function getDocumentIds($value, $searchField = null)
    {
        if (!$searchField) {
            $searchField = $this->_defaultIdKey;
        }

        $term = new Zend_Search_Lucene_Index_Term($value, $searchField);
        $docIds = $this->_searchIndex->termDocs($term);

        return $docIds;
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
        if (!is_array($indexables)) {
            $indexables = array($indexables);
        }
        
        if (!$searchField) {
            $searchField = $this->_defaultIdKey;
        }

        foreach ($indexables as $indexable) {
            if (!$indexable instanceof Zym_Search_Lucene_IIndexable) {
                /**
                 * @see Zym_Search_Lucene_Exception
                 */
                require_once 'Zym/Search/Lucene/Exception.php';
        
                throw new Zym_Search_Lucene_Exception('The object needs to have Zym_Search_Lucene_Indexable_Interface implemented.');
            }

            if ($update) {
                $recordId = $indexable->getRecordID();

                if (!$recordId) {
                    /**
                     * @see Zym_Search_Lucene_Exception
                     */
                    require_once 'Zym/Search/Lucene/Exception.php';
            
                    throw new Zym_Search_Lucene_Exception('The record ID must not be null');
                }

                $this->delete($recordId, $searchField);
            }

            $document = $indexable->getSearchDocument();

            if (!$document instanceof Zend_Search_Lucene_Document) {
                /**
                 * @see Zym_Search_Lucene_Exception
                 */
                require_once 'Zym/Search/Lucene/Exception.php';
        
                throw new Zym_Search_Lucene_Exception('The document is not an instance of Zend_Search_Lucene_Document.');
            }

            $this->_searchIndex->addDocument($document);
        }

        return $this;
    }

    /**
     * Execute the query
     *
     * @param string|Zym_Search_Lucene_IQuery $query
     * @param int $resultSetLimit
     * @return array
     */
    public function search($query, $resultSetLimit = null)
    {
        if (!$resultSetLimit) {
            $resultSetLimit = self::$_defaultResultSetLimit;
        }
        
        Zend_Search_Lucene::setResultSetLimit((int) $resultSetLimit);

        return $this->_searchIndex->find((string) $query);
    }
}