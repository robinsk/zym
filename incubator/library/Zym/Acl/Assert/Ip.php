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
 * @package    Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Acl_Assert_Interface
 */
require_once 'Zend/Acl/Assert/Interface.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Acl
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Acl_Assert_Ip implements Zend_Acl_Assert_Interface
{
    /**
     * IP address whitelist
     */
    protected $_addresses = array();

    /**
     * Constructor
     *
     * @param array $addresses
     */
    public function __construct(array $addresses = array())
    {
        if (!empty($addresses)) {
            $this->_addresses = $addresses;
        }
    }

    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Zend_Acl                    $acl
     * @param  Zend_Acl_Role_Interface     $role
     * @param  Zend_Acl_Resource_Interface $resource
     * @param  string                      $privilege
     * @return boolean
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
        return $this->_isCleanIP($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Check if the the IP is in the whitelist
     *
     * @param string $ip
     * @return boolean
     */
    protected function _isCleanIP($ip)
    {
        $wildCard = '*';
        $separator = '.';
        $regxMatch = '/(\d-\d)/';

        foreach ($this->_addresses as $ipAddress) {
            if ($ip == $ipAddress) {
                return true;
            }

            if (strpos($ipAddress, $wildCard) !== false) {
                $wildcardIp = str_replace($wildCard, '', $ipAddress);

                if (strpos($ip, $wildcardIp) === 0) {
                    return true;
                }
            } else if (preg_match($regxMatch, $ipAddress) == 1) {
                $exploded = explode($separator, $ipAddress);

                $range = array_pop($exploded);

                $range = str_replace(array('(', ')'), '', $range);

                $ipStart = implode($separator, $exploded);

                if (strpos($ip, $ipStart) === 0) {
                    list($rangeStart, $rangeEnd) = explode('-', $range);

                    for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                        $checkIp = implode($separator, array($ipStart, $i));

                        if ($ip == $checkIp) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}