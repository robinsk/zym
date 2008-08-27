In a view script or layout:
<?php echo $this->menu() ?>

or if short tags are enabled:
<?= $this->menu() ?>

Output:
<ul class="navigation">
    <li>
        <a href="/setting/the/position/option">Page 0?</a>

    </li>
    <li>
        <a id="home-link" href="/">Page 1</a>
    </li>
    <li class="active">
        <a href="/page2">Page 2</a>
        <ul>
            <li class="active">

                <a title="This element has a special class" class="special-one" href="/page2/page2_1">Page 2.1</a>
            </li>
            <li>
                <a title="This element has a special class too" class="special-two" href="/page2/page2_2">Page 2.2</a>
            </li>
        </ul>
    </li>
    <li>

        <a href="/page2/index/format/json/foo/bar">Page 2 with params</a>
    </li>
    <li>
        <a href="/page2/json">Page 2 with params and a route</a>
    </li>
    <li>
        <a href="/mymodule">Page 3</a>

    </li>
    <li class="active">
        <a href="#">Page 4</a>
        <ul>
            <li class="active">
                <a title="Page 4 using uri" href="/page4">Page 4.1</a>
                <ul>
                    <li class="active">

                        <a title="Page 4 using mvc params" href="/page4">Page 4.1.1</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <a href="#acl-guest.foo">ACL page 1 (guest.foo)</a>

        <ul>
            <li>
                <a href="#acl-member.foo">ACL page 1.1 (member.foo)</a>
            </li>
            <li>
                <a href="#acl-member.foo">ACL page 1.2 (member.foo)</a>
            </li>
            <li>

                <a href="#acl-member.foo">ACL page 1.4 (member.foo)</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="#acl-member">ACL page 2 (member.baz)</a>
    </li>
    <li>

        <span title="This URI page has no URI set, so a span is generated">No link :o</span>
    </li>
    <li>
        <a href="http://www.zym-project.com/">Zym</a>
    </li>
</ul>