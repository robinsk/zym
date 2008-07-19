<?php
$notification = Zym_Message::get();
$notification->attach($theReceivingObject, testEvent, customMethod );