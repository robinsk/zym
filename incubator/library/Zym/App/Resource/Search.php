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
                    'indexpath' => null,
                    'resultsetlimit' => 0
                ),
                /*
                'indexes' => array(
                    array(
                        'indexpath' => '',
                        'usedefaultpath' => true,
                        'create' => true,
                        'idkey' => ''
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
        
        Zym_Search_Lucene::setDefaultIndexPath($searchConfig->defaults->indexpath);
        Zym_Search_Lucene::setDefaultResultSetLimit($searchConfig->defaults->resultsetlimit);
                
        foreach ($searchConfig->indexes as $index) {
            if (!isset($index->indexpath)) {
                require_once 'Zym/App/Resource/Exception.php';
                
                throw new Zym_App_Resource_Exception('No index path set for the current index.');
            }
            
            $indexpath = $index->indexpath;
            
            $useDefaultPath = isset($index->usedefaultpath) ? $index->usedefaultpath : true;
            $createIfNotExists = isset($index->create) ? $index->create : true;
            
            $index = Zym_Search_Lucene::factory($index->indexpath, $useDefaultPath, $createIfNotExists);
            
            if (isset($index->idkey)) {
                $index->setIdKey($index->idKey);
            }
        }
    }
}