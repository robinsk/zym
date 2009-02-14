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
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Service_Scribd_Abstract
 */
require_once 'Zym/Service/Scribd/Abstract.php';

/**
 * Zym Scribd Document API Implementation
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Scribd_Document extends Zym_Service_Scribd_Abstract
{
    /**
     * Public access
     *
     */
    const ACCESS_PUBLIC  = 'public';

    /**
     * Private access
     *
     */
    const ACCESS_PRIVATE = 'private';

    /**
     * Conversion status displayable
     *
     */
    const CONVERSION_STATUS_DISPLAYABLE = 'DISPLAYABLE';

    /**
     * Conversion status done
     *
     */
    const CONVERSION_STATUS_DONE        = 'DONE';

    /**
     * Conversion status error
     *
     */
    const CONVERSION_STATUS_ERROR       = 'ERROR';

    /**
     * Conversions status processing
     *
     */
    const CONVERSION_STATUS_PROCESSING  = 'PROCESSING';

    /**
     * PDF Document type
     *
     */
    const DOC_TYPE_PDF = 'pdf';

    /**
     * Word Document tyoe
     *
     */
    const DOC_TYPE_DOC = 'doc';

    /**
     * Plain Text Document type
     *
     */
    const DOC_TYPE_TXT = 'txt';

    /**
     * Original Document type
     *
     */
    const DOC_TYPE_ORIGINAL = 'original';

    /**
     * Powerpoint Document type
     *
     */
    const DOC_TYPE_PPT = 'ppt';

    /**
     * Search Scope All
     *
     */
    const SCOPE_ALL = 'all';

    /**
     * Search Scope User
     *
     */
    const SCOPE_USER = 'user';

    /**
     * Search Scope Account
     *
     */
    const SCOPE_ACCOUNT = 'account';

    /**
     * Property Map
     *
     * @var array
     */
    protected $_propertyMap = array('docId' => 'id');

    /**
     * Id
     *
     * @var integer
     */
    private $_id;

    /**
     * Access Key
     *
     * @var string
     */
    private $_accessKey;

    /**
     * Secret Password
     *
     * @var string
     */
    private $_secretPassword;

    /**
     * Document Title
     *
     * @var string
     */
    private $_title;

    /**
     * Document Description
     *
     * @var string
     */
    private $_description;

    /**
     * Page Count
     *
     * @var integer
     */
    private $_pageCount;

    /**
     * License
     *
     * @var string
     */
    private $_license;

    /**
     * Show Ads
     *
     * @var string
     */
    private $_showAds;

    /**
     * Link Back Url
     *
     * @var string
     */
    private $_linkBackUrl;

    /**
     * Tags
     *
     * @var string
     */
    private $_tags;

    /**
     * Author
     *
     * @var string
     */
    private $_author;

    /**
     * Publisher
     *
     * @var string
     */
    private $_publisher;

    /**
     * When Published
     *
     * @var string
     */
    private $_whenPublished;

    /**
     * Edition
     *
     * @var string
     */
    private $_edition;

    /**
     * Thumbnail Url
     *
     * Link to a JPG that contains a thumbnail of the document.
     * This can be used to make search results more presentable.
     * Do not cache thumbnail URLs - they are subject to change.
     *
     * @var string
     */
    private $_thumbnailUrl;

    /**
     * Construct
     *
     * @param string $id
     */
    public function __construct($id = null)
    {
        if ($id !== null) {
            $this->_id = $id;
        }
    }

    /**
     * Upload document
     *
     * @param string  $file
     * @param string  $docType
     * @param string  $access
     * @param integer $revId
     * @return Zym_Service_Scribd_Document
     */
    public function upload($file, $docType = null, $access = null, $revisionId = null)
    {
        $options = array();
        if ($docType !== null) {
            $options['doc_type'] = $docType;
        }

        if ($access !== null) {
            $options['access'] = $access;
        }

        if ($revisionId !== null) {
            $options['rev_id'] = $revisionId;
        }

        $response = $this->_restFileUpload(
            'docs.upload',
            $file,
            'file',
            $options
        );

        $this->_id        = $response->doc_id;
        $this->_accessKey = $response->access_key;
        if (isset($response->secret_password)) {
            $this->_secretPassword = $response->secret_password;
        }

        return $this;
    }

    /**
     * Upload document from url
     *
     * @param string  $url
     * @param string  $docType
     * @param string  $access
     * @param integer $revId
     * @return Zym_Service_Scribd_Document
     */
    public function uploadFromUrl($url, $docType = null, $access = null, $revisionId = null)
    {
        $options = array('url' => $url);

        if ($docType !== null) {
            $options['doc_type'] = $docType;
        }

        if ($access !== null) {
            $options['access'] = $access;
        }

        if ($revisionId !== null) {
            $options['rev_id'] = $revisionId;
        }

        $response = $this->_restGet(
            'docs.uploadFromUrl',
            $options
        );

        $this->_id        = $response->doc_id;
        $this->_accessKey = $response->access_key;
        if (isset($response->secret_password)) {
            $this->_secretPassword = $response->secret_password;
        }

        return $this;
    }

    /**
     * Get Document List
     *
     * @param integer $offset
     * @param integer $limit
     * @return array Array of Documents
     */
    public function getList($offset, $limit = 25)
    {
        $response = $this->_restGet(
        	'docs.getList',
            array('offset' => $offset, 'limit' => $limit)
        );

        $list = $this->_simpleXmlToArray($response->result_set);
        if (count($list) <= 1) {
            // So one exists
            if (isset($response->result_set->result)) {
                $doc      = new self();
                $doc->setScribdClient($this->getScribdClient());

        	    $return[] = $doc->setFromArray($list['result']);
            }
        } else {
            foreach ($list as $results) {
                foreach ((array)$results as $result) {
                    $doc      = new self();
                    $doc->setScribdClient($this->getScribdClient());

            	    $return[] = $doc->setFromArray($result);
                }
            }
        }

        return $return;
    }

    /**
     * Get document conversion status
     *
     * @return string
     */
    public function getConversionStatus()
    {
        $response = $this->_restGet('docs.getConversionStatus', array('doc_id' => $this->getId()));
        return (string) $response->conversion_status;
    }

    /**
     * Get document settings
     *
     * @return Zym_Service_Scribd_Document
     */
    public function getSettings()
    {
        $response = $this->_restGet('docs.getSettings', array('doc_id' => $this->getId()));
        $this->setFromArray($this->_simpleXmlToArray($response));
        return $this;
    }

    /**
     * Change Settings
     *
     * This method updates the meta-data for existing documents.
     * Only send arguments for fields you would like to overwrite.
     *
     * @param array $metadata
     * @return Zym_Service_Scribd_Document
     */
    public function changeSettings(array $metadata)
    {
        $this->_restGet('docs.changeSettings', $metadata);

        return $this;
    }

    /**
     * Get Download Url
     *
     * @param string $docType
     * @return string
     */
    public function getDownloadUrl($docType)
    {
        $response = $this->_restGet('docs.getDownloadUrl',
            array(
            	'doc_id' => $this->getId(),
            	'doc_type' => $docType
            )
        );
        return (string) $response->download_link;
    }

    /**
     * Delete Document
     *
     * @return void
     */
    public function delete()
    {
         $this->_restGet('docs.delete', array('doc_id' => $this->getId()));
    }

    /**
     * Search
     *
     * @param string $query
     * @param integer $offset
     * @param integer $limit
     * @param string $scope
     * @return Zym_Service_Scribd_Search_Result
     */
    public function search($query, $offset, $limit = null, $scope = null)
    {
        $options = array(
            'query' => $query,
            'num_start' => $offset
        );

        if ($limit !== null) {
            $options['num_results'] = $limit;
        }

        if ($scope !== null) {
            $options['scope'] = $scope;
        }

        $response = $this->_restGet(
        	'docs.search',
            $options
        );

        $return = array();

        $list = $this->_simpleXmlToArray($response->result_set);
        if (count($list) <= 1) {
            // So one exists
            if (isset($response->result_set->result)) {
                $doc      = new self();
                $doc->setScribdClient($this->getScribdClient());

        	    $return[] = $doc->setFromArray($list['result']);
            }
        } else {
            foreach ($list as $results) {
                foreach ((array)$results as $result) {
                    $doc      = new self();
                    $doc->setScribdClient($this->getScribdClient());

            	    $return[] = $doc->setFromArray($result);
                }
            }
        }

        /**
         * @see Zym_Service_Scribd_Search_Result
         */
        require_once 'Zym/Service/Scribd/Search/Result.php';
        $results = new Zym_Service_Scribd_Search_Result(
            $return,
            $response->result_set['totalResultsAvailable'],
            $response->result_set['firstResultPosition']
        );

        return $results;
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getAccessKey()
    {
        return $this->_accessKey;
    }

    public function getSecretPassword()
    {
        return $this->_secretPassword;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function getLicense()
    {
        return $this->_license;
    }

    public function getShowAds()
    {
        return $this->_showAds;
    }

    public function getLinkBackUrl()
    {
        return $this->_linkBackUrl;
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function getAuthor()
    {
        return $this->_author;
    }

    public function getPublisher()
    {
        return $this->_publisher;
    }

    public function getWhenPublished()
    {
        return $this->_whenPublished;
    }

    public function getEdition()
    {
        return $this->_edition;
    }

    /**
     * Set From Array
     *
     * @param array $metadata
     * @return Zym_Service_Scribd_Document
     */
    public function setFromArray(array $metadata)
    {
        foreach ($metadata as $key => $value) {
            // Camel-case keys
        	$propertyName    = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        	$propertyName{0} = strtolower($propertyName{0});

        	if (in_array($propertyName, array_keys($this->_propertyMap))) {
        	    $propertyName = $this->_propertyMap[$propertyName];
        	}

    	    $this->{'_' . $propertyName} = $value;
        }

        return $this;
    }

    /**
     * Returns a string or an associative and possibly multidimensional array from
     * a SimpleXMLElement.
     *
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array|string
     */
    protected function _simpleXmlToArray(SimpleXMLElement $xmlObject)
    {
        $config = array();

        // Search for children
        if (count($xmlObject->children()) > 0) {
            foreach ($xmlObject->children() as $key => $value) {
                if (count($value->children()) > 0) {
                    $value = $this->_simpleXmlToArray($value);
                } else {
                    $value = (string) $value;
                }

                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
                        $config[$key] = array($config[$key]);
                    }

                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        } else if ((count($config) === 0)) {
            // Object has no children nor attributes
            // attribute: it's a string
            $config = (string) $xmlObject;
        }

        return $config;
    }
}