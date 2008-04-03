<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Paths array
 *
 * Change this array to add items into the include path.
 * Remeber to add get_include_path() which appends the existing paths
 
 * NOTE: The order is important because it determines the loading
 *       order of files and can have a great impact on loading performance.
 */
$paths = array(
    '.',
    PATH_PROJECT . 'app/library/',
    PATH_PROJECT . 'library/',
    PATH_PROJECT . 'library/incubator',
    PATH_PROJECT . 'library/laboratory',
    get_include_path()
);

/* ------------- Editing below this line is unneccessary ------------------- */
// Setup include paths
set_include_path(implode(PATH_SEPARATOR, $paths));
unset($paths);