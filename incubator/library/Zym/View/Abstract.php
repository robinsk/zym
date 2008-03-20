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
 * @package Zym_View
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_View_Abstract
 */
require_once 'Zend/View/Abstract.php';

/**
 * @see Zym_View_Stream_Wrapper
 */
require_once 'Zym/View/Stream/Wrapper.php';

/**
 * View component
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_View
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_View_Abstract extends Zend_View_Abstract 
{
    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        $previousWrapperExists = false;
        
        // Unregister existing wrapper
        if (in_array('view', stream_get_wrappers())) {
            stream_wrapper_unregister('view');
            $previousWrapperExists = true;
        }
        
        // Register wrapper
        stream_wrapper_register('view', 'Zym_View_Stream_Wrapper');
        $return = parent::render($name);
        
        // Register any old wrapper
        if ($previousWrapperExists) {
            stream_wrapper_restore('view');
        }
        
        return $return;
    }
}