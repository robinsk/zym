<?php
echo $this->navigation()->menu()->renderSubMenu();
?>

Output if Page 2 is active:
<ul>
    <li>
        <a title="This element has a special class" class="special-one" href="/page2/page2_1">Page 2.1</a>
    </li>
    <li>
        <a title="This element has a special class too" class="special-two" href="/page2/page2_2">Page 2.2</a>
    </li>
</ul>

Output if Page 2.1 is active:
<ul>
    <li class="active">
        <a title="This element has a special class" class="special-one" href="/page2/page2_1">Page 2.1</a>
    </li>
    <li>
        <a title="This element has a special class too" class="special-two" href="/page2/page2_2">Page 2.2</a>
    </li>
</ul>