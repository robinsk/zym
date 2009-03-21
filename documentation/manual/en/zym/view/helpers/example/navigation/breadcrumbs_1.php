In a view script or layout:
<?php echo $this->navigation()->breadcrumbs(); ?>

or if short tags are enabled:
<?= $this->navigation()->breadcrumbs(); ?>

The two calls above take advantage of the magic __toString() method, and are
equivalent to:
<?php echo $this->navigation()->breadcrumbs()->render(); ?>

Output:
<a href="#">Page 4</a> &gt; <a title="Page 4 using uri" href="/page4">Page 4.1</a> &gt; Page 4.1.1

Rendering with 8 spaces indentation:
<?= $this->navigation()->breadcrumbs()->setIndent(8); ?>
        <a href="#">Page 4</a> &gt; <a title="Page 4 using uri" href="/page4">Page 4.1</a> &gt; Page 4.1.1