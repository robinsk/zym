<?php
/**
 * Zym Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_View_Helper_Navigation_Abstract
 */
require_once 'Zym/View/Helper/Navigation/Abstract.php';

/**
 * Helper for printing <link> elements
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_View_Helper_Navigation_Links
    extends Zym_View_Helper_Navigation_Abstract
{
    /**#@+
     * Render constants
     * 
     * @see setRenderFlag() 
     * @var int
     */
    const RENDER_ALTERNATE  = 0x01;
    const RENDER_STYLESHEET = 0x02;
    const RENDER_START      = 0x04;
    const RENDER_NEXT       = 0x08;
    const RENDER_PREV       = 0x10;
    const RENDER_CONTENTS   = 0x20;
    const RENDER_INDEX      = 0x40;
    const RENDER_GLOSSARY   = 0x80;
    const RENDER_COPYRIGHT  = 0x100;
    const RENDER_CHAPTER    = 0x200;
    const RENDER_SECTION    = 0x400;
    const RENDER_SUBSECTION = 0x800;
    const RENDER_APPENDIX   = 0x1000;
    const RENDER_HELP       = 0x2000;
    const RENDER_BOOKMARK   = 0x4000;
    const RENDER_CUSTOM     = 0x8000;
    const RENDER_ALL        = 0xffff;
    /**#@+**/
    
    /**#@+
     * Render attribute constants
     * 
     * @see setReleationAttribute()
     * @var string
     */
    const REL = 'rel';
    const REV = 'rev';
    /**#@+**/
    
    /**
     * Valid relation attributes
     * 
     * @var array
     */
    private static $_ATTRIBS = array(self::REL, self::REV);
    
    /**
     * Valid valid relations
     * 
     * @var array
     */
    private static $_RELATIONS = array(
        self::RENDER_ALTERNATE  => 'alternate',
        self::RENDER_STYLESHEET => 'stylesheet',
        self::RENDER_START      => 'start',
        self::RENDER_NEXT       => 'next',
        self::RENDER_PREV       => 'prev',
        self::RENDER_CONTENTS   => 'contents',
        self::RENDER_INDEX      => 'index',
        self::RENDER_GLOSSARY   => 'glossary',
        self::RENDER_COPYRIGHT  => 'copyright',
        self::RENDER_CHAPTER    => 'chapter',
        self::RENDER_SECTION    => 'section',
        self::RENDER_SUBSECTION => 'subsection',
        self::RENDER_APPENDIX   => 'appendix',
        self::RENDER_HELP       => 'help',
        self::RENDER_BOOKMARK   => 'bookmark'
    );
    
    /**
     * Relation attribute to use for links (rel or rev)
     * 
     * @var string
     */
    protected $_relationAttribute = self::REL;
    
    /**
     * The helper's render flag
     * 
     * @see render()
     * @see setRenderFlag()
     * @var int
     */
    protected $_renderFlag = self::RENDER_ALL;
    
    /**
     * Custom relations to find and render
     * 
     * @var array
     */
    protected $_customRelations = array();
    
    /**
     * Root container
     * 
     * Used for preventing finder methods to traverse above the container given
     * to the {@link render()} method.
     * 
     * @var Zym_Navigation_Container
     */
    protected $_root;
    
    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              operate on
     * @return Zym_View_Helper_Navigation_Links     fluent interface, returns
     *                                              self
     */
    public function links(Zym_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }
    
    // Accessors:
    
    /**
     * Sets the relation attribute that is used for links (rel or rev)
     * 
     * @param  string $relationAttribute         relation attribute to use for 
     *                                           links (rel or rev)
     * @return Zym_View_Helper_Navigation_Links  fluent interface, returns self
     * @throws Zend_View_Exception               if attribute is invalid
     */
    public function setRelationAttribute($relationAttribute)
    {
        if (!in_array($relationAttribute, self::$_ATTRIBS)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(sprintf(
                    'Invalid relation attribute "%s", must be one of; %s',
                    $relation,
                    implode(', ', self::$_ATTRIBS)));
        }
        
        $this->_relationAttribute = $relationAttribute;
    }
    
    /**
     * Returns the relation attribute that is used for links (rel or rev)
     * 
     * @return string  relation attribute (rel or rev)
     */
    public function getRelationAttribute()
    {
        return $this->_relationAttribute;
    }
    
    /**
     * Sets the helper's render flag
     * 
     * The helper uses the bitwise '&' operator against the hex values of the
     * render constants. This means that the flag can is "bitwised" value of
     * the render constants. Examples:
     * <code>
     * // render all links except glossary
     * $flag = Zym_View_Helper_Navigation_Links:RENDER_ALL ^
     *         Zym_View_Helper_Navigation_Links:RENDER_GLOSSARY;
     * $helper->setRenderFlag($flag);
     * 
     * // render only chapters and sections
     * $flag = Zym_View_Helper_Navigation_Links:RENDER_CHAPTER |
     *         Zym_View_Helper_Navigation_Links:RENDER_SECTION;
     * $helper->setRenderFlag($flag);
     * 
     * // render only relations added with {@link addCustomRelation()}
     * $helper->setRenderFlag(Zym_View_Helper_Navigation_Links:RENDER_CUSTOM);
     * 
     * // render all relations (default)
     * $helper->setRenderFlag(Zym_View_Helper_Navigation_Links:RENDER_ALL);
     * </code>
     * 
     * Note that custom relations can also be rendered directly using one of;
     * {@link renderLink()}, {@link renderRel()}, or {@link renderRev()}.
     * 
     * @param  int $renderFlag                   render flag
     * @return Zym_View_Helper_Navigation_Links  fluent interface, returns self
     */
    public function setRenderFlag($renderFlag)
    {
        $this->_renderFlag = (int) $renderFlag;
        return $this;
    }
    
    /**
     * Returns the helper's render flag
     * 
     * @return int  render flag
     */
    public function getRenderFlag()
    {
        return $this->_renderFlag;
    }
    
    /**
     * Sets custom relations to find and render
     * 
     * @param  array $relations                  custom relations to find
     * @return Zym_View_Helper_Navigation_Links  fluent interface, returns self
     */
    public function setCustomRelations(array $relations = array())
    {
        $newCustomRelations = array();
        foreach ($relations as $relation) {
            if (is_string($relation)) {
                $newCustomRelations[] = $relation;
            }
        }
        $this->_customRelations = $newCustomRelations;
        
        return $this;
    }
    
    /**
     * Returns custom relations to find and render
     * 
     * @return array  custom relations to find and render
     */
    public function getCustomRelations()
    {
        return $this->_customRelations;
    }
    
    // Finder methods:
    
    /**
     * Finds the deepest active page in a container that is not hidden, or null
     * if such a page doesn't exist
     * 
     * @param  Zym_Navigation_Container $container  container to search
     * @return Zym_Navigation_Page|null             deepest active page or null
     */
    public function findActive(Zym_Navigation_Container $container)
    {
        $found = null;
        $depth = -1;
        $iterator = new RecursiveIteratorIterator($container,
                RecursiveIteratorIterator::CHILD_FIRST);

        // find the deepest active page
        foreach ($iterator as $page) {
            if (!$this->accept($page)) {
                // page is not accepted
                continue;
            }
            
            if ($page->isActive() && $iterator->getDepth() > $depth) {
                // found an active page at a deeper level than before
                $found = $page;
                $depth = $iterator->getDepth();
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'alternate' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Designates substitute versions for the document in which the link occurs.
     * When used together with the lang attribute, it implies a translated 
     * version of the document. When used together with the media attribute, it 
     * implies a version designed for a different medium (or media).
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findAlternate(Zym_Navigation_Page $page)
    {
        return $this->findRelationFromPage($page, 'alternate');
    }
    
    /**
     * Finds the 'stylesheet' relations for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to an external style sheet. See the section on 
     * {@link http://www.w3.org/TR/html4/present/styles.html#style-external 
     * external style sheets} for details. This is used together with the link 
     * type "Alternate" for user-selectable alternate style sheets.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findStylesheet(Zym_Navigation_Page $page)
    {
        return $this->findRelationFromPage($page, 'stylesheet');
    }
    
    /**
     * Finds the 'start' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to the first document in a collection of documents. This link type
     * tells search engines which document is considered by the author to be the
     * starting point of the collection.
     * 
     * @param  Zym_Navigation_Page $page  page to find relation for
     * @return Zym_Navigation_Page|null   page(s) or null
     */
    public function findStart(Zym_Navigation_Page $page)
    {
        if ($found = $this->findRelationFromPage($page, 'start')) {
            // found; make sure it's a single page
            if (is_array($found)) {
                $found = current($found);
            }
        } else {
            $found = $this->_findRoot($page);
            if (!$found instanceof Zym_Navigation_Page) {
                $found->rewind();
                $found = $found->current();
            }
            
            if ($found === $page) {
                $found = null;
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'next' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to the next document in a linear sequence of documents. User 
     * agents may choose to preload the "next" document, to reduce the perceived
     * load time.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findNext(Zym_Navigation_Page $page)
    {
        if (!$found = $this->findRelationFromPage($page, 'next')) {
            if ($page->hasPages()) {
                $page->rewind();
                $found = $page->current();
            } elseif ($parent = $page->getParent()) {
                // nothing found in page itself; traverse
                $prev = null;
                $iterator = new RecursiveIteratorIterator(
                        $this->_findRoot($page),
                        RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($iterator as $intermediate) {
                    if ($intermediate === $page) {
                        $found = $prev;
                        break;
                    }
                    
                    $prev = $intermediate;
                }
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'prev' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to the previous document in an ordered series of documents. Some 
     * user agents also support the synonym "Previous".
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findPrev(Zym_Navigation_Page $page)
    {
        if (!$found = $this->findRelationFromPage($page, 'prev')) {
            // nothing found in page itself; traverse
            $prev = null;
            $iterator = new RecursiveIteratorIterator(
                    $this->_findRoot($page),
                    RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $intermediate) {
                if ($intermediate === $page) {
                    $found = $prev;
                    break;
                }
                
                $prev = $intermediate;
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'contents' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document serving as a table of contents. Some user agents 
     * also support the synonym ToC (from "Table of Contents").
     * 
     * @param  Zym_Navigation_Page $page  page to find relation for
     * @return Zym_Navigation_Page|null   page(s) or null
     */
    public function findContents(Zym_Navigation_Page $page)
    {
        if ($found = $this->findRelationFromPage($page, 'contents')) {
            // make sure only one page is returned
            if (is_array($found)) {
                $found = current($found);
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'index' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document providing an index for the current document.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findIndex(Zym_Navigation_Page $page)
    {
        if ($found = $this->findRelationFromPage($page, 'index')) {
            // make sure only one page is returned
            if (is_array($found)) {
                $found = current($found);
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'glossary' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document providing a glossary of terms that pertain to the 
     * current document.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findGlossary(Zym_Navigation_Page $page)
    {
        if ($found = $this->findRelationFromPage($page, 'glossary')) {
            // make sure only one page is returned
            if (is_array($found)) {
                $found = current($found);
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'copyright' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a copyright statement for the current document.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findCopyright(Zym_Navigation_Page $page)
    {
        if ($found = $this->findRelationFromPage($page, 'copyright')) {
            // make sure only one page is returned
            if (is_array($found)) {
                $found = current($found);
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'chapter' relations for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document serving as a chapter in a collection of documents.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findChapter(Zym_Navigation_Page $page)
    {
        if (!$found = $this->findRelationFromPage($page, 'chapter')) {
            // nothing found in page itself; find first level of pages
            $found = $this->_findRoot($page)->getPages();
            
            foreach ($found as $key => $value) {
                if ($value === $page) {
                    unset($found[$key]);
                }
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the section relations for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document serving as a section in a collection of documents.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findSection(Zym_Navigation_Page $page)
    {
        if (!$found = $this->findRelationFromPage($page, 'section')) {
            // nothing found in page itself; find first level of pages
            $root = $this->_findRoot($page)->getPages();
            
            foreach ($root as $firstLevelPage) {
                if ($firstLevelPage === $page) {
                    $found = $page->getPages();
                    break;
                } elseif ($page->isDescendentOf($firstLevelPage)) {
                    $found = $firstLevelPage->getPages();
                    foreach ($found as $key => $value) {
                        if ($value === $page) {
                            // remove self
                            unset($found[$key]);
                        }
                    }
                    break;
                }
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'subsection' relations for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document serving as a subsection in a collection of 
     * documents.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findSubsection(Zym_Navigation_Page $page)
    {
        if (!$found = $this->findRelationFromPage($page, 'subsection')) {
            // nothing found in page itself; find first level of pages
            $root = $this->_findRoot($page)->getPages();
            
            foreach ($root as $firstLevelPage) {
                foreach ($firstLevelPage as $secondLevelPage) {
                    if ($secondLevelPage === $page) {
                        $found = $page->getPages();
                        break;
                    }
                }
            }
        }
        
        return $found;
    }
    
    /**
     * Finds the 'appendix' relations for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document serving as an appendix in a collection of documents.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findAppendix(Zym_Navigation_Page $page)
    {
        return $this->findRelationFromPage($page, 'appendix');
    }
    
    /**
     * Finds the 'help' relations for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a document offering help (more information, links to other 
     * sources information, etc.)
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findHelp(Zym_Navigation_Page $page)
    {
        if (!$found = $this->findRelationFromPage($page, 'help')) {
            // TODO: implement traversal?
        }
        
        return $found;
    }
    
    /**
     * Finds the 'bookmark' relation for the given $page
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Refers to a bookmark. A bookmark is a link to a key entry point within an
     * extended document. The title attribute may be used, for example, to label
     * the bookmark. Note that several bookmarks may be defined in each
     * document.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    public function findBookmark(Zym_Navigation_Page $page)
    {
        return $this->findRelationFromPage($page, 'bookmark');
    }
    
    /**
     * Finds the custom relations for the given $page
     * 
     * Custom relations to search for are added using
     * {@link addCustomRelation()} or {@link setCustomRelations()}.
     * 
     * From {@link http://www.w3.org/TR/html4/types.html#type-links}:
     * Authors may wish to define additional link types not described in this 
     * specification. If they do so, they should use a profile to cite the 
     * conventions used to define the link types. Please see the profile 
     * attribute of the HEAD element for more details.
     * 
     * @param  Zym_Navigation_Page $page       page to find relation for
     * @return array                           found relatations. Each key in
     *                                         the array corresponds to a
     *                                         relation, and each value is an
     *                                         array of the found pages for
     *                                         the relation
     */
    public function findCustomRelations(Zym_Navigation_Page $page)
    {
        $relations = array();
        
        foreach ($this->getCustomRelations() as $relation) {
            if ($found = $this->findRelationFromPage($page, $relation)) {
                if (is_array($found)) {
                    $relations[$relation] = $found;
                } else {
                    $relations[$relation] = array($found);
                }
            }
        }
        
        return $relations;
    }
    
    // Util methods:
    
    /**
     * Adds a custom relation to find and render
     * 
     * @param  string $relation                  custom relation
     * @return Zym_View_Helper_Navigation_Links  fluent interface, returns self
     */
    public function addCustomRelation($relation)
    {
        if (is_string($relation) &&
            !in_array($relation, $this->_customRelations)) {
            $this->_customRelations[] = $relation;
        }
        
        return $this;
    }
    
    /**
     * Removes a custom relation from finding and rendering
     * 
     * @param  string $relation                  custom relation
     * @return Zym_View_Helper_Navigation_Links  fluent interface, returns self
     */
    public function removeCustomRelation($relation)
    {
        if ($key = array_search($relation, $this->_customRelations)) {
            unset($this->_customRelations[$key]);
        }
        
        return $this;
    }
    
    /**
     * Finds relations of type $relation from the property called $relation
     * of the given $page
     * 
     * @param  Zym_Navigation_Page $page              page to search
     * @param  string              $relation          property to search
     * @return Zym_Navigation_Page|string|array|null  page(s) or null
     */
    public function findRelationFromPage(Zym_Navigation_Page $page, $relation)
    {
        // check if page has the given relation as a property
        if ($found = $page->get($relation)) {
            // extract page(s) from the property
            return $this->_extractPageFromValue($found);
        }
        
        // nothing found
        return null;
    }
    
    /**
     * Extracts page(s) from a mixed value
     * 
     * @param mixed $mixed                     mixed value to get a page from
     * @param bool  $recursive                 whether $value should be looped
     *                                         if it is an array or a config
     * @return Zym_Navigation_Page|array|null  page(s) or null
     */
    protected function _extractPageFromValue($mixed, $recursive = true)
    {
        if (is_object($mixed)) {
            if ($mixed instanceof Zym_Navigation_Page) {
                // value is a page instance; return directly
                return $mixed;
            } elseif ($mixed instanceof Zym_Navigation_Container) {
                // value is a container; return pages directly
                return $mixed->getPages();
            } elseif ($mixed instanceof Zend_Config && $recursive) {
                // value is a config object; sniff some more
                return $this->_extractPageFromLoopableValue($mixed);
            }
        } elseif (is_string($mixed)) {
            // value is a string; make an URI page
            return Zym_Navigation_Page::factory(array(
                'type' => 'uri',
                'uri'  => $mixed
            ));
        } elseif (is_array($mixed) && $recursive) {
            // value is an array; sniff some more
            return $this->_extractPageFromLoopableValue($mixed);
        }
        
        // nothing found
        return null;
    }
    
    /**
     * Extracts page(s) from a mixed loopable value
     * 
     * @param  array|Zend_Config $mixed        mixed value to sniff
     * @return array|Zym_Navigation_Page|null  page(s) or null
     */
    protected function _extractPageFromLoopableValue($mixed)
    {
        if (is_numeric(key($mixed))) {
            // first key is numeric; assume several pages
            $pages = array();
            
            foreach ($mixed as $value) {
                if ($value = $this->_extractPageFromValue($value, false)) {
                    $pages[] = $value;
                }
            }
            
            return $pages;
        } else {
            // first key is not numeric; assume a single page
            try {
                $page = Zym_Navigation_Page::factory($mixed);
                return $page;
            } catch (Exception $e) {
            }
        }
        
        // nothing found
        return null;
    }
    
    /**
     * Returns the root container of the given page
     * 
     * When rendering a container, the render method still store the given
     * container as the root container, and unset it when done rendering. This
     * makes sure finder methods will not traverse above the container given
     * to the render method.
     * 
     * @param  Zym_Navigaiton_Page $page  page to find root for
     * @return Zym_Navigation_Container   the root container of the given page
     */
    private function _findRoot(Zym_Navigation_Page $page)
    {
        if ($this->_root) {
            return $this->_root;
        }
        
        $root = $page;
        
        while ($parent = $page->getParent()) {
            $root = $parent;
            if ($parent instanceof Zym_Navigation_Page) {
                $page = $parent;
            } else {
                break;
            }
        }
        
        return $root;
    }
    
    // Render methods:
    
    /**
     * Renders the given $page as a link element, with $attrib = $relation
     * 
     * @param  Zym_Navigation_Page $page      the page to render the link for
     * @param  string              $attrib    the attribute to use for $type,
     *                                        either 'rel' or 'rev'
     * @param  string              $relation  relation type, muse be one of;
     *                                        alternate, appendix, bookmark,
     *                                        chapter, contents, copyright, 
     *                                        glossary, help, home, index, next,
     *                                        prev, section, start, stylesheet,
     *                                        subsection
     * @return string                         rendered link element
     * @throws Zend_View_Exception            if $attrib is invalid
     */
    public function renderLink(Zym_Navigation_Page $page, $attrib, $relation)
    {
        if (!in_array($attrib, self::$_ATTRIBS)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(sprintf(
                    'Invalid relation attribute "%s", must be one of; %s',
                    $attrib,
                    implode(', ', self::$_ATTRIBS)));
        }
        
        if (!$href = $page->getHref()) {
            return '';
        }
        
        // TODO: add more attribs
        // http://www.w3.org/TR/html401/struct/links.html#h-12.2
        $attribs = array(
            $attrib  => $relation,
            'href'   => $href,
            'title'  => $page->getLabel(),
            'target' => $page->getTarget()
        );
        
        return '<link' .
               $this->_htmlAttribs($attribs) .
               $this->getClosingBracket();
    }
    
    /**
     * Renders the given $page as a link element, with rel = $relation
     * 
     * @param  Zym_Navigation_Page $page      the page to render the link for
     * @param  string              $relation  relation value, muse be one of;
     *                                        alternate, appendix, bookmark,
     *                                        chapter, contents, copyright, 
     *                                        glossary, help, home, index, next,
     *                                        prev, section, start, stylesheet,
     *                                        subsection
     * @return string                         rendered link element
     * @throws Zend_View_Exception            if $attrib or $type is invalid
     */
    public function renderRel(Zym_Navigation_Page $page, $relation)
    {
        return $this->renderLink($page, 'rel', $relation);
    }
    
    /**
     * Renders the given $page as a link element, with rev = $relation
     * 
     * @param  Zym_Navigation_Page $page      the page to render the link for
     * @param  string              $relation  relation value, muse be one of;
     *                                        alternate, appendix, bookmark,
     *                                        chapter, contents, copyright, 
     *                                        glossary, help, home, index, next,
     *                                        prev, section, start, stylesheet,
     *                                        subsection
     * @return string                         rendered link element
     * @throws Zend_View_Exception            if $attrib or $type is invalid
     */
    public function renderRev(Zym_Navigation_Page $page, $relation)
    {
        return $this->renderLink($page, 'rev', $relation);
    }
    
    // Zym_View_Helper_Navigation_Interface:

    /**
     * Renders helper
     * 
     * Implements {@link Zym_View_Helper_Navigation_Interface::render()}.
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is to render
     *                                              the container registered in
     *                                              the helper.
     */
    public function render(Zym_Navigation_Container $container = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }
        
        if (!$active = $this->findActive($container)) {
            // no active page
            return '';
        }
        
        // store root to make sure finder methods don't traverse above it
        $this->_root = $container;
        
        $output = '';
        $indent = $this->getIndent();
        $renderFlag = $this->getRenderFlag();
        $attrib = $this->getRelationAttribute();
        
        // render native relations
        foreach (self::$_RELATIONS as $relationFlag => $relation) {
            // find and render this relation?
            if (!($relationFlag & $renderFlag)) {
                continue;
            }
            
            // method name to call for finding relations
            $finderMethod = 'find' . ucfirst($relation);
            
            // find relations to active page
            if ($found = $this->$finderMethod($active)) {
                if (is_array($found)) {
                    // found several relations
                    foreach ($found as $page) {
                        if ($r = $this->renderLink($page, $attrib, $relation)) {
                            $output .= $indent . $r . self::EOL;
                        }
                    }
                } else {
                    // found one relation
                    if ($r = $this->renderLink($found, $attrib, $relation)) {
                        $output .= $indent . $r . self::EOL;
                    }
                }
            }
        }
        
        // render custom relations?
        if ($renderFlag & self::RENDER_CUSTOM) {
            $found = $this->findCustomRelations($active);
            foreach ($found as $relation => $relations) {
                foreach ($relations as $page) {
                    if ($r = $this->renderLink($page, $attrib, $relation)) {
                        $output .= $indent . $r . self::EOL;
                    }
                }
            }
        }
        
        // unset root
        $this->_root = null;
        
        // return output (trim last newline by spec)
        return strlen($output) ? rtrim($output, self::EOL) : '';
    }
}
