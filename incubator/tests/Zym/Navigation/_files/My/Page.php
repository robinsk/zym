<?php

require_once 'Zym/Navigation/Page.php';

class My_Page extends Zym_Navigation_Page
{
    /**
     * Returns the page's href
     *
     * @return string
     */
    public function getHref()
    {
        return '#';
    }
}