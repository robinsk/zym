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
 * @package Zym_Timer
 * @subpackage Manager_Exception
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Timer_Manager_Exception
 */
require_once 'Zym/Timer/Manager/Exception.php';

/**
 * Timer exists exception
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Timer
 * @subpackage Manager_Exception
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Timer_Manager_Exception_TimerExists extends Zym_Timer_Manager_Exception
{
    /**
     * Construct
     *
     * @param string $name
     * @param string $group
     */
    public function __construct($name, $group = null)
    {
        parent::__construct(sprintf(
            'Cannot add timer because timer exists in group "%s" named "%s"', $group, $name
        ));
    }
}