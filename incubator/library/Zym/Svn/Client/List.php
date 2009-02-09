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
class Zym_Svn_Client_List
{
    /**
     * File
     */
    const TYPE_FILE      = 'file';
    
    /**
     * Directory
     */
    const TYPE_DIRECTORY = 'dir';
    
    /**
     * Properties
     *
     * @var array
     */
    private $_properties = array(
        'createdRevision',
        'lastAuthor',
        'size',
        'date',
        'timestamp',
        'name',
        'type'
    );
    
    /**
     * Created revision
     *
     * @var integer
     */
    private $_createdRevision;
    
    /**
     * Last author
     *
     * @var string
     */
    private $_lastAuthor;
    
    /**
     * File size
     *
     * @var integer
     */
    private $_size;
    
    /**
     * Date of last edit
     *
     * Format is 'M d H:i' or 'M d Y', depending on how old the file is
     *
     * @var string
     */
    private $_date;
    
    /**
     * Timestamp of last edit
     *
     * @var integer
     */
    private $_timestamp;
    
    /**
     * Item name
     *
     * @var string
     */
    private $_name;
    
    /**
     * Item type
     *
     * @var string
     */
    private $_type;
    
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
     * Get created revision
     * 
     * @return integer
     */
    public function getCreatedRevision()
    {
        return $this->_createdRevision;
    }
    
    /**
     * Get last edited by author
     *
     * @return string
     */
    public function getLastAuthor()
    {
        return $this->_lastAuthor;
    }
    
    /**
     * Get byte size of file
     *
     * Max of 2048MB
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->_size;
    }
    
    /**
     * Get date of last edit
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
     * Get timestamp since last edit
     *
     * @return integer
     */
    public function getTimestamp()
    {
        if ($this->_timestamp === null && ($date = $this->getDate())) {
            $timestamp        = strtotime($date);
            $this->_timestamp = $timestamp;
        }
        
        return $this->_timestamp;
    }
    
    /**
     * Get name of file/directory
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Get item type (file/dir)
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
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