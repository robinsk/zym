<?php
$notification = Zym_Notification::get();
$notification->attach($theReceivingObject, testEvent, customMethod );