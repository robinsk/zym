<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zym_Search_Lucene_Index
 */
require_once 'Zym/Search/Lucene/Index.php';

/**
 * Search
 * 
 * @author Jurrien Stutterheim
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Search extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'search' => array(
                'defaults' => array(
                    'index_path' => 'data/indexes',
                    'resultset_limit' => 0
                ),
                /*
                'indexes' => array(
                    array(
                        'name' => '',
                        'use_default_path' => true,
                        'create' => true,
                        'id_key' => ''
                    ), ...
                ),
                */
            )
        )
    );

    /**
     * Setup search
     *
     */
    public function setup(Zend_Config $config)
    {
        // Get resource config
        $searchConfig = $config->search;
        
        $indexPath = $this->getApp()->getHome($searchConfig->defaults->index_path);
        
        Zym_Search_Lucene::setDefaultIndexPath($indexPath);
        Zym_Search_Lucene::setDefaultResultSetLimit($searchConfig->defaults->resultset_limit);
                
        foreach ($searchConfig->indexes as $index) {
            if (!isset($index->name)) {
                require_once 'Zym/App/Resource/Exception.php';
                
                throw new Zym_App_Resource_Exception('No index name set for the current index.');
            }
            
            $useDefaultPath = isset($index->use_default_path) ? $index->use_default_path : true;
            $createIfNotExists = isset($index->create) ? $index->create : true;
            
            $index = Zym_Search_Lucene::factory($index->name, $useDefaultPath, $createIfNotExists);
            
            if (isset($index->id_key)) {
                $index->setIdKey($index->id_key);
            }
        }
    }
}