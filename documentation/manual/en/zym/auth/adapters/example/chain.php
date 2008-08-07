<?php
// Adapters to authenticate with
$dbAdapter = new Zend_Auth_Adapter_DbTable($db, 'users');
$dbAdapter->setIdentityColumn('username')
          ->setCredentialColumn('password')
          ->setIdentity('john')
          ->setCredential('xxx');

$ldapAdpater = new Zend_Auth_Adapter_Ldap(array(), 'john', 'xxx');

// Setup chain adapter
$chain = new Zym_Auth_Adapter_Chain();
$chain->addAdapter($dbAdapter)
      ->addAdapter($ldapAdpater);

// Authenticate with Zend_Auth to persist
$auth = Zend_Auth::getInstance();
$result = $auth->authenticate($chain);

// Did we pass?
if ($result->isValid()) {
    // Get the successful adapter
    $successAdapter = $chain->getLastSuccessfulAdapter();
    if ($successAdapter === $dbAdapter) {
        // Do something like store user info in session
    } else if ($successAdapter === $ldapAdpater) {
        // Do something like store user info in session
    }
}