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
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 */

/**
 * @see Zym_App_Resource_Abstract
 */
require_once 'Zym/App/Resource/Abstract.php';

/**
 * @see Zend_Mail
 */
require_once 'Zend/Mail.php';

/**
 * Mail component configuration
 * 
 * @author Geoffrey Tran
 * @license http://www.assembla.com/wiki/show/zym/License New BSD License
 * @category Zym
 * @package Zym_App
 * @subpackage Resource
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 */
class Zym_App_Resource_Mail extends Zym_App_Resource_Abstract
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = array(
        Zym_App::ENV_DEFAULT => array(
            'default_transport' => 'sendmail',
        
            'transport' => array(
                'sendmail' => array(
                    'parameters' => null
                ),
                
                'smtp' => array(
                    'host' => '127.0.0.1'
                )
            )
        )
    );
    
    /**
     * Mail transport
     *
     * @var Zend_Mail_Transport_Abstract
     */
    protected $_transport;

    /**
     * Setup mail component
     *
     */
    public function setup(Zend_Config $config)
    {
        // Don't do anything if we already have our default obj
        if ($config->default_transport instanceof Zend_Mail_Transport_Abstract) {
            Zend_Mail::setDefaultTransport($config->default_transport);
            return;
        }
        
        // TODO: Decide whether to lazy load or not
        // Transport Config
        $transportConfig = $config->transport;
        switch (trim(strtolower($config->default_transport))) {
            case 'smtp':
                require_once('Zend/Mail/Transport/Smtp.php');
                $transport = new Zend_Mail_Transport_Smtp($transportConfig->smtp->host, $transportConfig->smtp->toArray());
                break;
                
            case 'sendmail': // defaults to sendmail
            default:
                require_once('Zend/Mail/Transport/Sendmail.php');
                $transport = new Zend_Mail_Transport_Sendmail($transportConfig->sendmail->parameters);
        }
        
        // Save transport in here
        $this->setTransport($transport);
        
        // Set default mail transport
        Zend_Mail::setDefaultTransport($transport);
    }
    
    /**
     * Set transport
     *
     * @param Zend_Mail_Transport_Abstract $transport
     * @return Zym_App_Resource_Mail
     */
    public function setTransport(Zend_Mail_Transport_Abstract $transport)
    {
        $this->_transport = $transport;
        return $this;
    }
    
    /**
     * Get the mail transport
     *
     * @return Zend_Mail_Transport_Abstract
     */
    public function getTransport()
    {
        return $this->_transport;
    }
}