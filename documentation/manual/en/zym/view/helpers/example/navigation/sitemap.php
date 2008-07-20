<?php
// In a view script or layout:

// format output
$this->sitemap()->setFormatOutput(true); // default is false

// other possible methods:
//$this->sitemap()->setUseXmlDeclaration(false); // default is true
//$this->sitemap()->setUseMaxDepth(1); // default is null, no max depth
//$this->sitemap()->setServerUrl('http://my.otherhost.com'); // default is to detect automatically

// print sitemap
echo $this->sitemap();