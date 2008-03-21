<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym
 * @package Zym_View
 * @subpackage Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * Converts short tags to full <?php
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @package Zym_View
 * @subpackage Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Filter_ShortTags
{
    /**
     * Filter
     *
     * @param string $buffer
     * @return string
     */
    public function filter($buffer)
    {
        // Don't parse if short_tags is enabled
        if (ini_get('short_tags')) {
            return $buffer;
        }
        
        $pattern = array(
            '/<\?=\s*(.*?)\s*%>/is', // <?=
            '/<\?(?!php)\s*(.*?)/is', // <?
        );
        
        $replace = array(
            '<?php echo$1 ?>',
            '<?php $1'
        );
        
        $out = preg_replace($pattern, $replace, $buffer);

        return $out;
    }
}
/*
$filter = new Zym_View_Filter_ShortTags();
echo '<html><body><pre>';
$a = array(
    '<?=$this->test ? $a
                    : $b; ?>'.

    '<?=   $this->test ?>',

    '<? echo $this->test;
    echo "Sdf";
    ?>',

    '<?php echo $this->Test;
    print "Sdf";
    ?>',

    '<?php echo "<?=$this->test; ?>"',
    '<?xml',
    '<?php echo "<?xml"; ?>'
);
foreach ($a as $ab) {
    echo htmlentities($filter->filter($ab)) . '</br></br>';
}

echo '</pre></body>';
//(?<!<\?php)<\?(?!php)=\s*(.*?);*\s*\?>*/
