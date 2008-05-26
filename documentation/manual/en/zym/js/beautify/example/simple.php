<?php
$json = <<<JSON
{ "widget": {  "debug": "on",
   "window": {
    "title": "Sample Konfabulator Widget",
  "name": "main_window",
      "width": 500,
"height": 500
 }
 }
}
JSON;

$beautifier = new Zym_Js_Beautify();
echo $beautifier->parse($json);

/*
{
    "widget": {
        "debug": "on",
        "window": {
            "title": "Sample Konfabulator Widget",
            "name": "main_window",
            "width": 500,
            "height": 500
        }
    }
}
*/