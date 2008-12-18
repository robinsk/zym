<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym
 * @package   Zym_Svn_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license   http://www.zym-project.com/license New BSD License
 */

/**
 * PHP Subversion Client
 *
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym
 * @package   Zym_Svn_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Svn_Client_Item
{
    const TYPE_FILE = 'file';
    const TYPE_DIRECTORY = 'dir'
    
    private $createdRevision;
    private $lastAuthor;
    private $size;
    private $date;
    private $timestamp;
    private $name;
    private $type;
    
    public function __construct()
    {
    }
    
    public function fromArray(array $values)
    {}
    
    public function toArray()
    {}
}