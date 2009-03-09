<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Navigation
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Zym_Navigation_Container
 *
 * Container class for Zym_Navigation_Page classes.
 *
 * @author     Robin Skoglund
 * @category   Zym
 * @package    Zym_Navigation
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
abstract class Zym_Navigation_Container
    implements RecursiveIterator, Countable
{
    /**
     * Contains sub pages
     *
     * @var array
     */
    private $_pages = array();

    /**
     * An index that contains the order in which to iterate pages
     *
     * @var array
     */
    private $_index = array();

    /**
     * Whether index is dirty and needs to be re-arranged
     *
     * @var bool
     */
    private $_dirtyIndex = false;

    // Internal methods:

    /**
     * Sorts the page index according to page order
     *
     * @return void
     */
    private function _sort()
    {
        if ($this->_dirtyIndex) {
            $newIndex = array();
            $index = 0;

            foreach ($this->_pages as $hash => $page) {
                $order = $page->getOrder();
                if ($order === null) {
                    $newIndex[$hash] = $index;
                    $index++;
                } else {
                    $newIndex[$hash] = $order;
                }
            }

            asort($newIndex);
            $this->_index = $newIndex;
            $this->_dirtyIndex = false;
        }
    }

    // Public methods:

    /**
     * Notifies container that the order of pages are updated
     *
     * @return void
     */
    public function notifyOrderUpdated()
    {
        $this->_dirtyIndex = true;
    }

    /**
     * Adds a page to the container
     *
     * This method will inject the container as the given page's parent by
     * calling {@link Zym_Navigation_Page::setParent()}.
     *
     * @param  Zym_Navigation_Page|array|Zend_Config $page  page to add
     * @return Zym_Navigation_Container                     fluent interface,
     *                                                      returns self
     * @throws InvalidArgumentException                     if page is invalid
     */
    public function addPage($page)
    {
        if (is_array($page) || $page instanceof Zend_Config) {
            require_once 'Zym/Navigation/Page.php';
            $page = Zym_Navigation_Page::factory($page);
        } elseif (!$page instanceof Zym_Navigation_Page) {
            $msg = '$page must be Zym_Navigation_Page|array|Zend_Config';
            throw new InvalidArgumentException($msg);
        }

        $hash = $page->hashCode();

        if (array_key_exists($hash, $this->_index)) {
            // page is already in container
            return $this;
        }

        // adds page to container and sets dirty flag
        $this->_pages[$hash] = $page;
        $this->_index[$hash] = $page->getOrder();
        $this->_dirtyIndex = true;

        // inject self as page parent
        $page->setParent($this);

        return $this;
    }

    /**
     * Adds several pages at once
     *
     * @param  array|Zend_Config $pages  pages to add
     * @return Zym_Navigation_Container  fluent interface, returns self
     * @throws InvalidArgumentException  if $pages is not array or Zend_Config
     */
    public function addPages($pages)
    {
        if ($pages instanceof Zend_Config) {
            $pages = $pages->toArray();
        }

        if (!is_array($pages)) {
            $msg = '$pages must be an array or a Zend_Config object';
            throw new InvalidArgumentException($msg);
        }

        foreach ($pages as $page) {
            $this->addPage($page);
        }

        return $this;
    }

    /**
     * Sets pages this container should have, clearing existing ones
     *
     * @param  array $pages              pages to set
     * @return Zym_Navigation_Container  fluent interface, returns self
     */
    public function setPages(array $pages)
    {
        $this->removePages();
        return $this->addPages($pages);
    }

    /**
     * Returns pages in the container
     *
     * @return array  array og Zym_Navigation_Page instances
     */
    public function getPages()
    {
        return $this->_pages;
    }

    /**
     * Removes the given page from the container
     *
     * @param  Zym_Navigation_Page|int $page  page to remove, either a page
     *                                        instance or a specific page order
     * @return bool                           whether the removal was successful
     */
    public function removePage($page)
    {
        if ($page instanceof Zym_Navigation_Page) {
            $hash = $page->hashCode();
        } elseif (is_int($page)) {
            $this->_sort();
            if (!$hash = array_search($page, $this->_index)) {
                return false;
            }
        } else {
            return false;
        }

        if (isset($this->_pages[$hash])) {
            unset($this->_pages[$hash]);
            unset($this->_index[$hash]);
            $this->_dirtyIndex = true;
            return true;
        }

        return false;
    }

    /**
     * Removes all pages in container
     *
     * @return Zym_Navigation_Container  fluent interface, returns self
     */
    public function removePages()
    {
        $this->_pages = array();
        $this->_index = array();
        return $this;
    }

    /**
     * Checks if the container has the given page
     *
     * @param  Zym_Navigation_Page $page       page to look for
     * @param  bool                $recursive  [optional] whether to search
     *                                         recursively. Default is false.
     * @return bool                            whether page is in container
     */
    public function hasPage(Zym_Navigation_Page $page, $recursive = false)
    {
        if (array_key_exists($page->hashCode(), $this->_index)) {
            return true;
        } elseif ($recursive) {
            foreach ($this->_pages as $childPage) {
                if ($childPage->hasPage($page, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns true if container contains any pages
     *
     * @return bool  whether container has any pages
     */
    public function hasPages()
    {
        return count($this->_index) > 0;
    }

    /**
     * Returns a child page matching $property == $value, or null if not found
     *
     * @param  string $property          name of property to match against
     * @param  mixed  $value             value to match property against
     * @return Zym_Navigation_Page|null  matching page or null
     */
    public function findOneBy($property, $value)
    {
        $iterator = new RecursiveIteratorIterator($this,
                            RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $page) {
            if ($page->get($property) == $value) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Returns all child pages matching $property == $value, or an empty array
     * if no pages are found
     *
     * @param  string $property  name of property to match against
     * @param  mixed  $value     value to match property against
     * @return array             array containing only Zym_Navigation_Page
     *                           instances
     */
    public function findAllBy($property, $value)
    {
        $found = array();

        $iterator = new RecursiveIteratorIterator($this,
                            RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $page) {
            if ($page->get($property) == $value) {
                $found[] = $page;
            }
        }

        return $found;
    }

    /**
     * Returns page(s) matching $property == $value
     *
     * @param string $property  name of property to match against
     * @param mixed  $value     value to match property against
     * @param bool   $all       [optional] whether an array of all matching
     *                          pages should be returned, or only the first.
     *                          If true, an array will be returned, even if not
     *                          matching pages are found. If false, null will be
     *                          returned if no matching page is found. Default
     *                          is false.
     * @return Zym_Navigation_Page|null  matching page or null
     */
    public function findBy($property, $value, $all = false)
    {
        if ($all) {
            return $this->findAllBy($property, $value);
        } else {
            return $this->findOneBy($property, $value);
        }
    }

    /**
     * Magic overload: Proxy calls to finder methods
     *
     * Examples of finder calls:
     * <code>
     * // METHOD                    // SAME AS
     * $nav->findByLabel('foo');    // $nav->findOneBy('label', 'foo');
     * $nav->findOneByLabel('foo'); // $nav->findOneBy('label', 'foo');
     * $nav->findAllByClass('foo'); // $nav->findAllBy('class', 'foo');
     * </code>
     *
     * @param  string $method          method name
     * @param  array  $arguments       method arguments
     * @throws BadMethodCallException  if method does not exist
     */
    public function __call($method, $arguments)
    {
        if (@preg_match('/(find(?:One|All)?By)(.+)/', $method, $match)) {
            return $this->{$match[1]}($match[2], $arguments[0]);
        }

        $msg = sprintf('Unknown method %s::%s', get_class($this), $method);
        throw new BadMethodCallException($msg);
    }

    /**
     * Returns an array representation of all pages in container
     *
     * @return array
     */
    public function toArray()
    {
        $pages = array();

        foreach ($this->_pages as $page) {
            $pages[] = $page->toArray();
        }

        return $pages;
    }

    // RecursiveIterator interface:

    /**
     * Returns current page
     *
     * Implements RecursiveIterator interface.
     *
     * @return Zym_Navigation_Page   current page or null
     * @throws OutOfBoundsException  if the index is invalid
     */
    public function current()
    {
        $this->_sort();
        current($this->_index);
        $hash = key($this->_index);

        if (isset($this->_pages[$hash])) {
            return $this->_pages[$hash];
        } else {
            $msg = 'Corruption detected in container; '
                 . 'invalid key found in internal iterator';
            throw new OutOfBoundsException($msg);
        }
    }

    /**
     * Returns hash code of current page
     *
     * Implements RecursiveIterator interface.
     *
     * @return string  hash code of current page
     */
    public function key()
    {
        $this->_sort();
        return key($this->_index);
    }

    /**
     * Moves index pointer to next page in the container
     *
     * Implements RecursiveIterator interface.
     *
     * @return void
     */
    public function next()
    {
        $this->_sort();
        next($this->_index);
    }

    /**
     * Sets index pointer to first page in the container
     *
     * Implements RecursiveIterator interface.
     *
     * @return void
     */
    public function rewind()
    {
        $this->_sort();
        reset($this->_index);
    }

    /**
     * Checks if container index is valid
     *
     * Implements RecursiveIterator interface.
     *
     * @return bool
     */
    public function valid()
    {
        $this->_sort();
        return current($this->_index) !== false;
    }

    /**
     * Proxy to hasPages()
     *
     * Implements RecursiveIterator interface.
     *
     * @return bool  whether container has any pages
     */
    public function hasChildren()
    {
        return $this->hasPages();
    }

    /**
     * Returns the child container.
     *
     * Implements RecursiveIterator interface.
     *
     * @return Zym_Navigation_Page|null
     */
    public function getChildren()
    {
        $hash = key($this->_index);

        if (isset($this->_pages[$hash])) {
            return $this->_pages[$hash];
        }

        return null;
    }

    // Countable interface:

    /**
     * Returns number of pages in container
     *
     * Implements Countable interface.
     *
     * @return int  number of pages in the container
     */
    public function count()
    {
        return count($this->_index);
    }
}