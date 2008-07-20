In a view script or layout:
<?php echo $this->breadcrumbs() ?>

or if short tags are enabled:
<?= $this->breadcrumbs() ?>

Output:
<a href="#">Page 4</a> &gt; <a title="Page 4 using uri" href="/page4">Page 4.1</a> &gt; Page 4.1.1

Rendering with 8 spaces indentation:
<?= $this->breadcrumbs()->toString(8) ?>
        <a href="#">Page 4</a> &gt; <a title="Page 4 using uri" href="/page4">Page 4.1</a> &gt; Page 4.1.1