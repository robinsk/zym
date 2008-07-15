<?php
require_once 'Zym/Navigation/Page.php';

class My_Simple_Page extends Zym_Navigation_Page
{
    public function getHref()
    {
        return 'something-completely-different';
    }
}
