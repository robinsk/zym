<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Navigation
 */
require_once 'Zym/Navigation.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Tests the class Zym_Navigation_Container
 * 
 * @author    Robin Skoglund
 * @category  Zym_Tests
 * @package   Zym_Navigation
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Navigation_ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     * 
     */
    protected function setUp()
    {
        
    }
    
    /**
     * Tear down the environment after running a test
     *
     */
    protected function tearDown()
    {
        
    }
    
    /**
     * Iterating a container should be done in the order specified by pages
     *
     */
    public function testIteratorShouldBeOrderAware()
    {
        $nav = new Zym_Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'position' => -1
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 4',
                'uri' => '#',
                'position' => 100
            ),
            array(
                'label' => 'Page 5',
                'uri' => '#'
            )
        ));
        
        $order = array();
        $expected = array('Page 2', 'Page 1', 'Page 3', 'Page 5', 'Page 4');
        foreach ($nav as $page) {
            $order[] = $page->getLabel();
        }
        $this->assertEquals($expected, $order);
    }
    
    /**
     * Iterating a container should be done in the order specified by pages
     *
     */
    public function testRecursiveIteration()
    {
        $nav = new Zym_Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#',
                'pages' => array(
                    array(
                        'label' => 'Page 1.1',
                        'uri' => '#',
                        'pages' => array(
                            array(
                                'label' => 'Page 1.1.1',
                                'uri' => '#'
                            ),
                            array(
                                'label' => 'Page 1.1.2',
                                'uri' => '#'
                            )
                        )
                    ),
                    array(
                        'label' => 'Page 1.2',
                        'uri' => '#'
                    )
                )
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'pages' => array(
                    array(
                        'label' => 'Page 2.1',
                        'uri' => '#'
                    )
                )
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            )
        ));
        
        $order = array();
        $expected = array(
            'Page 1',
            'Page 1.1',
            'Page 1.1.1',
            'Page 1.1.2',
            'Page 1.2',
            'Page 2',
            'Page 2.1',
            'Page 3'
        );
        
        $iterator = new RecursiveIteratorIterator($nav,
            RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $page) {
            $order[] = $page->getLabel();
        }
        $this->assertEquals($expected, $order);
    }
    
    /**
     * When setting position for a page, the container order should be updated
     *
     */
    public function testSettingPagePositionShouldUpdateContainerOrder()
    {
        $nav = new Zym_Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));
        
        $page3 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 3',
            'uri' => '#'
        ));
        
        $nav->addPage($page3);
        
        $order = array();
        $orderExpected = array('Page 1', 'Page 2', 'Page 3');
        foreach ($nav as $page) {
            $order[] = $page->getLabel();
        }
        $this->assertEquals($orderExpected, $order);
        
        $page3->setPosition(-1);
        
        $order = array();
        $orderExpected = array('Page 3', 'Page 1', 'Page 2');
        foreach ($nav as $page) {
            $order[] = $page->getLabel();
        }
        $this->assertEquals($orderExpected, $order);
    }
    
    /**
     * Should be able to add a page using an array
     *
     */
    public function testAddPageShouldWorkWithArray()
    {
        $pageOptions = array(
            'label' => 'From array',
            'uri' => '#array'
        );
        
        $nav = new Zym_Navigation();
        $nav->addPage($pageOptions);
        
        $this->assertEquals(1, count($nav));
    }
    
    /**
     * Should be able to add a page using a Zend_Config object 
     *
     */
    public function testAddPageShouldWorkWithConfig()
    {
        $pageOptions = array(
            'label' => 'From config',
            'uri' => '#config'
        );
        
        $pageOptions = new Zend_Config($pageOptions);
        
        $nav = new Zym_Navigation();
        $nav->addPage($pageOptions);
        
        $this->assertEquals(1, count($nav));
    }
    
    /**
     * Should be able to add an actual page instance to a container
     *
     */
    public function testAddPageShouldWorkWithPageInstance()
    {
        $pageOptions = array(
            'label' => 'From array 1',
            'uri' => '#array'
        );
        
        $nav = new Zym_Navigation(array($pageOptions));
        
        $page = Zym_Navigation_Page::factory($pageOptions);
        $nav->addPage($page);
        
        $this->assertEquals(2, count($nav));
    }
    
    /**
     * Should be able to add several pages from an array
     *
     */
    public function testAddPagesShouldWorkWithArray()
    {
        $nav = new Zym_Navigation();
        $nav->addPages(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )
        ));
        
        $this->assertEquals(2, count($nav));
    }
    
    /**
     * Should be able to add several pages from a Zend_Config object
     *
     */
    public function testAddPagesShouldWorkWithConfig()
    {
        $nav = new Zym_Navigation();
        $nav->addPages(new Zend_Config(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )
        )));
        
        $this->assertEquals(2, count($nav));
    }
    
    /**
     * Should be able to add several pages where each page may be an array,
     * a Zend_Config object or a page instance
     *
     */
    public function testAddPagesShouldWorkWithMixedArray()
    {
        $nav = new Zym_Navigation();
        $nav->addPages(new Zend_Config(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            new Zend_Config(array(
                'label' => 'Page 2',
                'action' => 'index',
                'controller' => 'index'
            )),
            Zym_Navigation_Page::factory(array(
                'label' => 'Page 3',
                'uri' => '#'
            ))
        )));
        
        $this->assertEquals(3, count($nav));
    }
    
    /**
     * Tests removing pages
     *
     */
    public function testShouldBeAbleToRemovePages()
    {
        $nav = new Zym_Navigation();
        $nav->addPages(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));
        
        $nav->removePages();
        
        $this->assertEquals(0, count($nav));
    }
    
    /**
     * Tests (re)setting pages
     *
     */
    public function testShouldBeAbleToSetPages()
    {
        $nav = new Zym_Navigation();
        $nav->addPages(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));
        
        $nav->setPages(array(
            array(
                'label' => 'Page 3',
                'uri' => '#'
            )
        ));
        
        $this->assertEquals(1, count($nav));
    }
    
    /**
     * Should be able to remove a page by giving position
     *
     */
    public function testShouldBeAbleToRemovePageByPosition()
    {
        $nav = new Zym_Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'position' => 32
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 4',
                'uri' => '#'
            )
        ));
        
        $this->assertEquals(true, $nav->removePage(0));
        $this->assertEquals(true, $nav->removePage(32));
        $this->assertEquals(true, $nav->removePage(0));
        $this->assertEquals(false, $nav->removePage(1000));
        
        if ($nav->count() != 1) {
            $this->fail(4 - $nav->count() . ' pages removed, expected 3');
        } elseif ($nav->current()->getLabel() != 'Page 4') {
            $this->fail('Removed page that should not be removed');
        }
    }
    
    /**
     * Should be able to remove a page by giving an instance
     *
     */
    public function testShouldBeAbleToRemovePageByInstance()
    {
        $nav = new Zym_Navigation(array(
            array(
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#'
            )
        ));
        
        $page3 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 3',
            'uri' => '#'
        ));
        
        $page4 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 4',
            'uri' => '#'
        ));
        
        $nav->addPage($page3);
        
        $this->assertEquals(true, $nav->removePage($page3));
        $this->assertEquals(false, $nav->removePage($page4));
    }
    
    /**
     * Should be able to search a container for a specific page
     *
     */
    public function testHasPage()
    {
        $page0 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 0',
            'uri' => '#'
        ));
        
        $page1 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));
        
        $page1_1 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1.1',
            'uri' => '#'
        ));
        
        $page1_2 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1.2',
            'uri' => '#'
        ));
        
        $page1_2_1 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1.2.1',
            'uri' => '#'
        ));
        
        $page1_3 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1.3',
            'uri' => '#'
        ));
        
        $page2 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));
        
        $page3 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 3',
            'uri' => '#'
        ));
        
        $nav = new Zym_Navigation(array($page1, $page2, $page3));
        
        $page1->addPage($page1_1);
        $page1->addPage($page1_2);
        $page1_2->addPage($page1_2_1);
        $page1->addPage($page1_3);
        
        $this->assertEquals(false, $nav->hasPage($page0));
        $this->assertEquals(true, $nav->hasPage($page2));
        $this->assertEquals(false, $nav->hasPage($page1_1));
        $this->assertEquals(true, $nav->hasPage($page1_1, true));
    }
    
    /**
     * Tests the method Zym_Navigation_Container::hasPages()
     *
     */
    public function testHasPages()
    {
        $nav1 = new Zym_Navigation();
        $nav2 = new Zym_Navigation();
        $nav2->addPage(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));
        
        $this->assertEquals(false, $nav1->hasPages());
        $this->assertEquals(true, $nav2->hasPages());
    }
    
    /**
     * Should be able to use setParent() with other regular containers
     *
     */
    public function testSetParentShouldWorkWithContainer()
    {
        $nav1 = new Zym_Navigation();
        $nav2 = new Zym_Navigation();
        
        $nav2->setParent($nav1);
        
        $this->assertEquals($nav1, $nav2->getParent());
    }
    
    /**
     * Should be able to use setParent() with page instances
     *
     */
    public function testSetParentShouldWorkWithPage()
    {
        $page1 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));
        
        $page2 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));
        
        $page2->setParent($page1);
        
        $this->assertEquals($page1, $page2->getParent());
        $this->assertEquals(true, $page1->hasPages());
    }
    
    /**
     * Should be able to null out parent
     *
     */
    public function testSetParentShouldWorkWithNull()
    {
        $page1 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));
        
        $page2 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));
        
        $page2->setParent($page1);
        $page2->setParent(null);
        
        $this->assertEquals(null, $page2->getParent());
    }
    
    /**
     * When setting new parent for page that already has a parent, it should
     * be removed from the old parent
     *
     */
    public function testSetParentShouldRemoveFromOldParentPage()
    {
        $page1 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 1',
            'uri' => '#'
        ));
        
        $page2 = Zym_Navigation_Page::factory(array(
            'label' => 'Page 2',
            'uri' => '#'
        ));
        
        $page2->setParent($page1);
        $page2->setParent(null);
        
        $this->assertEquals(null, $page2->getParent());
        $this->assertEquals(false, $page1->hasPages());
    }
    
    /**
     * It should be possible to use custom properties in finder methods
     *
     */
    public function testFinderMethodsShouldWorkWithCustomProperties()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findOneBy('page2', 'page2');
        $this->assertType('Zym_Navigation_Page', $found);
        $this->assertSame('Page 2', $found->getLabel());
    }
    
    /**
     * The findOneBy() method should only return one page or null
     *
     */
    public function testFindOneByShouldReturnOnlyOnePage()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findOneBy('id', 'page_2_and_3');
        $this->assertType('Zym_Navigation_Page', $found);
    }
    
    /**
     * The findOneBy() method should return null if no matching page is found
     *
     */
    public function testFindOneByShouldReturnNullIfNotFound()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findOneBy('id', 'non-existant');
        $this->assertNull($found);
    }
    
    /**
     * The findAllBy() method should return all matching pages
     *
     */
    public function testFindAllByShouldReturnAllMatchingPages()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findAllBy('id', 'page_2_and_3');
        $this->assertType('array', $found, 'array not returned');
        $this->assertSame(2, count($found), 'found more/less than 2 pages');
        $this->assertContainsOnly('Zym_Navigation_Page', $found, false);
    }
    
    /**
     * The findAllBy() method should return an empty array if no matching pages
     * are found
     *
     */
    public function testFindAllByShouldReturnEmptyArrayifNotFound()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findAllBy('id', 'non-existant');
        $this->assertType('array', $found, 'array not returned');
        $this->assertSame(0, count($found), 'array is not empty');
    }
    
    /**
     * The findBy() method should default to findOneBy()
     *
     */
    public function testFindByShouldDefaultToFindOneBy()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findBy('id', 'page_2_and_3');
        $this->assertType('Zym_Navigation_Page', $found);
    }
    
    /**
     * It should be possible to use methods like findById(), findByClass(),
     * findOneByLabel(), findAllByClass(), and so on.
     *
     */
    public function testShouldBeAbleToUseMagicFinderMethods()
    {
        $nav = $this->_getFindByNavigation();
        
        $found = $nav->findById('non-existant');
        $this->assertNull($found);
        
        $found = $nav->findById('page_2_and_3');
        $this->assertType('Zym_Navigation_Page', $found);
        
        $found = $nav->findAllById('page_2_and_3');
        $this->assertType('array', $found, 'array not returned');
        $this->assertSame(2, count($found), 'found more/less than 2 pages');
        $this->assertContainsOnly('Zym_Navigation_Page', $found, false);
        
        $found = $nav->findAllByaction('about');;
        $this->assertSame(2, count($found));
    }
    
    /**
     * Returns navigation object for the findBy methods
     *
     * @return Zym_Navigation
     */
    protected function _getFindByNavigation()
    {
        // findAllByFoo('bar')         // Page 1, Page 1.1 
        // findById('page_2_and_3')    // Page 2 
        // findOneById('page_2_and_3') // Page 2
        // findAllById('page_2_and_3') // Page 2, Page 3
        // findAllByAction('about')    // Page 1.3, Page 3
        return new Zym_Navigation(array(
            array(
                'label' => 'Page 1',
                'uri'   => 'page-1',
                'foo'   => 'bar',
                'pages' => array(
                    array(
                        'label' => 'Page 1.1',
                        'uri'   => 'page-1.1',
                        'foo'   => 'bar',
                        'title' => 'The given title'
                    ),
                    array(
                        'label' => 'Page 1.2',
                        'uri'   => 'page-1.2',
                        'title' => 'The given title'
                    ),
                    array(
                        'type'   => 'uri',
                        'label'  => 'Page 1.3',
                        'uri'    => 'page-1.3',
                        'title'  => 'The given title',
                        'action' => 'about'
                    )
                )
            ),
            array(
                'id'         => 'page_2_and_3',
                'label'      => 'Page 2',
                'module'     => 'page2',
                'controller' => 'index',
                'action'     => 'page1',
                'page2'      => 'page2'
            ),
            array(
                'id'         => 'page_2_and_3',
                'label'      => 'Page 3',
                'module'     => 'page3',
                'controller' => 'index',
                'action'     => 'about'
            )
        ));
    }
}
