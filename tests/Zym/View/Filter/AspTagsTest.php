<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_View_Filter_AspTags
 */
require_once 'Zym/View/Filter/AspTags.php';

/**
 * AspTags Test Case
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Filter
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Filter_AspTagsTest extends PHPUnit_Framework_TestCase
{
    /**
     * View filter
     *
     * @var Zym_View_Filter_AspTags
     */
    private $_filter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_filter = new Zym_View_Filter_AspTags();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->_filter);
    }

    public function testFilterReturnsIfPhpHandles()
    {
        if (!ini_get('asp_tags')) {
            $this->markTestSkipped('Enable php ini asp_tags to run this test');
        }
        $string = $this->_filter->filter('<% %>');
        $this->assertEquals('<% %>', $string);

        $string = $this->_filter->filter('<%= $foo %>');
        $this->assertEquals('<%= $foo %>', $string);
    }

    public function testFilterReturnsFiltered()
    {
        $string = $this->_filter->filter('<% echo "" %>');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->_filter->filter('<% echo ""; %>');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->_filter->filter('<%= $foo %>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->_filter->filter('<%= $foo; %>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->_filter->filter('<% echo $foo; echo $bar; %>');
        $this->assertEquals('<?php echo $foo; echo $bar; ?>', $string);
    }

    public function testFilterReturnsFilteredWithoutCloseTag()
    {
        $this->markTestSkipped('Does not support this yet');

        $string = $this->_filter->filter('<% echo ""');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->_filter->filter('<% echo "";');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->_filter->filter('<%= $foo');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->_filter->filter('<%= $foo;');
        $this->assertEquals('<?php echo $foo; ?>', $string);
    }

    public function testFiltersMultiLine()
    {
        $string = $this->_filter->filter('<% echo "
        "%>');
        $this->assertEquals('<?php echo "
        "; ?>', $string);


        $string = $this->_filter->filter('<% echo "
        ";%>');
        $this->assertEquals('<?php echo "
        "; ?>', $string);

        $string = $this->_filter->filter('<%=
        $foo%>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->_filter->filter('<%=
        $foo;%>');
        $this->assertEquals('<?php echo $foo; ?>', $string);
    }
}