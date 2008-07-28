<?php
$coreCache = Zym_Cache::factory();
$server    = new Zend_XmlRpc_Server();

if (!Zym_XmlRpc_Server_Cache::get('myCacheId', $coreCache, $server)) {
    require_once 'Some/Service/Class.php';
    require_once 'Another/Service/Class.php';

    // Attach Some_Service_Class with namespace 'some'
    $server->setClass('Some_Service_Class', 'some');

    // Attach Another_Service_Class with namespace 'another'
    $server->setClass('Another_Service_Class', 'another');

    Zym_XmlRpc_Server_Cache::save('myCacheId', $coreCache, $server);
}

$response = $server->handle();
echo $response;