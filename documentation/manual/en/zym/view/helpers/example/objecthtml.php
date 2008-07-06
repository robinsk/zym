<div>
    <? $attrs   = array('attr' => 'test'); ?>
    <? $params  = array('param' => 'myVal'); ?>
    <? $content = '<p>Your browser does not support embedding objects</p>'; ?>
    <?= $this->objectHtml('http://consumerist.com', $attrs, $params, $content); ?>
</div>

<!-- Equivalent to below -->
<div>
    <object data="http://consumerist.com" type="text/html"
            classid="clsid:25336920-03F9-11CF-8FD0-00AA00686F13"
            attr="test">
        <param name="src" value="http://consumerist.com" />
        <param name="param" value="myVal" />

        <p>Your browser does not support embedding objects</p>
    </object>
</div>