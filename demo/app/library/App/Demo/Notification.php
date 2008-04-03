<?php
/**
 * Zym Framework Demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category App
 * @package App_Demo
 * @subpackage Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Notification_Interface
 */
require_once 'Zym/Notification/Interface.php';

/**
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package App_Demo
 * @subpackage Notification
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class App_Demo_Notification implements Zym_Notification_Interface 
{
    /**
     * Enter description here...
     *
     * @param Zym_Notification_Message $message
     */
    public function notify(Zym_Notification_Message $message)
    {
        printf('Name: %s, <br />Sender: %s, <br />Notifier: %s()', 
                $message->getName(), get_class($message->getSender()), __METHOD__);
    }

    /**
     * Enter description here...
     *
     * @param Zym_Notification_Message $message
     */
    public function hello(Zym_Notification_Message $message)
    {
        printf('Name: %s, <br />Sender: %s, <br />Notifier: %s()', 
                $message->getName(), get_class($message->getSender()), __METHOD__);
    }
}