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
 * A demo helper to demonstrate how to create module
 * specific helpers
 *
 * $this->demo() in view scripts
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Demo_View_Helper_Demo
{
    /**
     * A demo helper
     *
     * @return string
     */
    public function demo()
    {
        return 'I am a module demo helper';
    }
}