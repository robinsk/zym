<?php
$message = Zym_Message_Dispatcher::get();
$message->attach($theReceivingObject, $testEvent, $customMethod);