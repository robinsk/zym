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
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * Imports
 *
 * @see Zym_View_Helper_Navigation_TestAbstract
 * @see Zym_View_Helper_Links
 */
require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zym/View/Helper/Navigation/Links.php';

/**
 * Tests Zym_View_Helper_Navigation_Links
 *
 * @author     Robin Skoglund
 * @category   Zym_Tests
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Navigation_LinksTest
    extends Zym_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zym_View_Helper_Navigation_Links';

    /**
     * View helper
     *
     * @var Zym_View_Helper_Navigation_Links
     */
    protected $_helper;

    public function setUp()
    {
        parent::setUp();

        // disable all active pages
        foreach ($this->_helper->findAllByActive(true) as $page) {
            $page->active = false;
        }
    }

    public function testDoNotRenderIfNoPageIsActive()
    {
        $this->assertEquals('', $this->_helper->render());
    }

    public function testDetectRelationFromStringPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->example = 'http://www.example.com/';
        $found = $this->_helper->findRelationFromPage($active, 'example');
        unset($active->example);

        $expected = array(
            'Zym_Navigation_Page_Uri',
            'http://www.example.com/',
            null
        );

        $actual = array(
            get_class($found),
            $found->getHref(),
            $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromPageInstancePropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->example = Zym_Navigation_Page::factory(array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        ));
        $found = $this->_helper->findRelationFromPage($active, 'example');
        unset($active->example);

        $expected = array(
            'Zym_Navigation_Page_Uri',
            'http://www.example.com/',
            'An example page'
        );

        $actual = array(
            get_class($found),
            $found->getHref(),
            $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromArrayPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->example = array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        );
        $found = $this->_helper->findRelationFromPage($active, 'example');
        unset($active->example);

        $expected = array(
            'Zym_Navigation_Page_Uri',
            'http://www.example.com/',
            'An example page'
        );

        $actual = array(
            get_class($found),
            $found->getHref(),
            $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectRelationFromConfigInstancePropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->example = new Zend_Config(array(
            'uri' => 'http://www.example.com/',
            'label' => 'An example page'
        ));
        $found = $this->_helper->findRelationFromPage($active, 'example');
        unset($active->example);

        $expected = array(
            'Zym_Navigation_Page_Uri',
            'http://www.example.com/',
            'An example page'
        );

        $actual = array(
            get_class($found),
            $found->getHref(),
            $found->getLabel()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testDetectMultipleRelationsFromArrayPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');

        $active->alternate = array(
            array(
                'label' => 'foo',
                'uri'   => 'bar'
            ),
            array(
                'label' => 'baz',
                'uri'   => 'bat'
            )
        );

        $found = $this->_helper->findAlternate($active);
        unset($active->alternate);

        $expected = array('array', 2);
        $actual = array(gettype($found), count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testDetectMultipleRelationsFromConfigPropertyOfActivePage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');

        $active->alternate = new Zend_Config(array(
            array(
                'label' => 'foo',
                'uri'   => 'bar'
            ),
            array(
                'label' => 'baz',
                'uri'   => 'bat'
            )
        ));

        $found = $this->_helper->findAlternate($active);
        unset($active->alternate);

        $expected = array('array', 2);
        $actual = array(gettype($found), count($found));
        $this->assertEquals($expected, $actual);
    }

    public function testExtractingRelationsFromPageProperties()
    {
        $relations = array(
            'alternate', 'stylesheet', 'start', 'next', 'prev', 'contents',
            'index', 'glossary', 'copyright', 'chapter', 'section', 'subsection',
            'appendix', 'help', 'bookmark'
        );

        $samplePage = Zym_Navigation_Page::factory(array(
            'label' => 'An example page',
            'uri'   => 'http://www.example.com/'
        ));

        $active = $this->_helper->findOneByLabel('Page 2');
        $expected = array();
        $actual = array();

        foreach ($relations as $relation) {
            $active->$relation = $samplePage;

            $method = 'find' . ucfirst($relation);
            $expected[$relation] = $samplePage->getLabel();
            $actual[$relation]   = $this->_helper->$method($active)->getLabel();

            unset($active->$relation);
        }

        $this->assertEquals($expected, $actual);
    }

    public function testShouldFindStartPageByTraversal()
    {
        $active = $this->_helper->findOneByLabel('Page 2.1');
        $expected = 'Home';
        $actual = $this->_helper->findStart($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testNotFindStartWhenGivenPageIsTheFirstPage()
    {
        $active = $this->_helper->findOneByLabel('Home');
        $actual = $this->_helper->findStart($active);
        $this->assertNull($actual, 'Should not find any start page');
    }

    public function testFindNextPageByTraversalShouldFindChildPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $expected = 'Page 2.1';
        $actual = $this->_helper->findNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindNextPageByTraversalShouldFindSiblingPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2.1');
        $expected = 'Page 2.2';
        $actual = $this->_helper->findNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindNextPageByTraversalShouldWrap()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.2');
        $expected = 'Page 2.3';
        $actual = $this->_helper->findNext($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldFindParentPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2.1');
        $expected = 'Page 2';
        $actual = $this->_helper->findPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldFindSiblingPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $expected = 'Page 2.1';
        $actual = $this->_helper->findPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testFindPrevPageByTraversalShouldWrap()
    {
        $active = $this->_helper->findOneByLabel('Page 2.3');
        $expected = 'Page 2.2.2';
        $actual = $this->_helper->findPrev($active)->getLabel();
        $this->assertEquals($expected, $actual);
    }

    public function testShouldFindChaptersFromFirstLevelOfPagesInContainer()
    {
        $active = $this->_helper->findOneByLabel('Page 2.3');
        $found = $this->_helper->findChapter($active);

        $expected = array('Page 1', 'Page 2', 'Page 3', 'Zym');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindingChaptersShouldExcludeSelfIfChapter()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $found = $this->_helper->findChapter($active);

        $expected = array('Page 1', 'Page 3', 'Zym');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFindSectionsWhenActiveChapterPage()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $found = $this->_helper->findSection($active);
        $expected = array('Page 2.1', 'Page 2.2', 'Page 2.3');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindSectionsWhenActivePageIsASection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $found = $this->_helper->findSection($active);
        $this->assertNull($found);
    }

    public function testDoNotFindSectionsWhenActivePageIsASubsection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.1');
        $found = $this->_helper->findSection($active);
        $this->assertNull($found);
    }

    public function testFindSubsectionWhenActivePageIsSection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2');
        $found = $this->_helper->findSubsection($active);

        $expected = array('Page 2.2.1', 'Page 2.2.2');
        $actual = array();
        foreach ($found as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testDoNotFindSubsectionsWhenActivePageIsASubSubsection()
    {
        $active = $this->_helper->findOneByLabel('Page 2.2.1');
        $found = $this->_helper->findSubsection($active);
        $this->assertNull($found);
    }

    public function testDoNotFindSubsectionsWhenActivePageIsAChapter()
    {
        $active = $this->_helper->findOneByLabel('Page 2');
        $found = $this->_helper->findSubsection($active);
        $this->assertNull($found);
    }

    public function testAddingAndFindingCustomRelations()
    {
        $this->_helper->addCustomRelation('up');
        $active = $this->_helper->findOneByLabel('Page 2');
        $active->up = 'http://www.example.com/';

        $expected = array('up' => array('http://www.example.com/'));
        $actual = $this->_helper->findCustomRelations($active);
        $actual['up'][0] = $actual['up'][0]->getHref();
        unset($active->up);
        $this->_helper->removeCustomRelation('up');

        $this->assertEquals($expected, $actual);
    }
}
