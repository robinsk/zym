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
 * Javascript beautifier
 *
 * Warning! Bad code ahead...
 *
 * Not so perfect as a js beautifier, but works great for JSON
 * Ported to PHP5 from {@link http://elfz.laacz.lv/beautify/}
 *
 * @author Geoffrey Tran
 * @license http://www.zym-project.com/license New BSD License
 * @category Zym
 * @package Zym_Js
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Js_Beautify
{
    const IN_EXPR  = 2;
    const IN_BLOCK = 3;

    const TK_UNKNOWN     = 4;
    const TK_WORD        = 5;
    const TK_START_EXPR  = 6;  // ([
    const TK_END_EXPR    = 7;  // )]
    const TK_START_BLOCK = 8;  // {
    const TK_END_BLOCK   = 9;  // }
    const TK_END_COMMAND = 10; // ;
    const TK_EOF         = 11;
    const TK_STRING      = 12; // '"

    const TK_BLOCK_COMMENT = 13; // /* ... */
    const TK_COMMENT       = 14; // //

    const TK_OPERATOR = 15;

    // Internal flags
    const PRINT_NONE  = 16;
    const PRINT_SPACE = 17;
    const PRINT_NL    = 18;

    /**
     * Whether to use tabs or spaces
     *
     * @var boolean
     */
    protected $_tabsAsSpaces = true;

    /**
     * Number of spaces to use for a tab
     *
     * @var integer
     */
    protected $_tabSize = 4;

    /**
     * Words that should start on a new line
     *
     * Simple hack for cases when lines aren't ending with semicolon.
     *
     * @var array
     */
    protected $_lineStarters = array(
        'continue', 'try', 'throw', 'return', 'var', 'if', 'switch', 'case',
        'default', 'for', 'while', 'break', 'function'
    );

    /**
     * Whitespace characters
     *
     * @var array
     */
    protected $_whitespaceChars = array("\n", "\r", "\t", ' ');

    /**
     * Operators
     *
     * @var array
     */
    protected $_operators = array(
        '+', '-', '*', '/', '%', '&', '++', '--', '=', '+=', '-=', '*=', '/=', '%=',
        '==', '===', '!=', '!==', '>', '<', '>=', '<=', '>>', '<<', '>>>', '>>>=',
        '>>=', '<<=', '&&', '&=', '|', '||', '!', '!!', ',', ':', '?', '^', '^=', '|='
    );

    /**
     * Word characters
     *
     * Ew yucky man... have you heard of regex
     *
     * @var array
     */
    protected $_wordChars = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1',
        '2', '3', '4', '5', '6', '7', '8', '9', '_', '$',
    );

    /**
     * Current parser area
     *
     * @var integer
     */
    protected $_in = self::IN_BLOCK;

    /**
     * Current in cache
     *
     * @var array
     */
    protected $_ins = array(self::IN_BLOCK);

    /**
     * Flag for parser that case/default has been processed,
     * and next colon needs special attention
     *
     * @var boolean
     */
    protected $_inCase = false;

    /**
     * Indent size
     *
     * @var integer
     */
    protected $_indentSize = 0;

    /**
     * Keep tracks of if the last line was a new line
     *
     * @var boolean
     */
    protected $_isLastNl = true;

    /**
     * Last {self::TK_WORD} passed
     *
     * @var string
     */
    protected $_lastWord = '';

    /**
     * Last token type
     *
     * @var integer
     */
    protected $_lastType = self::TK_START_EXPR;

    /**
     * Last token text
     *
     * @var string
     */
    protected $_lastText = '';

    /**
     * Parser position
     *
     * @var integer
     */
    protected $_pos = 0;

    /**
     * Holds output to be worked on
     *
     * @var string
     */
    protected $_output = '';

    /**
     * Construct
     *
     * @param boolean $tabsAsSpaces
     * @param integer $tabSize
     */
    public function __construct($tabsAsSpaces = true, $tabSize = 4)
    {
        $this->setTabsAsSpaces($tabsAsSpaces);
        $this->setTabSize($tabSize);
    }

    /**
     * Get tabs as spaces setting
     *
     * @return boolean
     */
    public function getTabAsSpaces()
    {
        return $this->_tabsAsSpaces;
    }

    /**
     * Set tabs as spaces
     *
     * @param boolean $spaces
     * @return Zym_Js_Beautify
     */
    public function setTabsAsSpaces($spaces = true)
    {
        $this->_tabsAsSpaces = (bool) $spaces;
        return $this;
    }

    /**
     * Get tabs size setting
     *
     * @return integer
     */
    public function getTabSize()
    {
        return $this->_tabSize;
    }

    /**
     * Set tabs size
     *
     * @param integer $size
     * @return Zym_Js_Beautify
     */
    public function setTabSize($size)
    {
        $this->_tabSize = $size;
        return $this;
    }

    /**
     * Get words that should start on a new line
     *
     * @return array
     */
    public function getLineStarters()
    {
        return $this->_lineStarters;
    }

    /**
     * Set words that should start on a new line
     *
     * @param array $starters
     * @return Zym_Js_Beautify
     */
    public function setLineStarters(array $starters)
    {
        $this->_lineStarters = $starters;
        return $this;
    }

    /**
     * Get whitespace characters
     *
     * @return array
     */
    public function getWhitespaceChars()
    {
        return $this->_whitespaceChars;
    }

    /**
     * Set whitespace characters
     *
     * @param array $whitespaces
     * @return Zym_Js_Beautify
     */
    public function setWhitespaceChars(array $whitespaces)
    {
        $this->_whitespaceChars = $whitespaces;
        return $this;
    }

    /**
     * Get operators
     *
     * @return array
     */
    public function getOperators()
    {
        return $this->_operators;
    }

    /**
     * Set operators
     *
     * @param array $operators
     * @return Zym_Js_Beautify
     */
    public function setOperators(array $operators)
    {
        $this->_operators = $operators;
        return $this;
    }

    /**
     * Get word chars
     *
     * @return array
     */
    public function getWordChars()
    {
        return $this->_wordChars;
    }

    /**
     * Set word chars
     *
     * @param array $wordChars
     * @return Zym_Js_Beautify
     */
    public function setWordChars(array $wordChars)
    {
        $this->_wordChars = $wordChars;
        return $this;
    }

    /**
     * Parse javascript
     *
     * @param string $source
     * @return string
     */
    public function parse($source)
    {
        $this->_output = '';
        $this->_pos = 0;
        while(true) {
        	list($tokenText, $tokenType) = $this->_getNextToken($source);
        	if ($tokenType == self::TK_EOF) {
        	    break;
        	}

        	switch ($tokenType) {
                case self::TK_START_EXPR:
                    $this->_in(self::IN_EXPR);
                    if ($this->_lastType == self::TK_END_EXPR || $this->_lastType == self::TK_START_EXPR) {
                        // do nothing on (( and )( and ][ and ]( ..
                    } elseif ($this->_lastType != self::TK_WORD && $this->_lastType != self::TK_OPERATOR) {
                        $this->_space();
                    } elseif (in_array($this->_lastWord, $this->getLineStarters()) && $this->_lastWord != 'function') {
                        $this->_space();
                    }
                    $this->_token($tokenText);
                    break;

                case self::TK_END_EXPR:
                    $this->_token($tokenText);
                    $this->_inPop();
                    break;

                case self::TK_START_BLOCK:
                    $this->_in(self::IN_BLOCK);
                    if ($this->_lastType != self::TK_OPERATOR && $this->_lastType != self::TK_START_EXPR) {
                        if ($this->_lastType == self::TK_START_BLOCK) {
                            $this->_nl();
                        } else {
                            $this->_space();
                        }
                    }
                    $this->_token($tokenText);
                    $this->_indent();
                    break;

                case self::TK_END_BLOCK:
                    if ($this->_lastType == self::TK_END_EXPR) {
                        $this->_unIndent();
                        $this->_nl(0, false);
                    } elseif ($this->_lastType == self::TK_END_BLOCK) {
                        $this->_unIndent();
                        $this->_nl(0, false);
                    } elseif ($this->_lastType == self::TK_START_BLOCK) {
                        // nothing
                        $this->_unIndent();
                    } else {
                        $this->_unIndent();
                        $this->_nl(0, false);
                    }
                    $this->_token($tokenText);
                    $this->_inPop();
                    break;

                case self::TK_WORD:
                    if ($tokenText == 'case' or $tokenText == 'default') {
                        if ($this->_lastText == ':') {
                            // switch cases following one another
                            $this->_removeIdent();
                        } else {
                            $this->_indentSize--;
                            $this->_nl();
                            $this->_indentSize++;
                        }
                        $this->_token($tokenText);
                        $this->_inCase = true;
                        break;
                    }

                    $prefix = self::PRINT_NONE;
                    if ($this->_lastType == self::TK_END_BLOCK) {
                        if (!in_array($tokenText, array('else', 'catch', 'finally'))) {
                            $prefix = self::PRINT_NL;
                        } else {
                            $prefix = self::PRINT_SPACE;
                            $this->_space();
                        }
                    } elseif ($this->_lastType == self::TK_END_COMMAND && $this->_in== self::IN_BLOCK) {
                        $prefix = self::PRINT_NL;
                    } elseif ($this->_lastType == self::TK_END_COMMAND && $this->_in== self::IN_EXPR) {
                        $prefix = self::PRINT_SPACE;
                    } elseif ($this->_lastType == self::TK_WORD) {
                        if ($this->_lastWord == 'else') { // else if
                            $prefix = self::PRINT_SPACE;
                        } else {
                            $prefix = self::PRINT_SPACE;
                        }
                    } elseif ($this->_lastType == self::TK_START_BLOCK) {
                        $prefix = self::PRINT_NL;
                    } elseif ($this->_lastType == self::TK_END_EXPR) {
                        $this->_space();
                    }

                    if (in_array($tokenText, $this->getLineStarters()) or $prefix == self::PRINT_NL) {
                        if ($this->_lastText == 'else') {
                            // no need to force newline on else break
                            // DONOTHING
                            $this->_space();
                        } elseif (($this->_lastType == self::TK_START_EXPR or $this->_lastText == '=') and $tokenText == 'function') {
                            // no need to force newline on 'function': (function
                            // DONOTHING
                        } elseif ($this->_lastType == self::TK_WORD and ($this->_lastText == 'return' or $this->_lastText == 'throw')) {
                            // no newline between 'return nnn'
                            $this->_space();
                        } else
                            if ($this->_lastType != self::TK_END_EXPR) {
                                if (($this->_lastType != self::TK_START_EXPR or $tokenText != 'var') and $this->_lastText != ':') {
                                    // no need to force newline on 'var': for (var x = 0...)
                                    if ($tokenText == 'if' and $this->_lastType == self::TK_WORD and $this->_lastWord == 'else') {
                                        // no newline for } else if {
                                        $this->_space();
                                    } else {
                                        $this->_nl();
                                    }
                                }
                            }
                    } elseif ($prefix == self::PRINT_SPACE) {
                        $this->_space();
                    }
                    $this->_token($tokenText);
                    $this->_lastWord = $tokenText;
                    break;

                case self::TK_END_COMMAND:
                    $this->_token($tokenText);
                    break;

                case self::TK_STRING:

                    if ($this->_lastType == self::TK_START_BLOCK or $this->_lastType == self::TK_END_BLOCK) {
                        $this->_nl();
                    } elseif ($this->_lastType == self::TK_WORD) {
                        $this->_space();
                    }
                    $this->_token($tokenText);
                    break;

                case self::TK_OPERATOR:
                    $startDelim = true;
                    $endDelim   = true;

                    if ($tokenText == ':' and $this->_inCase) {
                        $this->_token($tokenText); // colon really asks for separate treatment
                        $this->_nl();
                        break;
                    }

                    $this->_inCase = false;

                    if ($tokenText == ',') {
                        if ($this->_lastType == self::TK_END_BLOCK) {
                            $this->_token($tokenText);
                            $this->_nl();
                        } else {
                            if ($this->_in == self::IN_BLOCK) {
                                $this->_token($tokenText);
                                $this->_nl();
                            } else {
                                $this->_token($tokenText);
                                $this->_space();
                            }
                        }
                        break;
                    } else if ($tokenText == '--' or $tokenText == '++') { // unary operators special case
                        if ($this->_lastText == ';') {
                            // space for (;; ++i)
                            $startDelim = true;
                            $endDelim = false;
                        } else {
                            $startDelim = false;
                            $endDelim = false;
                        }
                    } else if ($tokenText == '!' and $this->_lastType == self::TK_START_EXPR) {
                        // special case handling: if (!a)
                        $startDelim = false;
                        $endDelim = false;
                    } else if ($this->_lastType == self::TK_OPERATOR) {
                        $startDelim = false;
                        $endDelim = false;
                    } else if ($this->_lastType == self::TK_END_EXPR) {
                        $startDelim = true;
                        $endDelim = true;
                    } else if ($tokenText == '.') {
                        // decimal digits or object.property
                        $startDelim = false;
                        $endDelim   = false;

                    } elseif ($tokenText == ':') {
                        // zz: xx
                        // can't differentiate ternary op, so for now it's a ? b: c; without space before colon
                        $startDelim = false;
                    }

                    if ($startDelim) {
                        $this->_space();
                    }

                    $this->_token($tokenText);

                    if ($endDelim) {
                        $this->_space();
                    }
                    break;

                case self::TK_BLOCK_COMMENT:
                    $this->_nl();
                    $this->_token($tokenText);
                    $this->_nl();
                    break;

                case self::TK_COMMENT:
                    $this->_nl();
                    $this->_token($tokenText);
                    $this->_nl();
                    break;

                case self::TK_UNKNOWN:
                    $this->_token($tokenText);
                    break;
        	}

            if ($tokenType != self::TK_COMMENT) {
                $this->_lastType = $tokenType;
                $this->_lastText = $tokenText;
            }
        }

        // clean empty lines from redundant spaces
        $output = preg_replace('/^ +$/m', '', $this->_output);
        return $output;
    }

    /**
     * Get tab
     *
     * @return string
     */
    protected function _getTab()
    {
        return ($this->getTabAsSpaces()) ? str_repeat(' ', $this->_tabSize) : "\t";
    }

    /**
     * Get token
     *
     * @param string $source
     * @return array
     */
    protected function _getNextToken($source)
    {
        $newLines = 0;
        $inputLength = strlen($source);

        do {
            if ($this->_pos >= $inputLength) {
                return array('', self::TK_EOF);
            }

            $c = $source[$this->_pos++];
            if ($c == "\n") {
                $newLines++;
            }
        } while(in_array($c, $this->getWhitespaceChars()));

        if ($newLines) {
            $this->_nl($newLines);
        }

        $wordChars = $this->getWordChars();
        if (in_array($c, $wordChars)) {
            if ($this->_pos < $inputLength) {
                while (in_array($source[$this->_pos], $wordChars)) {
                    $c .= $source[$this->_pos];
                    $this->_pos++;

                    if ($this->_pos == $inputLength) {
                        break;
                    }
                }
            }

            // Hack for 'in' operator
            return ($c == 'in') ? array($c, self::TK_OPERATOR) : array($c, self::TK_WORD);
        }

        if ($c == '(' || $c == '[') {
            return array($c, self::TK_START_EXPR);
        }

        if ($c == ')' || $c == ']') {
            return array($c, self::TK_END_EXPR);
        }

        if ($c == '{') {
            return array($c, self::TK_START_BLOCK);
        }

        if ($c == '}') {
            return array($c, self::TK_END_BLOCK);
        }

        if ($c == ';') {
            return array($c, self::TK_END_COMMAND);
        }

        if ($c == '/') {
            // Peek for comment /* ... */
            if ($source[$this->_pos] == '*') {
                $comment = '';
                $this->_pos++;

                if ($this->_pos < $inputLength){
                    while (!($source[$this->_pos] == '*' && isset($source[$this->_pos + 1]) && $source[$this->_pos + 1] == '/') && $this->_pos < $inputLength) {
                        $comment .= $source[$this->_pos];
                        $this->_pos++;
                        if ($this->_pos >= $inputLength) break;
                    }
                }

                $this->_pos += 2;
                return array("/*$comment*/", self::TK_BLOCK_COMMENT);
            }

            // Peek for comment // ...
            if ($source[$this->_pos] == '/') {
                $comment = $c;
                while ($source[$this->_pos] != "\x0d" && $source[$this->_pos] != "\x0a") {
                    $comment .= $source[$this->_pos];
                    $this->_pos++;
                    if ($this->_pos >= $inputLength) break;
                }

                $this->_pos++;
                return array($comment, self::TK_COMMENT);
            }
        }

        if ($c == "'" || // string
            $c == '"' || // string
            ($c == '/' &&
                (($this->_lastType = self::TK_WORD and $this->_lastText == 'return') || ($this->_lastType == self::TK_START_EXPR || $this->_lastType == self::TK_END_BLOCK || $this->_lastType == self::TK_OPERATOR || $this->_lastType == self::TK_EOF || $this->_lastType == self::TK_END_COMMAND)))) { // regexp
            $sep = $c;
            $c   = '';
            $esc = false;

            if ($this->_pos < $inputLength) {
                while ($esc || $source[$this->_pos] != $sep) {
                    $c .= $source[$this->_pos];
                    if (!$esc) {
                        $esc = $source[$this->_pos] == '\\';
                    } else {
                        $esc = false;
                    }

                    $this->_pos++;
                    if ($this->_pos >= $inputLength) {
                        break;
                    }
                }
            }

            $this->_pos++;
            if ($this->_lastType == self::TK_END_COMMAND) {
                $this->_nl();
            }

            return array($sep . $c . $sep, self::TK_STRING);
        }

        $operators = $this->getOperators();
        if (in_array($c, $operators)) {
            while (in_array($c . $source[$this->_pos], $operators)) {
                $c .= $source[$this->_pos];
                $this->_pos++;

                if ($this->_pos >= $inputLength) {
                    break;
                }
            }
            return array($c, self::TK_OPERATOR);
        }

        return array($c, self::TK_UNKNOWN);

    }

    /**
     * NewLine
     *
     * @param integer $newLines
     * @param boolean $ignoreRepeat
     */
    protected function _nl($newLines = 1, $ignoreRepeat = true)
    {
        if ($newLines) {
            $this->_output .= str_repeat("\n", $newLines - 1);
        }

        if ($this->_output == '' || ($ignoreRepeat && $this->_isLastNl)) {
            // no newline on start of file
            return;
        }

        $this->_isLastNl = true;
        $this->_output .= "\n" . str_repeat($this->_getTab(), $this->_indentSize);
    }

    /**
     * Space
     *
     */
    protected function _space()
    {
        $this->_isLastNl = false;

        if ($this->_output && substr($this->_output, -1) != ' ') { // prevent occassional duplicate space
            $this->_output .= ' ';
        }
    }

    /**
     * Token
     *
     * @param string $token TokenText
     */
    protected function _token($token)
    {
        $this->_output .= $token;
        $this->_isLastNl = false;
    }

    /**
     * Indent
     *
     */
    protected function _indent()
    {
        $this->_indentSize++;
    }

    /**
     * Unindent
     *
     */
    protected function _unIndent()
    {
        if ($this->_indentSize) {
            $this->_indentSize--;
        }
    }

    /**
     * Remove Indent
     *
     */
    protected function _removeIndent()
    {
        $tabStr = $this->_getTab();
        $tabStrLen = strlen($tabStr);
        if (substr($this->_output, -$tabStrLen) == $tabStr) {
            $this->_output = substr($this->_output, 0, -$tabStrLen);
        }
    }

    /**
     * Set in statement status
     *
     * @param integer $where
     */
    protected function _in($where)
    {
        array_push($this->_ins, $this->_in);
        $this->_in = $where;
    }

    /**
     * Remove in
     *
     */
    protected function _inPop()
    {
        $this->_in = array_pop($this->_ins);
    }
}
