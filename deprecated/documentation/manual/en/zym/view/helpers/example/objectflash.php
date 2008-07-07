<div>
    <? $attrs   = array('attr' => 'test'); ?>
    <? $params  = array('param' => 'myVal'); ?>
    <? $content = '<p>Your browser does not support flash</p>'; ?>
    <?= $this->objectFlash('/bar.swf', $attrs, $params, $content); ?>
</div>

<!-- Equivalent to below -->
<div>
    <object data="/bar.swf" type="application/x-shockwave-flash"
            classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab"
            attr="test">
        <param name="movie" value="/bar.swf" />
        <param name="param" value="myVal" />

        <p>Your browser does not support flash</p>
    </object>
</div>