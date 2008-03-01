<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_App
 * @subpackage Resource_Mail_Transport
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zend_Mail_Transport_Sendmail
 */
require_once 'Zend/Mail/Transport/Sendmail.php';

/**
 * @see Zym_App_Resource_Mail_Transport_Interface
 */
require_once 'Zym/App/Resource/Mail/Transport/Interface.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource_Mail_Transport
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_App_Resource_Mail_Transport_Sendmail implements Zym_App_Resource_Mail_Transport_Interface
{
    /**
     * Get transport
     *
     * @param Zend_Config $config
     * @return Zend_Mail_Transport_Sendmail
     */
    public static function getTransport(Zend_Config $config = null) 
    {
        $parameters =  isset($config->parameters) ? $config->parameters : null;        
        return new Zend_Mail_Transport_Sendmail($parameters);
    }
}