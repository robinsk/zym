<div>
    <? $attrs   = array('attr' => 'test'); ?>
    <? $params  = array('param' => 'myVal'); ?>
    <? $content = '<p>Your browser does not support quicktime</p>'; ?>
    <?= $this->objectQuicktime('/bar.mov', $attrs, $params, $content); ?>
</div>

<!-- Equivalent to below -->
<div>
    <object data="/bar.swf" type="video/quicktime"
            classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
            codebase="http://www.apple.com/qtactivex/qtplugin.cab"
            attr="test">
        <param name="src" value="/bar.mov" />
        <param name="param" value="myVal" />

        <p>Your browser does not support quicktime</p>
    </object>
</div>