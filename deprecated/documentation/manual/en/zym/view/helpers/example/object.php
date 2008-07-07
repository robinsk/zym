<div>
    <? $attrs   = array('attr' => 'test'); ?>
    <? $params  = array('param' => 'myVal'); ?>
    <? $content = '<p>Your browser does not support objects</p>'; ?>
    <?= $this->object('http://consumerist.com', 'text/html', $attrs, $params, $content); ?>
</div>

<!-- Equivalent to below -->
<div>
    <object data="http://consumerist.com" type="text/html" attr="test">
        <param name="param" value="myVal" />

        <p>Your browser does not support objects</p>
    </object>
</div>