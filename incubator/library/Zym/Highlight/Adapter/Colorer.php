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
 * @package    Zym_Highlight
 * @subpackage Adapter
 * @author     Robin Skoglund
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Highlight_Adapter_Abstract
 */
require_once 'Zym/Highlight/Adapter/Abstract.php';

/**
 * Zym_Highlight_Adapter_Colorer
 * 
 * Highlight adapter using 'Colorer take5' in backend.
 * 
 * @link http://colorer.sourceforge.net/ Colorer take5
 *
 * @category   Kaf
 * @package    Zym_Highlight
 * @subpackage Adapter
 * @author     Robin Skoglund <robinsk@gmail.com>
 * @copyright  Copyright (c) 2007-2008 Kameleon.ws (http://kameleon.ws)
 * @license    http://kameleon.ws/license/new-bsd  New BSD License
 */
class Zym_Highlight_Adapter_Colorer extends Zym_Highlight_Adapter_Abstract
{
    /**
     * Contains supported categories and types
     *
     * @var array
     */
    protected $_types = array(
        'Text' => array(
            'auto' => 'Autodetect type',
            'text' => 'Plain text',
            'postscript' => 'PostScript',
            'rtf' => 'RTF text',
            'tex' => 'TeX',
            'url' => 'URL',
            'vim' => 'VIM'),
        
        'Compiled languages' => array(
            'asm' => 'ASM',
            'c' => 'C',
            'cpp' => 'C++',
            'csharp' => 'C#',
            'fortran' => 'Fortran',
            'idl' => 'IDL',
            'java' => 'Java',
            'jsnet' => 'JS.NET',
            'pascal' => 'Pascal',
            'perl' => 'Perl',
            'vbnet' => 'VB.NET',
            'vbasic' => 'Visual Basic'),
            
        'Scripted languages' => array(
            'ant' => 'Ant build.xml',
            'apache' => 'Apache httpd.conf',
            'batch' => 'Batch script (windows)',
            'javacc' => 'Java Compiler Compiler',
            'pnuts' => 'Java pnuts',
            'javaproperties' => 'Java properties',
            'javapolicy' => 'Java policy',
            'lex' => 'Lex',
            'lisp' => 'Lisp',
            'm4' => 'M4',
            'makefile' => 'Makefile',
            'python' => 'Python',
            'resources' => 'Resources',
            'ruby' => 'Ruby',
            'shell' => 'Shell script',
            'sml' => 'Standard ML',
            'tcltk' => 'TCL/Tk',
            'vrml' => 'VRML',
            'yacc' => 'YACC'),
    
        'Web' => array(
            'actionscript' => 'ActionScript',
            'asp.vb' => 'ASP - VBScript',
            'asp.js' => 'ASP - JavaScript',
            'asp.ps' => 'ASP - PerlScript',
            'coldfusion' => 'ColdFusion',
            'css' => 'CSS',
            'html-css' => 'CSS for html',
            'svg-css' => 'CSS for svg',
            'html' => 'HTML',
            'jScript' => 'JavaScript',
            'taglib' => 'JSP taglib',
            'jsp' => 'JSP',
            'xbl' => 'Mozilla XBL',
            'parser' => 'Parser',
            'php' => 'PHP',
            'rss' => 'RSS',
            'web-app' => 'Sun web-app.xml',
            'svg' => 'SVG 1.0',
            'vbScript' => 'VBScript',
            'wsc' => 'WSC',
            'wsf' => 'WSF',
            'xhtml-trans' =>
            'XHTML Transitional',
            'xhtml-strict' => 'XHTML Strict',
            'xhtml-frameset' => 'XHTML Frameset'),
    
        'XML' => array(
            'docbook' => 'DocBook 4.2',
            'dtd' => 'DTD',
            'mathml' => 'MathML2',
            'relaxng' => 'Relax NG',
            'rdf' => 'RDF',
            'wml' => 'WML',
            'wsdl' => 'WSDL 1.1',
            'xml' => 'XML',
            'xmlschema' => 'XML Schema',
            'xquery' => 'XQuery 1.0',
            'xslt' => 'XSLT 1.0',
            'xslt2' => 'XSLT 2.0',
            'xslfo' => 'XSLFO 1.0'),
    
        'Database' => array(
            'clarion' => 'Clarion',
            'Clipper' => 'Clipper',
            'foxpro' => 'FoxPro',
            'sqlj' => 'SQLJ (Java sql)',
            'paradox' => 'Paradox',
            'sql' => 'SQL, PL/SQL',
            'mysql' => 'MySQL',
            'csql' => 'EmbeddedSQL for C',
            'cppsql' => 'EmbeddedSQL for C++',
            'cobolsql' => 'EmbeddedSQL for COBOL'),
    
        'INI files' => array(
            'bootini' => 'Boot.ini',
            'config' => 'Config, INI and CTL',
            'configsys' => 'config.sys',
            'msdossys' => 'MsDos.sys',
            'reg' => 'Regedit',
            'ini' => 'Other INI'),
        
        'Other' => array(
            'mel3dmax' => '3D Max Script',
            'diff' => 'Diff/Patch',
            'email' => 'Email',
            'irclog' => 'IRC logs'),
    
        'Rarities' => array(
            'filesbbs' => 'files.bbs',
            'iss' => 'InnoSetup script',
            'isScripts' => 'IS script',
            'nsi' => 'Nullsoft Install Script',
            'rarscript' => 'RAR Install Script',
            'vhdl' => 'VHDL')
    );
    
