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
class Zym_Svn_Client_Log_Path
{   
    /**
     * Modified status
     */
    const MODIFIED = 'M';
    
    /**
     * Added status
     */
    const ADDED    = 'A';
    
    /**
     * Deleted status
     */
    const DELETED  = 'D';
    
    /**
     * Replaced status
     */
    const REPLACED = 'R';
    
    /**
     * Properties
     *
     * @var array
     */
    private $_properties = array(
        'action',
        'path',
    );
    
    /**
     * Action (Leter signifying change)
     *
     * @var string
     */
    private $_action;
    
    /**
     * Absolute repository path of changed file
     *
     * @var string
     */
    private $_path = '';
    
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
     * Get letter signifying change of file/folder
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * Get rabsolute repository path of changed file/folder
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Setup object from array
     *
     * @return Zym_Svn_Client_Log_Path
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