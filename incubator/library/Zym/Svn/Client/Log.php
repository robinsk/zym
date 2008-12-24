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
 * @see Zym_Svn_Client_Log_Path
 */
require_once 'Zym/Svn/Client/Log/Path.php';

/**
 * PHP Subversion Client
 *
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym
 * @package   Zym_Svn_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Svn_Client_Log
{   
    /**
     * Properties
     *
     * @var array
     */
    private $_properties = array(
        'revision',
        'author',
        'message',
        'date',
        'paths'
    );
    
    /**
     * Log revision
     *
     * @var integer
     */
    private $_revision;
    
    /**
     * Revision author
     *
     * @var string
     */
    private $_author;
    
    /**
     * Log message
     *
     * @var string
     */
    private $_message;
    
    /**
     * Date of revision
     *
     * Format is 'M d H:i' or 'M d Y', depending on how old the file is
     *
     * @var string
     */
    private $_date;
    
    /**
     * Paths
     *
     * @var array Array of Zym_Svn_Client_Log_Path
     */
    private $_paths = array();
    
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
     * Get log revision
     * 
     * @return integer
     */
    public function getRevision()
    {
        return $this->_revision;
    }
    
    /**
     * Get revision author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->_author;
    }
    
    /**
     * Revision message
     *
     * @return integer
     */
    public function getMessage()
    {
        return $this->_message;
    }
    
    /**
     * Get date of revision
     *
     * Format is 'M d H:i' or 'M d Y', depending on how old the file is
     *
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }
    
    /**
     * Get paths
     *
     * @return array Array of Zym_Svn_Client_Log_Path
     */
    public function getPaths()
    {
        foreach ($this->_paths as $key => $path) {
            if ($path instanceof Zym_Svn_Client_Log_Path && $key == 0) {
                // Assume that if we encounter one instance, the rest is also the same
                break;
            }
            
            if (is_array($path)) {
                $this->_paths[$key] = new Zym_Svn_Client_Log_Path($path);
            }
        }
        
        return $this->_paths;
    }
    
    /**
     * Setup object from array
     *
     * @return Zym_Svn_Client_Log
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