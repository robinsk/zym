<?php
require_once 'Zend/Json.php';

class Zym_CouchDb_Document
{
    protected $_content;

    public function __construct($content)
    {
        if (is_string($content)) {
            $content = Zend_Json::decode($content);
        }

        $reserved = array('_id', '_rev');

        foreach ($reserved as $key) {
        	if (!isset($content[$key])) {
        	    $content[$key] = '';
        	}
        }

        $this->_content = $content;
    }

    public function getId()
    {
        return $this->_content['_id'];
    }

    public function getRevision()
    {
        return $this->_content['_rev'];
    }

    public function __sleep()
    {
        return Zend_Json::encode($this->_content);
    }

    public function __wakeup($value)
    {
        $this->_content = Zend_Json::decode($value);
    }
}