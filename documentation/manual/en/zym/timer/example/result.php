<?php
// Retrieve complete times, assume each start/stop is 1 second
$timer = new Zym_Timer();
$timer->start();
$time = $timer->stop(); // 1s

$timer->start();
$time = $timer->stop(); // 2s

// Time
$runTime = $timer->getRun(); // 2s

$timer->start();
// Get time elapsed
$runTime = $timer->getElapsed(); // 2.5s
$timer->stop();