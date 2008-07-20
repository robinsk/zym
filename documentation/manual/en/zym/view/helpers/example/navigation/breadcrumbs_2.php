In a view script or layout:
<?php
$this->breadcrumbs()->setLinkLast(true);
$this->breadcrumbs()->setSeparator('<span class="separator"> &#9654; </span>');
echo $this->breadcrumbs();
?>
Output:
<a href="#">Page 4</a><span class="separator"> &#9654; </span><a title="Page 4 using uri" href="/page4">Page 4.1</a><span class="separator"> &#9654; </span><a title="Page 4 using mvc params" href="/page4">Page 4.1.1</a>

Setting minimum depth required to render breadcrumbs:
<?php
$this->breadcrumbs()->setMinDepth(10);
// this outputs an empty string, since the deepest
// active page is not level 10 or deeper
echo $this->breadcrumbs();
?>