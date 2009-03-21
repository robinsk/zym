<?php

$page = new Zym_Navigation_Page_Mvc();
$page->foo = 'bar';
$page->meaning = 42;

echo $page->foo;

if ($page->meaning != 42) {
    // action should be taken
}