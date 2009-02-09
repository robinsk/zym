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
class Zym_Svn_Client_Status
{
    /**
     * Properties
     *
     * @var array
     */
    private $_properties = array(
        'path',
        'textStatus',
        'repositoryTextStatus',
        'propertyStatus',
        'repositoryPropertyStatus',
        'locked',
        'copied',
        'switched',
        
        'versioned',
        
        'name',
        'url',
        'repository',
        'revision',
        'type',
        'scheduledAction',
        'deleted',
        'absent',
        'incomplete',
        'commitTimestamp',
        'commitRevision',
        'commitAuthor',
        'propertyUpdateTimestamp',
        'textUpdateTimestamp'
    );
    
    /**
     * Item path
     *
     * @var string
     */
    private $_path;
    
    
    /**
     * Construct
     *
     * @param array $props
     */
    public function __construct(array $props)
    {
        $this->fromArray($props);
    }
    
    /**
     * Setup object from array
     *
     * @return Zym_Svn_Client_List
     */
    public function fromArray(array $values)
    {   
        foreach ($this->_properties as $prop) {
            if (isset($values[$prop])) {
                $this->{'_' . $prop};
            }
        }
        
        return $this;
    }
    
    /**
     * Return array
     *
     * @return array
     */
    public function toArray()
    {
        $return = array();
        foreach ($this->_properties as $prop) {
            $return[$prop] = $this->{'get' . $prop}();
        }
        
        return $return;
    }
}