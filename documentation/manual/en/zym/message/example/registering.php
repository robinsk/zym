<?php
$notification = Zym_Message_Dispatcher::get();
$notification->attach($theReceivingObject, $testEvent, $customMethod);