<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym_Search
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym_Search
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Search_Lucene_Index_Factory
{
	/**
	 * Registry key prefix
	 *
	 */
	const REGISTRY_PREFIX = 'ZSL';

	/**
	 * Exception
	 *
	 */
	const INDEX_DOESNT_EXISTS = 'Index "%s" does not exists';

	/**
     * Exception
     *
     */
	const NO_PATH_SPECIFIED = 'No index path specified';

	/**
	 * Default path for the search index.
	 * Usefull when the application has just one search index.
	 *
	 * Only used if set.
	 *
	 * @var string
	 */
	protected static $_defaultIndexPath;

	/**
	 * Set the default index path
	 *
	 * @param string $path
	 */
	public static function setDefaultIndexPath($path)
	{
		self::$_defaultIndexPath = $path;
	}

	/**
	 * Get a Zend_Search_Lucene instance
	 *
	 * @param string $indexPath
	 * @return Zend_Search_Lucene_Interface
	 */
	public static function getIndex($indexPath = null, $appendDefaultPath = true, $createIfNotExists = false)
	{
		if (!$indexPath && !self::$_defaultIndexPath) {
		    self::_throwException(self::NO_PATH_SPECIFIED);
		}

		$trimMask = '/\\';

		rtrim($indexPath, $trimMask);

		if ($appendDefaultPath) {
			$indexPath = rtrim(self::$_defaultIndexPath, $trimMask) . DIRECTORY_SEPARATOR . ltrim($indexPath, $trimMask);
		}

		$registryKey = self::REGISTRY_PREFIX . $indexPath;

		if (Zend_Registry::isRegistered($registryKey)) {
			$index = Zend_Registry::get($registryKey);
		} else {
		    if (file_exists($indexPath)) {
		        $index = Zend_Search_Lucene::open($indexPath);
            } else {
                if (!$createIfNotExists) {
                    self::_throwException(sprintf(self::INDEX_DOESNT_EXISTS, $indexPath));
                }

                $index = Zend_Search_Lucene::create($indexPath);
            }

            Zend_Registry::set($registryKey, $index);
		}

		return new Zym_Search_Lucene_Index($index);
	}

	/**
	 * Throw an exception
	 *
	 * @throws Zym_Search_Lucene_Exception
	 * @param string $message
	 */
	protected static function _throwException($message)
	{
	    /**
         * @see Zym_Search_Lucene_Exception
         */
        require_once 'Zym/Search/Lucene/Exception.php';

        throw new Zym_Search_Lucene_Exception($message);
	}
}