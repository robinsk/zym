<?php

$timer = new Zym_Timer();

// Start timer
$timer->start();

for ($x = 0; $x < 100000; ++$x) {
    // Long execution
}

// Stop timer
$runTime = $timer->stop(); // Returns time elapsed since start()
