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
 * @see Zym_Svn_Client_Adapter_Abstract
 */
require_once 'Zym/Svn/Client/Adapter/Abstract.php';

/**
 * PHP Subversion Client Abstract Adapter
 *
 * @author    Geoffrey Tran
 * @license   http://www.zym-project.com/license New BSD License
 * @category  Zym
 * @package   Zym_Svn_Client
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Svn_Client_Adapter_Svn extends Zym_Svn_Client_Adapter_Abstract
{
    /**
     * SVN Binary
     * 
     * @var string
     */
    const CLIENT = 'svn';
    
    /**
     * Schedules the addition of an item in a working directory
     *
     * @param string $path
     * @param boolean $recursive
     * @param boolean $force
     * @return array Added paths
     */
    public function add($path, $recursive = true, $force = false)
    {
        $command = self::CLIENT . ' ' . escapeshellarg($path);
        
        if ($recursive) {
            $command .= ' --recursive';
        }
        
        if ($force) {
            $command .= ' --force';
        }
        
        exec($command, $output, $return);
        
        if ($return !== 0) {
            /**
             * @see Zym_Svn_Client_Adapter_Exception
             */
            require_once 'Zym/Svn/Client/Adapter/Exception.php';
            throw new Zym_Svn_Client_Adapter_Exception('');
        }
        
        return $addedDirs;
    }
    
    /**
     * Returns the contens of a file in a repository
     *
     * @param string $repoUrl
     * @param integer $revision
     * @return string
     */
     public function cat($repoUrl, $revision = null)
     {
     }
    
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
     */
    abstract public function log($url, $startRevision = null, $endRevision = null, $limit = null, $flags = null);
    
    /**
     * Returns list of directory contents in repository URL
     *
     * @param string $path
     * @param integer $revision
     */
    abstract public function ls($path, $revision = null);
    
    /**
     * Creates a directory in a working copy or repository
     *
     * @param string $path
     */
    abstract public function mkdir($path);
    
    /**
     * Revert changes to the working copy
     *
     * @param string $path
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
     * Get repository version
     *
     * @return integer
     */
    abstract public function version();
}