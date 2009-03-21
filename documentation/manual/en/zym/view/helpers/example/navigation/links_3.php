<?php
$helper = $this->navigation()->links();

// render only start, next, and prev
$helper->setRenderFlag(Zym_View_Helper_Navigation_Links::RENDER_START |
                       Zym_View_Helper_Navigation_Links::RENDER_NEXT |
                       Zym_View_Helper_Navigation_Links::RENDER_PREV);

// render only native link types
$helper->setRenderFlag(Zym_View_Helper_Navigation_Links::RENDER_ALL ^
                       Zym_View_Helper_Navigation_Links::RENDER_CUSTOM);

// render all but chapter
$helper->setRenderFlag(Zym_View_Helper_Navigation_Links::RENDER_ALL ^
                       Zym_View_Helper_Navigation_Links::RENDER_CHAPTER);
?>