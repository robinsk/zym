<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category Zym_Tests
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_View_Helper_XmlDeclaration
 */
require_once 'Zym/View/Helper/XmlDeclaration.php';

/**
 * Zym_View_Helper_XmlDeclaration test case.
 *
 * @author  Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym_Tests
 * @package Zym_View
 * @subpackage Helper
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_View_Helper_XmlDeclarationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Helper
     *
     * @var Zym_View_Helper_XmlDeclaration
     */
    protected $_helper;

    /**
     * Setup
     *
     */
    protected function setUp()
    {
        $this->_helper = new Zym_View_Helper_XmlDeclaration();
    }

    public function testXmlDeclaration()
    {
        $xml = $this->_helper->xmlDeclaration();

        $this->assertRegExp('/<\?xml version="1.0" encoding="UTF-8" \?>/si', $xml);
    }

    public function testXmlDeclarationWithVersion()
    {
        $xml = $this->_helper->xmlDeclaration('2');
        $this->assertRegExp('/<\?xml version="2.0" encoding="UTF-8" \?>/si', $xml);

        $xml = $this->_helper->xmlDeclaration('2.0');
        $this->assertRegExp('/<\?xml version="2.0" encoding="UTF-8" \?>/si', $xml);
    }

    public function testXmlDeclarationWithEncoding()
    {
        $xml = $this->_helper->xmlDeclaration('1.0', 'UTF-16');

        $this->assertRegExp('/<\?xml version="1.0" encoding="UTF-16" \?>/si', $xml);
    }

    public function testXmlDeclarationWithStandalone()
    {
        $xml = $this->_helper->xmlDeclaration('1.0', 'UTF-16', 'yes');

        $this->assertRegExp('/<\?xml version="1.0" encoding="UTF-16" standalone="yes" \?>/si', $xml);
    }
}