<?php
// Context value in constructor
$confirm = new Zym_Validate_Confirm('confirm', array('confirm' => 'aValue'));
$isValid = $confirm->isValid('bar');

// Context value in isValid
$confirm = new Zym_Validate_Confirm('confirm');
$isValid = $confirm->isValid('bar', array('confirm' => 'aValue'));