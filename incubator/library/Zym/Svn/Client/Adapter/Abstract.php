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
 * @package    Zym_Svn_Client
 * @subpackage Adapter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * PHP Subversion Client Abstract Adapter
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Svn_Client
 * @subpackage Adapter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
abstract class Zym_Svn_Client_Adapter_Abstract
{    
    /**
     * Schedules the addition of an item in a working directory
     *
     * @param string  $path
     * @param boolean $recursive
     * @param boolean $force
     */
    abstract public function add($path, $recursive = true, $force = false);
    
    /**
     * Returns the contents of a file in a repository
     *
     * @param string  $target
     * @param integer $revision
     * @return string
     */
    abstract public function getContents($target, $revision = null);
    
    /**
     * Returns the blame annotation
     *
     * @param string  $target
     * @param integer $revision
     * @return array  Array of Zym_Svn_Client_Blame
     */
    abstract public function getBlame($target, $revision = null);
    
    /**
     * Checks out a working copy from the repository
     *
     * @param string $repo
     * @param string $targetPath
     * @param integer $revision
     * @param integer $flags
     */
    abstract public function checkout($repo, $targetPath, $revision = null, $flags = null);
    
    /**
     * Recursively cleanup a working copy directory, finishing incomplete operations and removing locks
     *
     * @param string $workingDir
     */
    abstract public function cleanup($workingDir);
    
    /**
     * Sends changes from the local working copy to the repository
     *
     * @param string $log
     * @param array $targets
     */
    abstract public function commit($log, array $targets = array());
    
    /**
     * Export the contents of a SVN directory
     *
     * @param string $fromPath
     * @param string $toPath
     * @param integer $revision
     * @param integer $flags
     */
    abstract public function export($fromPath, $toPath, $revision = null, $flags = null);
    
    /**
     * Imports an unversioned path into a repository
     *
     * @param string $path
     * @param string $url
     */
    abstract public function import($path, $url);
    
    /**
     * Returns the commit log messages of a repository URL
     *
     * @param string  $url
     * @param integer $startRevision
     * @param integer $endRevision
     * @param integer $limit
     * @param integer $flags
     * @return array Array of Zym_Svn_Client_Log
     */
    abstract public function getLog($url, $startRevision = null, $endRevision = null, $limit = null, $flags = null);
    
    /**
     * Returns list of directory contents in repository URL
     *
     * @param string  $path
     * @param integer $revision
     * @return array Array of Zym_Svn_Client_List
     */
    abstract public function getList($path, $revision = null);
    
    /**
     * Creates a directory in a working copy or repository
     *
     * @param string $path
     */
    abstract public function createDirectory($path);
    
    /**
     * Revert changes to the working copy
     *
     * @param string  $path
     * @param boolean $recursive
     */
    abstract public function revert($path, $recursive = null);
    
    /**
     * Delete a file or directory
     *
     * @param string $path
     */
    abstract public function delete($path);
    
    /**
     * Update working copy
     *
     * @param string $path
     * @param integer $revision
     */
    abstract public function update($path, $revision = null);
    
    /**
     * Remove property
     *
     * @param string $path
     * @param string $propName
     */
    abstract public function deleteProperty($path, $propName);
    
    /**
     * Get property
     *
     * @param string  $path
     * @param string  $propName
     * @param integer $revision
     */
    abstract public function getProperty($path, $propName, $revision = null);
    
    /**
     * Set property
     *
     * @param string $path
     * @param string $propName
     * @param mixed  $propVal
     * @return Zym_Svn_Client_Abstract
     */
    abstract public function setProperty($path, $propName, $propVal);
    
    /**
     * Get property list
     *
     * @param string  $path
     * @param integer $revision
     * @return array
     */
    abstract public function getPropertyList($path, $revision = null);
    
    /**
     * Set properties
     *
     * @param string $path
     * @param array  $props 
     */
    abstract public function setProperties($path, array $props);
    
    /**
     * Get properties
     *
     * @param string  $path
     * @param integer $revision
     */
    abstract public function getProperties($path, $revision = null);
    
    /**
     * Get repository version
     *
     * @return integer
     */
    abstract public function getVersion();
}