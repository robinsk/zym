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
 * @package    Zym_App
 * @subpackage Resource_Doctine
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * Connection listener for the doctrine resource
 * 
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_App
 * @subpackage Resource_Doctine
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_App_Resource_Doctrine_ConnectionListener extends Doctrine_EventListener
{   
    /**
     * Connection charset
     *
     * @var string
     */
    private $_charset;
    
    /**
     * Sets a charset to relay to the postConnect event
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }
    
    /**
     * Handles the postConnect event from a Doctrine_Connection
     *
     * @param Doctine_Event $event
     */
    public function postConnect(Doctrine_Event $event)
    {
        if ($this->_charset) {
            $event->getInvoker()->setCharset($this->_charset);
        }
    }
}
