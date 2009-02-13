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
 * Zym Scribd User API Implementation
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym
 * @package    Zym_Service
 * @subpackage Scribd
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Service_Scribd_User extends Zym_Service_Scribd_Abstract
{
    /**
     * User id
     *
     * @var integer
     */
    private $_id;

    /**
     * Username
     *
     * @var string
     */
    private $_username;

    /**
     * Email
     *
     * @var string
     */
    private $_email;

    /**
     * Name
     *
     * @var string
     */
    private $_name;

    /**
     * Session key
     *
     * @var string
     */
    private $_sessionKey;

    /**
     * Phantom Id (my_user_id)
     *
     * @var integer
     */
    private $_phantomId;


    /**
     * Login
     *
     * This method allows your API application to sign in as an existing Scribd user,
     * executing methods as that user.
     *
     * @throws Exception code 613 if login failed
     *
     * @param string $username
     * @param string $password
     * @return Zym_Service_Scribd_User
     */
    public function login($username, $password)
    {
        $response = $this->_restGet(
            'user.login',

            array(
                'username' => $username,
                'password' => $password
            )
        );

        $this->_id         = $response->user_id;
        $this->_username   = $response->username;
        $this->_email      = $response->email;
        $this->_name       = $response->name;
        $this->_sessionKey = $response->session_key;

        return $this;
    }

    /**
     * User Signup
     *
     * This method allows your API application to signup a new Scribd user.
     * If the signup is successful, your application will be passed back a session
     * key which will allow you to execute methods on behalf of the new user.
     *
     * @throws Exception Code 615 - Username taken, 616 - User with email exists, 628 - Invalid email
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $name
     * @return Zym_Service_Scribd_User
     */
    public function signup($username, $password, $email, $name = '')
    {
        $response = $this->_restGet(
            'user.signup',

            array(
                'username' => $username,
                'password' => $password,
                'email'    => $email,
                'name'     => $name
            )
        );

        $this->_id         = $response->user_id;
        $this->_username   = $response->username;
        $this->_email      = $response->email;
        $this->_name       = $response->name;
        $this->_sessionKey = $response->session_key;

        return $this;
    }

    /**
     * Get auto signin url
     *
     * This method returns a URL that, when visited, will automatically sign in
     * the given user account and then redirect to the URL you provide.
     *
     * @param string $nextUrl The URL or path portion of a Scribd URL to redirect to. Set to blank for home page.
     * @return string
     */
    public function getAutoSigninUrl($nextUrl)
    {
        $response = $this->_restGet('user.getAutoSigninUrl', array('next_url' => $nextUrl));

        $url = $response->url;
        return $url;
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

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Get email
     *
     * @return unknown
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Get real name of user
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get session key
     *
     * @return string
     */
    public function getSessionKey()
    {
        return $this->_sessionKey;
    }

    /**
     * Set phantom id
     *
     * @param integer $id
     * @return Zym_Service_Scribd_User
     */
    public function setPhantomId($id)
    {
        $this->_phantomId = $id;
        return $this;
    }

    /**
     * Get phantom id
     *
     * @return integer
     */
    public function getPhantomId()
    {
        return $this->_phantomId;
    }
}