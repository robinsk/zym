<?php
echo $this->view->navigation()->links();
?>

Output:
<link rel="start" href="/setting/the/order/option" title="Page 0?">
<link rel="next" href="#acl-guest.foo" title="ACL page 1 (guest.foo)">
<link rel="prev" href="/page4" title="Page 4.1">
<link rel="chapter" href="/" title="Page 1">
<link rel="chapter" href="/page2" title="Page 2">
<link rel="chapter" href="/page2/index/format/json/foo/bar" title="Page 2 with params">
<link rel="chapter" href="/page2/json" title="Page 2 with params and a route">
<link rel="chapter" href="/mymodule" title="Page 3">
<link rel="chapter" href="#" title="Page 4">
<link rel="chapter" href="#acl-guest.foo" title="ACL page 1 (guest.foo)">
<link rel="chapter" href="http://www.zym-project.com/" title="Zym">
<link rev="subsection" href="/page4" title="Page 4.1">