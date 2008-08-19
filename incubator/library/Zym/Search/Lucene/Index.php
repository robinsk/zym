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
 * @see Zend_Paginator
 */
require_once 'Zend/Paginator.php';

/**
 * @see Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * @see Zend_Search_Lucene_Index_Term
 */
require_once 'Zend/Search/Lucene/Index/Term.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

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
     * Default index class
     *
     * @var string
     */
    protected static $_defaultIndexClass = 'Zym_Search_Lucene_Index';
    
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
     * Result cache
     *
     * @var Zend_Cache_Core
     */
    protected static $_resultCache = null;
    
    /**
     * Default record ID key
     *
     * @var string
     */
    public static $defaultIdKey = 'zsl_record_id';
    
    /**
     * Record ID key
     * 
     * @var string
     */
    protected $_idKey = null;

    /**
     * The search index
     *
     * @var Zend_Search_Lucene_Interface
     */
    protected $_searchIndex = null;
    
    /**
     * Set the default index class
     *
     * @param string $path
     */
    public static function setDefaultIndexClass($class)
    {
        self::$_defaultIndexClass = $class;
    }
    
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
     * Set the result cache
     *
     * @param Zend_Cache_Core $cache
     */
    public static function setResultCache(Zend_Cache_Core $cache)
    {
        self::$_resultCache = $cache;
    }
    
    /**
     * Get a Zend_Search_Lucene instance
     *
     * @param string $indexPath
     * @param array $params
     * @return Zend_Search_Lucene_Interface
     */
    public static function factory($indexPath = null, array $params = array())
    {
        $defaultParams = array('useDefaultPath'    => true,
                               'createIfNotExists' => true,
                               'indexClass'        => self::$_defaultIndexClass);
                               
        $params = array_merge($defaultParams, $params);
        
        $useDefaultPath    = $params['useDefaultPath'];
        $createIfNotExists = $params['createIfNotExists'];
        $indexClass        = $params['indexClass'];
        
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
            $indexPath = rtrim(self::$_defaultIndexPath, $trimMask)
                       . DIRECTORY_SEPARATOR . ltrim($indexPath, $trimMask);
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

        return new $indexClass($index);
    }
    
    /**
     * Construct the indexer
     *
     * @param Zend_Search_Lucene_Interface $index
     * @param string $idKey
     */
    public function __construct(Zend_Search_Lucene_Interface $searchIndex, $idKey = null)
    {
        if (!$idKey) {
            $idKey = self::$defaultIdKey;
        }
        
        $this->_searchIndex = $searchIndex;
        $this->_idKey = $idKey;
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
            $searchField = $this->_idKey;
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
            $searchField = $this->_idKey;
        }

        $term = new Zend_Search_Lucene_Index_Term($value, $searchField);
        $docIds = $this->_searchIndex->termDocs($term);

        return $docIds;
    }
    
    /**
     * Get the ID key
     */
    public function getIdKey()
    {
        return $this->_idKey;
    }
    
    /**
     * Set the ID key
     * 
     * @return Zym_Search_Lucene_Index
     */
    public function setIdKey($idKey)
    {
        $this->_idKey = $idKey;
        
        return $this;
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
            $searchField = $this->_idKey;
        }

        foreach ($indexables as $indexable) {
            if (!$indexable instanceof Zym_Search_Lucene_IIndexable) {
                /**
                 * @see Zym_Search_Lucene_Exception
                 */
                require_once 'Zym/Search/Lucene/Exception.php';
        
                throw new Zym_Search_Lucene_Exception('The object of type "' . get_class($indexable) . '" '
                                                    . 'is not an instance of Zym_Search_Lucene_Indexable_Interface.');
            }

            if ($update) {
                $recordId = $indexable->getRecordId();

                if (!$recordId) {
                    /**
                     * @see Zym_Search_Lucene_Exception
                     */
                    require_once 'Zym/Search/Lucene/Exception.php';
            
                    throw new Zym_Search_Lucene_Exception('You must provide a valid record ID.');
                }

                $this->delete($recordId, $searchField);
            }

            $document = $indexable->getSearchDocument();

            if (!$document instanceof Zend_Search_Lucene_Document) {
                /**
                 * @see Zym_Search_Lucene_Exception
                 */
                require_once 'Zym/Search/Lucene/Exception.php';
        
                throw new Zym_Search_Lucene_Exception('The provided search-document is not '
                                                    . 'an instance of Zend_Search_Lucene_Document.');
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
        // If the query is an instance of Zym_Search_Lucene_IQuery serialize it to a string
        $query = (string) $query;
        
        if (!$resultSetLimit) {
            $resultSetLimit = self::$_defaultResultSetLimit;
        }
        
        Zend_Search_Lucene::setResultSetLimit((int) $resultSetLimit);
        
        $cache = self::$_resultCache;
        
        if (null !== $cache) {
            $queryHash = $this->_getQueryHash($query);
            
            if (!($results = $cache->load($queryHash))) {
                $results = $this->_executeSearch($query, $resultSetLimit);
                
                $this->_cacheResults($results);
            }
        } else {
            $results = $this->_executeSearch($query, $resultSetLimit);
        }
        
        return $results;
    }
    
    /**
     * Execute the search and return the results
     *
     * @param string|Zym_Search_Lucene_IQuery $query
     * @return array
     */
    protected function _executeSearch($query)
    {
        return $this->_searchIndex->find((string) $query);
    }
    
    /**
     * Cache the search results
     *
     * @param Zend_Cache_Core $cache
     * @param array $results
     */
    protected function _cacheResults(Zend_Cache_Core $cache, $results)
    {
        $cache->clean(Zend_Cache::CLEANING_MODE_OLD);
        
        $cache->save($results);
    }
    
    /**
     * Get the query hash
     *
     * @param string|Zym_Search_Lucene_IQuery $query
     * @return string
     */
    protected function _getQueryHash($query)
    {
        return md5($query);
    }
}