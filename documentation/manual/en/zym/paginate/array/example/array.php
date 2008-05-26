<?php
// Create an array containing the numbers 1 to 100.
$data = range(1,100);
$paginate = new Zym_Paginate_Array($data);

// I want to show 15 items per page. 
// Setting this is optional, it will default to 10.
$paginate->setRowLimit(15); 

 // Get page 3
$page3 = $paginate->getPage(3);