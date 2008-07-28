<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian_Crowd_Entity
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Service_Atlassian_Crowd_Entity
 */
require_once 'Zym/Service/Atlassian/Crowd/Entity.php';

/**
 * Zym Service Atlassian Crowd Entity Principal
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Atlassian_Crowd_Entity
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Atlassian_Crowd_Entity_Principal extends Zym_Service_Atlassian_Crowd_Entity
{
    /**
     * Construct
     *
     * @param string $name
     */
    public function __construct($name = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
    }
}