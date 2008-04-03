<?php
/**
 * Zym Framework Demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Controller_Action_Abstract
 */
require_once 'Zym/Controller/Action/Abstract.php';

/**
 * @see Zym_Paginate_Array
 */
require_once 'Zym/Paginate/Array.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_PaginateController extends Zym_Controller_Action_Abstract 
{
    /**
     * Sample data array
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {}
    
    /**
     * Array paginate demo
     *
     * @return void
     */
    public function arrayAction()
    {
        // Contains our sample data
        $sampleData = array();
        
        // Populate sample data array
        for ($i = 0; $i < 123; $i++) {
            $sampleData[] = 'Item number ' . $i;
        }
        
        $limit = (int) $this->_getParam('limit');
        $page  = (int) $this->_getParam('page');
        
        $paginate = new Zym_Paginate_Array($sampleData);
        
        if ($limit > 0) {
            $paginate->setRowLimit($limit);
        }
        
        if ($page > 0 && $paginate->hasPageNumber($page)) {
            $paginate->setCurrentPageNumber($page);
        }
        
        // Pass view vars
        $this->getView()->assign(array(
            'paginate' => $paginate
        ));
    }
}