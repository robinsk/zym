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
     * Powerpoint Document type
     *
     */
    const DOC_TYPE_PPT = 'ppt';

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
     * Upload document
     *
     * @param string  $file
     * @param string  $docType
     * @param string  $access
     * @param integer $revId
     * @return Zym_Service_Scribd_Document
     */
    public function upload($file, $docType, $access = null, $revId = null)
    {
        $response = $this->_restFileUpload(
            'docs.upload',
            $file,
            'file',
            array(
                'username' => $username,
                'password' => $password
            )
        );

        $this->_id        = $response->doc_id;
        $this->_accessKey = $response->access_key;
        if (isset($response->secret_password)) {
            $this->_secretPassword = $response->secret_password;
        }

        return $this;
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
}