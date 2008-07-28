<?php

// Object style
$minifier = new Zym_Js_Minifier();
$js = $minifier->process('function() { alert(); }');

// Static
$js = Zym_Js_Minifier::minify('function() {alert();}');