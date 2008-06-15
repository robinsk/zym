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
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @TODO Make it iterable?
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_CouchDb_Document
{
    /**
     * Document content
     *
     * @var array
     */
    protected $_content;

    /**
     * Constructor
     *
     * @param array|string $content
     */
    public function __construct($content = array())
    {
        if (is_string($content)) {
            $content = Zend_Json::decode($content);
        }

        $content = (array) $content;

        $reserved = array('_id', '_rev');

        foreach ($reserved as $key) {
        	if (!isset($content[$key])) {
        	    $content[$key] = '';
        	}
        }

        $this->_content = $content;
    }

    /**
     * Get a value
     *
     * @throws Zym_CouchDb_Exception
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!array_key_exists($key, $this->_content)) {
            /**
             * @see Zym_CouchDb_Exception
             */
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Key "' . $key . '" not found in document.');
        }

        return $this->_content[$key];
    }

    /**
     * Set a value
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $content = $this->_content;

        $content[$key] = $value;

        $this->_content = $content;
    }

    /**
     * Get the document id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_content['_id'];
    }

    /**
     * Get the document revision
     *
     * @return string
     */
    public function getRevision()
    {
        return $this->_content['_rev'];
    }

    /**
     * Serialize the document
     *
     * @return string
     */
    public function toString()
    {
        return Zend_Json::encode($this->_content);
    }

    /**
     * Serialize the document with magic
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}