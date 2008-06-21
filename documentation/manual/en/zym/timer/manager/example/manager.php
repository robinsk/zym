<?php
$manager = new Zym_Timer_Manager();

// Add a timer of name myTestTimer to myGroup
$manager->addTimer('myTestTimer', new Zym_Timer(), 'myGroup');

// Create a timer myOtherTimer to myGroup
$manager->createTimer('myOtherTimer', 'myGroup');

// Create a myRandomTimer without a group
$manager->createTimer('myRandomTimer');

// Get runtime of all registered timers
$runtime = $manager->getRun();

// Get total runtime for timers of myGroup
$runtime = $manager->getGroupRun('myGroup');

// Number of timers currently being managed
$count = count($manager);

// Clear all timers
$manager->clearTimers();