    /**
     * Default type to use when highlighting
     *
     * @var string
     */
    protected $_defaultType = 'auto';
    
    /**
     * Path to colorer exectuable
     *
     * @var string
     */
    protected $_colorer = 'colorer';
    
    /**
     * Whether colorer path has been checked
     *
     * @var bool
     */
    protected $_checked = false;
    
    /**
     * Whether to use CSS tokens or inline CSS in highlighted text
     *
     * @var bool
     */
    protected $_tokens = false;
    
    /**
     * Input encoding
     *
     * @var string
     */
    protected $_inputEncoding = 'UTF-8';
    
    /**
     * Output encoding
     *
     * @var string
     */
    protected $_outputEncoding = 'UTF-8';
    
    /**
     * Checks if colorer path is ok
     *
     * @param string $path  [optional] path to colorer
     * @return bool
     * @throws Zym_Highlight_Exception if check fails
     */
    protected function _checkColorer($path = null)
    {
        if (null === $path) {
            $path = $this->_colorer;
        }
        
        if ($handle = @popen($path, 'r')) {
            pclose($handle);
            return true;
        } else {
            require_once 'Zym/Highlight/Exception.php';
            $msg = "Failed to open colorer executable '$path'";
            throw new Zym_Highlight_Exception($msg);
        }
    }
    
    /**
     * Sets path to colorer executable
     *
     * @param string $path
     * @throws Zym_Highlight_Exception if invalid path is given
     */
    public function setColorer($path)
    {
        if (!is_string($path) || empty($path)) {
            require_once 'Zym/Highlight/Exception.php';
            $msg = "Invalid path given '$path'";
            throw new Zym_Highlight_Exception($msg);
        }
        
        if ($this->_checkColorer($path)) {
            $this->_colorer = $path;
            $this->_checked = true;
        }
    }
    
    /**
     * Set whether to use CSS tokens or inline CSS
     *
     * @param bool $useTokens
     */
    public function setTokens($useTokens)
    {
        $this->_tokens = (bool) $useTokens;
    }
    
    /**
     * Sets input encoding for highlight method
     *
     * @param string $encoding  [optional] if not given, encoding will not
     *                          be specified when calling colorer
     * @return void
     * @throws Zym_Highlight_Exception  on invalid encoding
     */
    public function setInputEncoding($encoding = null)
    {
        if (null == $encoding) {
            $this->_inputEncoding = null;
        } elseif (is_string($encoding) && !empty($encoding)) {
            $this->_inputEncoding = $encoding;
        } else {
            require_once 'Zym/Highlight/Exception.php';
            $msg = "Invalid encoding '$encoding'";
            throw new Zym_Highlight_Exception($msg);
        }
    }
    
