## In a layout:
<?php
$partial = array('menu.phtml', 'default');
$this->navigation()->menu()->setPartial($partial);
echo $this->navigation()->menu()->render();
?>

## In application/modules/default/views/menu.phtml:
<?php
foreach ($this->container as $page) {
    echo $this->menu()->htmlify($page), PHP_EOL;
}
?>

Output:
<a href="/setting/the/order/option">Page 0?</a>
<a id="menu-home-link" href="/">Page 1</a>
<a href="/page2">Page 2</a>
<a href="/page2/index/format/json/foo/bar">Page 2 with params</a>
<a href="/page2/json">Page 2 with params and a route</a>
<a href="/mymodule">Page 3</a>
<a href="#">Page 4</a>
<a href="/">Page 5</a>
<a href="#acl-guest.foo">ACL page 1 (guest.foo)</a>
<a href="#acl-admin.foo">ACL page 2 (admin.foo)</a>
<span title="This URI page has no URI set, so a span is generated">No link :o</span>
<a href="http://www.zym-project.com/">Zym</a>
s