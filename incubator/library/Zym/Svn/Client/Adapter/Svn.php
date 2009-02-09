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
     * Path to SVN Binary
     *
     * @var string
     */
    protected $_client = self::CLIENT;
    
    /**
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        // Check if exec() is available
        if (!function_exists('exec')) {
            /**
             * @see Zym_Svn_Client_Adapter_Exception
             */
            require_once 'Zym/Svn/Client/Adapter/Exception.php';
            throw new Zym_Svn_Client_Adapter_Exception(sprintf('Adapter "%s" requires PHP function exec()', get_class($this)));
        }
    }
    
    /**
     * Get Svn client binary
     *
     * @return string
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Set svn client binary
     *
     * @param string $client
     * @return string
     */
    public function setClient($client)
    {
        $this->_client = (string) $client;
        
        return $this;
    }

    /**
     * Schedules the addition of an item in a working directory
     *
     * @param string  $path
     * @param boolean $recursive
     * @param boolean $force
     */
    public function add($path, $recursive = true, $force = false)
    {
        $args = array('add' => $path);

        if ($recursive) {
            $args['--recursive'] = null;
        }
        
        if ($force) {
            $args['--force'] = null;
        }
        
        $this->_execClient($args, $output, $return);
        if ($return !== 0) {
            /**
             * @see Zym_Svn_Client_Adapter_Exception
             */
            require_once 'Zym/Svn/Client/Adapter/Exception.php';
            throw new Zym_Svn_Client_Adapter_Exception(implode("\n", $output));
        }        
    }
    
    /**
     * Returns the contents of a file in a repository
     *
     * @param string  $target
     * @param integer $revision
     * @return string
     */
    public function getContents($target, $revision = null)
    {
        $args = array('cat' => $target);
        
        if ($revision) {
            $args['--revision'] = $revision;
        }

        $this->_execClient($args, $output, $return);
        if ($return !== 0) {
            /**
             * @see Zym_Svn_Client_Adapter_Exception
             */
            require_once 'Zym/Svn/Client/Adapter/Exception.php';
            throw new Zym_Svn_Client_Adapter_Exception(implode("\n", $output));
        }
        
        return implode("\n", $output);
    }
    
    /**
     * Returns the blame annotation
     *
     * @param string  $target
     * @param integer $revision
     * @return array  Array of Zym_Svn_Client_Blame
     */
    public function getBlame($target, $revision = null)
    {
        $args = array(
            'blame' => $target,
            '--xml' => null
        );

        if ($revision) {
            $args['--revision'] = $revision;
        }

        $this->_execClient($args, $output, $return);
        if ($return !== 0) {
            /**
             * @see Zym_Svn_Client_Adapter_Exception
             */
            require_once 'Zym/Svn/Client/Adapter/Exception.php';
            throw new Zym_Svn_Client_Adapter_Exception(implode("\n", $output));
        }

        $xml = implode("\n", $output);
    }
    
    /**
     * Checks out a working copy from the repository
     *
     * @param string $repo
     * @param string $targetPath
     * @param integer $revision
     * @param integer $flags
     */
    public function checkout($repo, $targetPath, $revision = null, $flags = null);
    
    /**
     * Recursively cleanup a working copy directory, finishing incomplete operations and removing locks
     *
     * @param string $workingDir
     */
    public function cleanup($workingDir);
    
    /**
     * Sends changes from the local working copy to the repository
     *
     * @param string $log
     * @param array $targets
     */
    public function commit($log, array $targets = array());
    
    /**
     * Export the contents of a SVN directory
     *
     * @param string $fromPath
     * @param string $toPath
     * @param integer $revision
     * @param integer $flags
     */
    public function export($fromPath, $toPath, $revision = null, $flags = null);
    
    /**
     * Imports an unversioned path into a repository
     *
     * @param string $path
     * @param string $url
     */
    public function import($path, $url);
    
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
    public function getLog($url, $startRevision = null, $endRevision = null, $limit = null, $flags = null);
    
    /**
     * Returns list of directory contents in repository URL
     *
     * @param string  $path
     * @param integer $revision
     * @return array Array of Zym_Svn_Client_List
     */
    public function getList($path, $revision = null);
    
    /**
     * Creates a directory in a working copy or repository
     *
     * @param string $path
     */
    public function createDirectory($path);
    
    /**
     * Revert changes to the working copy
     *
     * @param string  $path
     * @param boolean $recursive
     */
    public function revert($path, $recursive = null);
    
    /**
     * Delete a file or directory
     *
     * @param string $path
     */
    public function delete($path);
    
    /**
     * Update working copy
     *
     * @param string $path
     * @param integer $revision
     */
    public function update($path, $revision = null);
    
    /**
     * Remove property
     *
     * @param string $path
     * @param string $propName
     */
    public function deleteProperty($path, $propName);
    
    /**
     * Get property
     *
     * @param string  $path
     * @param string  $propName
     * @param integer $revision
     */
    public function getProperty($path, $propName, $revision = null);
    
    /**
     * Set property
     *
     * @param string $path
     * @param string $propName
     * @param mixed  $propVal
     * @return Zym_Svn_Client_Abstract
     */
    public function setProperty($path, $propName, $propVal);
    
    /**
     * Get property list
     *
     * @param string  $path
     * @param integer $revision
     * @return array
     */
    public function getPropertyList($path, $revision = null);
    
    /**
     * Set properties
     *
     * @param string $path
     * @param array  $props 
     */
    public function setProperties($path, array $props);
    
    /**
     * Get properties
     *
     * @param string  $path
     * @param integer $revision
     */
    public function getProperties($path, $revision = null);
    
    /**
     * Get repository version
     *
     * @return integer
     */
    public function getVersion();
    
    /**
     * Exec svn client
     *
     * @param array   $args
     * @param array   &$output
     * @param integer &$return
     */
    protected function _execClient(array $args, &$output = array(), &$return = null)
    {
        $escaped = '';
        
        foreach ($args as $cmd => $arg) {
            if (is_int($cmd)) {
                $escaped .= ' ' . escapeshellarg($arg);
            } else if ($arg === null) {
                $escaped .= ' ' . escapeshellcmd($cmd);
            } else {
                $escaped .= ' ' . escapeshellcmd($cmd) . ' ' . escapeshellarg($arg);
            }
        }
        
        exec($this->getClient . $escaped, $output, $return);
    }
}