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
 * @package Zym_Js
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license http://www.zym-project.com/license New BSD License
 */

/**
 * @see Zym_Js_Minifier_Iterator
 */
require_once 'Zym/Js/Minifier/Iterator.php';

/**
 * Javascript Minifier
 *
 * A better version of http://code.google.com/p/jsmin-php/
 * Although at the moment, it is slower...
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Js
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Js_Minifier
{
    /**
     * Ord value of line feed
     *
     */
    const ORD_LF    = 10;

    /**
     * Ord value of space
     *
     */
    const ORD_SPACE = 32;

    /**
     * Current char
     *
     * @var string
     */
    protected $_a;

    /**
     * Next char
     *
     * @var string
     */
    protected $_b;

    /**
     * Iterator
     *
     * @var Zym_Js_Minifier_Iterator
     */
    protected $_iterator;

    /**
     * Processed output
     *
     * @var string
     */
    protected $_output;

    /**
     * Construct
     *
     */
    public function __construct()
    {}

    /**
     * Minify Javascript
     *
     * @param  string $javscript
     * @return string
     */
    public function process($javascript)
    {
        // Clean object
        $this->_cleanUp();

        // Convert line endings
        $javascript = str_replace("\r\n", "\n", $javascript);

        // Create iterator
        $this->_iterator = new Zym_Js_Minifier_Iterator($javascript);

        $this->_a = "\n";
        $this->_action(3);

        while ($this->_a !== null) {
            switch ($this->_a) {
                case ' ':
                    if ($this->_isAlphaNumeric($this->_b)) {
                        $this->_action(1);
                    } else {
                        $this->_action(2);
                    }
                    break;

                case "\n":
                    switch ($this->_b) {
                        case '{':
                        case '[':
                        case '(':
                        case '+':
                        case '-':
                            $this->_action(1);
                            break;

                        case ' ':
                            $this->_action(3);
                            break;

                        default:
                            if ($this->_isAlphaNumeric($this->_b)) {
                                $this->_action(1);
                            } else {
                                $this->_action(2);
                            }
                            break;
                    }
                    break;

                default:
                    switch ($this->_b) {
                        case ' ':
                            if ($this->_isAlphaNumeric($this->_a)) {
                                $this->_action(1);
                                break;
                            }

                            $this->_action(3);
                        break;

                        case "\n":
                            switch ($this->_a) {
                                case '}':
                                case ']':
                                case ')':
                                case '+':
                                case '-':
                                case '"':
                                case '\'':
                                    $this->_action(1);
                                    break;

                                default:
                                    if ($this->_isAlphaNumeric($this->_a)) {
                                        $this->_action(1);
                                    } else {
                                        $this->_action(3);
                                    }
                            }
                            break;

                        default :
                            $this->_action(1);
                            break;
                    }
                    break;
            }
        }

        return $this->_output;
    }

    /**
     * Minify Javascript
     *
     * @param string $string
     * @return string
     */
    public static function minify($string)
    {
        $self = new self();

        return $self->process($string);
    }

    /**
     * Minify javascript reading from a file
     *
     * @param string $file
     * @return string
     */
    public static function minifyFromFile($file)
    {
        if (!file_exists($file)) {
            /**
             * @see Zym_Js_Minifier_Exception
             */
            require_once 'Zym/Js/Minifier/Exception.php';
            throw new Zym_Js_Minifier_Exception('File could not be found: ' . $file);
        }

        return self::minify(file_get_contents($file));
    }

    /**
     * Minify a javscript file
     *
     * @param string $file
     * @param string $destination New file name
     */
    public static function minifyFile($file, $destination = null)
    {
        if ($destination === null) {
            $destination = $file;
        }

        $data = self::minifyFromFile($file);

        $fp = fopen($destination, 'w');
        fwrite($fp, $data);
        fclose($fp);
    }

    /**
     * Cleanup
     *
     * @return void
     */
    protected function _cleanUp()
    {
        $this->_a      = null;
        $this->_b      = null;
        $this->_output = null;
    }

    /**
     * Check whether character is alphanumeric
     *
     * @param string $c
     * @return boolean
     */
    protected function _isAlphaNumeric($c)
    {
        return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
    }

    /**
     * Processing actions
     *
     * @param string $action
     */
    protected function _action($action)
    {
        switch ($action) {
            case 1:
                $this->_output .= $this->_a;

            case 2:
                $this->_a = $this->_b;

                if ($this->_a === "'" || $this->_a === '"') {
                    while (true) {
                        $this->_output .= $this->_a;
                        $this->_a       = $this->_iterator->next(true);

                        if ($this->_a === $this->_b) {
                            break;
                        }

                        if (ord($this->_a) <= self::ORD_LF) {
                            /**
                             * @see Zym_Js_Minifier_Exception
                             */
                            require_once 'Zym/Js/Minifier/Exception.php';
                            throw new Zym_Js_Minifier_Exception(sprintf(
                                'Unterminated string literal "%s" at char %d', $this->_a, $this->_iterator->key()
                            ));
                        }

                        if ($this->_a === '\\') {
                            $this->_output .= $this->_a;
                            $this->_a       = $this->_iterator->next(true);
                        }
                    }
                }

            case 3:
                $this->_b = $this->_iterator->next();

                $chars = array('(', ',', '=', ':', '[', '!', '&', '|', '?');
                if ($this->_b === '/' && in_array($this->_a, $chars)) {
                    $this->_output .= $this->_a . $this->_b;

                    while (true) {
                        $this->_a = $this->_iterator->next(true);

                        if ($this->_a === '/') {
                            break;
                        } else if ($this->_a === '\\') {
                            $this->_output .= $this->_a;
                            $this->_a       = $this->_iterator->next(true);
                        } else if (ord($this->_a) <= self::ORD_LF) {
                            /**
                             * @see Zym_Js_Minifier_Exception
                             */
                            require_once 'Zym/Js/Minifier/Exception.php';
                            throw new Zym_Js_Minifier_Exception(sprintf(
                                'Unterminated regular expression literal "%s" at char %d', $this->_a, $this->_iterator->key()
                            ));
                        }

                        $this->_output .= $this->_a;
                    }

                    $this->_b = $this->_iterator->next();
                }
        }
    }
}