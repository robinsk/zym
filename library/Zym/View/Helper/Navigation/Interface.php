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
 * Interface for navigational helpers
 *
 * @category   Zym
 * @package    Zym_View
 * @subpackage Helper_Navigation
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
interface Zym_View_Helper_Navigation_Interface
{
    /**
     * Sets navigation container the helper should operate on by default
     *
     * @param  Zym_Navigation_Container $container          [optional] container
     *                                                      to operate on.
     *                                                      Default is null,
     *                                                      which indicates that
     *                                                      the container should
     *                                                      be reset.
     * @return Zym_View_Helper_Navigation_NavigationHelper  fluent interface,
     *                                                      returns self
     */
    public function setContainer(Zym_Navigation_Container $container = null);

    /**
     * Returns the navigation container helper operates on by default
     *
     * @return Zym_Navigation_Container  navigation container
     */
    public function getContainer();

    /**
     * Renders helper
     *
     * @param  Zym_Navigation_Container $container  [optional] container to
     *                                              render. Default is null,
     *                                              which indicates that the
     *                                              helper should render the
     *                                              container returned by
     *                                              {@link getContainer()}.
     * @return string                               helper output
     */
    public function render(Zym_Navigation_Container $container = null);
}