    /**
     * Sets output encoding for highlight method
     *
     * @param string $encoding  [optional] if not given, encoding will not
     *                          be specified when calling colorer
     * @return void
     * @throws Zym_Highlight_Exception  on invalid encoding
     */
    public function setOutputEncoding($encoding = null)
    {
        if (null == $encoding) {
            $this->_outputEncoding = null;
        } elseif (is_string($encoding) && !empty($encoding)) {
            $this->_outputEncoding = $encoding;
        } else {
            require_once 'Zym/Highlight/Exception.php';
            $msg = "Invalid encoding '$encoding'";
            throw new Zym_Highlight_Exception($msg);
        }
    }
    
    /**
     * Sets the given option
     *
     * @param string $name
     * @param mixed $value
     * @throws Zym_Highligh_Exception on invalid option
     */
    public function setOption($name, $value)
    {
        switch (strtolower($name)) {
            case 'colorer':
                $this->setColorer($value);
                break;
            case 'tokens':
                $this->setInputEncoding($value);
                break;
            case 'inputencoding':
            case 'input_encoding':
                $this->setInputEncoding($value);
                break;
            case 'outputencoding':
            case 'output_encoding':
                $this->setOutputEncoding($value);
                break;
            default:
                parent::setOption($name, $value);
                break;
        }
    }
    
    /**
     * Highlights the given string
     *
     * @param string $str
     * @param string $type  [optional] defaults to $_defaultType
     * @return array  highlighted string with each line as an element
     */
    public function highlight($str, $type = null)
    {
        if (null == $type || !$this->hasType($type)) {
            $type = $this->_defaultType;
        }
        
        // simple handling of URLs
        if ($type == 'url') {
            $explode = explode("\n", $str);
            for ($i=0; $i<count($explode); $i++) {
                $explode[$i] = trim($explode[$i]);
                $explode[$i] = "<a rel=\"nofollow\" href=\"{$explode[$i]}\">{$explode[$i]}</a>";
            }
            return $explode;
        }
        
        // avoid checking colorer if already checked
        if (!$this->_checked) {
            // perform check
            $this->_checkColorer();
            $this->_checked = true;
        }
        
        $tmpdir = function_exists('sys_get_temp_dir') ? @sys_get_temp_dir() : '';
        
        // allocate tmpfile
        if ($tmpfile = @tempnam($tmpdir, "colorer")) {
            // put string in tmpfile
            if (@file_put_contents($tmpfile, $str)) {
                
                // generate params to use:
                
                // tokens or inline css?
                $params = $this->_tokens ? '-ht' : '-h';
                
                // specify type or autodetect?
                if ($type != 'auto') {
                    $params .= ' -t' . $type;
                }
                
                // input encoding?
                if ($this->_inputEncoding) {
                    $params .= ' -ei' . $this->_inputEncoding;
                }
                
                // output encoding?
                if ($this->_outputEncoding) {
                    $params .= ' -eo' . $this->_outputEncoding;
                }
                
                // disable BOM, information header and html header/footer
                $params .= ' -db -dc -dh';
                
                // done with params, create command
                $cmd = "{$this->_colorer} $params $tmpfile";
                
                // run colorer
                $result = @exec($cmd, $formatted, $returnVar);
                
                // delete tmpfile
                @unlink($tmpfile);
                
                // delete logfile, if any
                $logfile = dirname($tmpfile) . '/colorer.log';
                if (@is_writable($logfile)) {
                    @unlink($logfile);
                }
                
                if (!is_int($returnVar) || $returnVar !== 0) {
                    // error occured when executing colorer
                    $msg = 'Unable to highlight string';
                } else {
                    if (!is_array($formatted) || count($formatted) < 1) {
                        // something is semi-fucked
                        return explode("\n", htmlspecialchars($str));
                    }
                    
                    // everything's dandy
                    return $formatted;
                }
            } else {
                $msg = 'Unable to put string in tempfile';
            }
        } else {
            $msg = 'Unable to allocate tempfile';
        }
        
        require_once 'Zym/Highlight/Exception.php';
        throw new Zym_Highlight_Exception($msg);
    }
}
