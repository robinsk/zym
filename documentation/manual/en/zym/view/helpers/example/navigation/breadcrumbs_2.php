In a view script or layout:

<?php
$this->navigation()->breadcrumbs()->setLinkLast(true);
$this->navigation()->breadcrumbs()->setSeparator('<span class="separator"> &#9654; </span>');
echo $this->navigation()->breadcrumbs();
?>

Output:
<a href="#">Page 4</a><span class="separator"> &#9654; </span><a title="Page 4 using uri" href="/page4">Page 4.1</a><span class="separator"> &#9654; </span><a title="Page 4 using mvc params" href="/page4">Page 4.1.1</a>

/////////////////////////////////////////////////////

Setting minimum depth required to render breadcrumbs:

<?php
$this->navigation()->breadcrumbs()->setMinDepth(10);
echo $this->navigation()->breadcrumbs();
?>

Outputs nothing because the deepest active page is not level 10 or deeper.