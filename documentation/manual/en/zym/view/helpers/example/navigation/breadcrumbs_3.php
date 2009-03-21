## In a layout:
<?php
$partial = array('breadcrumbs.phtml', 'default');
$this->navigation()->breadcrumbs()->setPartial($partial);
echo $this->navigation()->breadcrumbs()->render();
?>

## In application/modules/default/views/breadcrumbs.phtml:
<?php
echo implode(', ', array_map(
        create_function('$a', 'return $a->getLabel();'),
        $this->pages));
?>

Output:
Page 4, Page 4.1, Page 4.1.1