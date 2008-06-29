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
 * @package Zym_Version
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Version
 */
require_once 'Zym/Version.php';

/**
 * Version test
 * 
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Version
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_VersionTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Tests that version_compare() and its "proxy"
     * Zym_Version::compareVersion() work as expected.
     */
    public function testVersionCompare()
    {
        $expect = -1;
        // unit test breaks if ZF version > 1.x
        for ($i=0; $i <= 1; $i++) {
            for ($j=0; $j < 10; $j++) {
                for ($k=0; $k < 20; $k++) {
                    foreach (array('PR', 'dev', 'alpha', 'beta', 'RC', 'RC1', 'RC2', 'RC3', '', 'pl') as $rel) {
                        $ver = "$i.$j.$k$rel";
                        if ($ver === Zym_Version::VERSION
                            || "$i.$j.$k-$rel" === Zym_Version::VERSION
                            || "$i.$j.$k.$rel" === Zym_Version::VERSION
                            || "$i.$j.$k $rel" === Zym_Version::VERSION) {

                            if ($expect != -1) {
                                $this->fail("Unexpected double match for Zym_Version::VERSION ("
                                    . Zym_Version::VERSION . ")");
                            }
                            else {
                                $expect = 1;
                            }
                        } else {
                            $this->assertSame(Zym_Version::compareVersion($ver), $expect,
                                "For version '$ver' and Zym_Version::VERSION = '"
                                . Zym_Version::VERSION . "': result=" . (Zym_Version::compareVersion($ver))
                                . ', but expected ' . $expect);
                        }
                    }
                }
            }
        }
        
        if ($expect === -1) {
            $this->fail('Unable to recognize Zym_Version::VERSION ('. Zym_Version::VERSION . ')');
        }
    }
}