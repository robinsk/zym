<?php
$app = Zym_App::getInstance();
$app->setEnvironment(Zym_App::ENV_PRODUCTION);
$app->setConfig('../bootstrap.ini');

// Dispatch
$app->dispatch();