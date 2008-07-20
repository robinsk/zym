<?php
$nav = new Zym_Navigation(array(
    array(
        'label'      => 'Site 1',
        'uri'        => 'site1',
        'changefreq' => 'daily',
        'priority'   => '0.9'
    ),
    array(
        'label'      => 'Site 2',
        'uri'        => 'site2',
        'active'     => true,
        'lastmod'    => 'earlier'
    ),
    array(
        'label'      => 'Site 3',
        'uri'        => 'site3',
        'changefrew' => 'often'
    )
));

$this->menu()->setUlClass('my-nav');
$this->menu()->setIndent(4);
echo $this->menu()->renderMenu($nav);
?>
    <ul class="my-nav">
        <li>
            <a href="site1">Site 1</a>
        </li>
        <li class="active">
            <a href="site2">Site 2</a>
        </li>
        <li>
            <a href="site3">Site 3</a>
        </li>
    </ul>