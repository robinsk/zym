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
 * @package   Zym_Js
 * @license   http://www.zym-project.com/license    New BSD License
 * @copyright Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */

/**
 * @see PHPUnite_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zym_Js_Beautifier
 */
require_once 'Zym/Js/Beautifier.php';

/**
 * Tests the class Zym_Js_Beautifier
 *
 * @author     Geoffrey Tran
 * @category   Zym_Tests
 * @package    Zym_Js
 * @license    http://www.zym-project.com/license    New BSD License
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zym_Js_BeautifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * Beautifier
     *
     * @var Zym_Js_Beautifier
     */
    private $_beautifier;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->markTestIncomplete('Code is not up to test standard yet');
        $this->_beautifier = new Zym_Js_Beautifier();
    }

    /**
     * Tear down the environment after running a test
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->_beautifier = null;
    }

    public function testEmptyString()
    {
        $js = $this->_beautifier->parse('');
        $this->assertEquals('', $js);
    }

    public function testVarAssignmentWithExtraSpaces()
    {
        $js = $this->_beautifier->parse('a     =    1');
        $this->assertEquals('a = 1', $js);
    }

    public function testVarAssignmentWithNoSpaces()
    {
        $js = $this->_beautifier->parse('a=1');
        $this->assertEquals('a = 1', $js);
    }

    public function testVarAssignmentsAreOnDifferentLines()
    {
        $js = $this->_beautifier->parse('var a = 1 var b = 2');
        $this->assertEquals("var a = 1\nvar b = 2", $js);
    }

    public function testVarAssignmentsWithSpacesInValueAreSame()
    {
        $js = $this->_beautifier->parse('a = " 12345 "');
        $this->assertEquals('a = " 12345 "', $js);

        $js = $this->_beautifier->parse("a = ' 12345 '");
        $this->assertEquals("a = ' 12345 '", $js);
    }

    public function testFuncCallsWithNewLinesInBetweenAreSame()
    {
        $js = $this->_beautifier->parse("a();\n\nb();");
        $this->assertEquals("a();\n\nb();", $js);
    }

    public function testSingleLineIfIsSame()
    {
        $js = $this->_beautifier->parse('if (a == 1) b = 2');
        $this->assertEquals('if (a == 1) b = 2', $js);
    }

    public function testIfShouldBeOnMultipleLines()
    {
        $js = $this->_beautifier->parse('if(1){2}else{3}');
        $this->assertEquals("if (1) {\n    2\n} else {\n    3\n}", $js);
    }

    public function testIfOperatorsShouldHaveSpacing()
    {
        $js = $this->_beautifier->parse('if(1||2)');
        $this->assertEquals('if (1 || 2)', $js);

        $js = $this->_beautifier->parse('(a==1)||(b==2)');
        $this->assertEquals('(a == 1) || (b == 2)', $js);
    }

    public function testStatementsSplitIntoMultipleLines()
    {
        $js = $this->_beautifier->parse('var a = 1 if (2) 3');
        $this->assertEquals("var a = 1\nif (2) 3", $js);
    }

    public function testAssignmentAddition()
    {
        $js = $this->_beautifier->parse('a = a + 1');
        $this->assertEquals('a = a + 1', $js);
    }

    public function testRegex()
    {
        $js = $this->_beautifier->parse('/12345[^678]*9+/.match(a)');
        $this->assertEquals('/12345[^678]*9+/.match(a)', $js);
    }

    public function testAssignmentDivision()
    {
        $js = $this->_beautifier->parse('a /= 5');
        $this->assertEquals('a /= 5', $js);
    }

    public function testAssignmentMultiplication()
    {
        $js = $this->_beautifier->parse('a = 0.5 * 3');
        $this->assertEquals('a = 0.5 * 3', $js);

        $js = $this->_beautifier->parse('a *= 10.55');
        $this->assertEquals('a *= 10.55', $js);
    }

    public function testComparisonOperatorSpacing()
    {
        $js = $this->_beautifier->parse('a < .5');
        $this->assertEquals('a < .5', $js);

        $js = $this->_beautifier->parse('a <= .5');
        $this->assertEquals('a <= .5', $js);

        $js = $this->_beautifier->parse('a<.5');
        $this->assertEquals('a < .5', $js);

        $js = $this->_beautifier->parse('a<=.5');
        $this->assertEquals('a <= .5', $js);
    }

    public function testHexAssignment()
    {
        $js = $this->_beautifier->parse('a = 0xff;');
        $this->assertEquals('a = 0xff;', $js);

        $js = $this->_beautifier->parse('a=0xff+4');
        $this->assertEquals('a = 0xff + 4', $js);
    }

    public function testArrayOnSingleLine()
    {
        $js = $this->_beautifier->parse('a = [1, 2, 3, 4]');
        $this->assertEquals('a = [1, 2, 3, 4]', $js);
    }

    public function testComplexMath()
    {
        $js = $this->_beautifier->parse('F*(g/=f)*g+b');
        $this->assertEquals('F * (g /= f) * g + b', $js);
    }

    public function testObjectInFuncCall()
    {
        $js = $this->_beautifier->parse('a.b({c:d})');
        $this->assertEquals("a.b({\n    c: d\n})", $js);
    }

    public function testVariableAssignmentWithNot()
    {
        $js = $this->_beautifier->parse('a=!b');
        $this->assertEquals('a = !b', $js);
    }

    public function testTenaryOperator()
    {
        // 'a ? b : c' would need too make parser more complex to differentiate
        //between ternary op and object assignment
        $js = $this->_beautifier->parse('a?b:c');
        $this->assertEquals('a ? b: c', $js);

        // 'a ? b : c' would need too make parser more complex to differentiate
        // between ternary op and object assignment
        $js = $this->_beautifier->parse('a?1:2');
        $this->assertEquals('a ? 1 : 2', $js);

        $js = $this->_beautifier->parse('a?1:2');
        $this->assertEquals('a ? 1 : 2', $js);

        $js = $this->_beautifier->parse('a?(b):c');
        $this->assertEquals('a ? (b) : c', $js);
    }

    public function testFunctionDeclaration()
    {
        $js = $this->_beautifier->parse('function void(void) {}');
        $this->assertEquals('function void(void) {}', $js);
    }

    public function testIfWithNotShouldHaveSpace()
    {
        $js = $this->_beautifier->parse('if(!a)');
        $this->assertEquals('if (!a)', $js);
    }

    public function testAssignmentTildeShouldHaveSpace()
    {
        $js = $this->_beautifier->parse('a=~a');
        $this->assertEquals('a = ~a', $js);
    }

    public function testMultiLineComment()
    {
        $js = $this->_beautifier->parse('a;/*comment*/b;');
        $this->assertEquals("a;\n/*comment*/\nb;", $js);
    }

    public function testIfWithBreak()
    {
        $js = $this->_beautifier->parse('if(a)break');
        $this->assertEquals("if (a) break", $js);

        $js = $this->_beautifier->parse('if(a){break}');
        $this->assertEquals("if (a) {\n    break\n}", $js);
    }

    public function testWithMultipleNestedParenthases()
    {
        $js = $this->_beautifier->parse('if((a))');
        $this->assertEquals('if ((a))', $js);
    }

    public function testForLoopArgSpacing()
    {
        $js = $this->_beautifier->parse('for(var i=0;;)');
        $this->assertEquals('for (var i = 0;;)', $js);
    }

    public function testVarIncrementingShouldNotSpace()
    {
        $js = $this->_beautifier->parse('a++;');
        $this->assertEquals('a++;', $js);

        $js = $this->_beautifier->parse('for(;;i++)');
        $this->assertEquals('for (;; i++)', $js);

        $js = $this->_beautifier->parse('for(;;++i)');
        $this->assertEquals('for (;; ++i)', $js);
    }

    public function testReturnWithParenthesesSpacing()
    {
        $js = $this->_beautifier->parse('return(1)');
        $this->assertEquals('return (1)', $js);
    }

    public function testTryCatchFinally()
    {
        $js = $this->_beautifier->parse('try{a();}catch(b){c();}finally{d();}');
        $this->assertEquals("try {\n    a();\n} catch(b) {\n    c();\n} finally {\n    d();\n}", $js);
    }

    public function testMagicFunctionCall()
    {
        $js = $this->_beautifier->parse('(xx)()');
        $this->assertEquals('(xx)()', $js);

        $js = $this->_beautifier->parse('a[1]()');
        $this->assertEquals('a[1]()', $js);
    }

    public function testIfElseStatement()
    {
        $js = $this->_beautifier->parse('if(a){b();}else if(');
        $this->assertEquals("if (a) {\n    b();\n} else if (", $js);

        $js = $this->_beautifier->parse('if (a) b() else c()');
        $this->assertEquals("if (a) b()\nelse c()", $js);

        $js = $this->_beautifier->parse('if (a) b() else if c() d()');
        $this->assertEquals("if (a) b()\nelse if c() d()", $js);
    }

    public function testSwitchStatement()
    {
        $js = $this->_beautifier->parse('switch(x) {case 0: case 1: a(); break; default: break}');
        $this->assertEquals("switch (x) {\ncase 0:\ncase 1:\n    a();\n    break;\ndefault:\n    break\n}", $js);
    }

    public function testNotStrictComparison()
    {
        $js = $this->_beautifier->parse('a !== b');
        $this->assertEquals('a !== b', $js);
    }

    public function testIfElseShort()
    {
        $js = $this->_beautifier->parse('if (a) b(); else c();');
        $this->assertEquals("if (a) b();\nelse c();", $js);
    }

    public function testSingleLineComment()
    {
        $js = $this->_beautifier->parse("// comment\n(function()");
        $this->assertEquals("// comment\n(function()", $js);

        $js = $this->_beautifier->parse("// comment\n(function something()");
        $this->assertEquals("// comment\n(function something()", $js);

        $js = $this->_beautifier->parse("a = 1;// comment\n");
        $this->assertEquals("a = 1; // comment\n", $js);

        $js = $this->_beautifier->parse("a = 1; // comment\n");
        $this->assertEquals("a = 1; // comment\n", $js);

        $js = $this->_beautifier->parse("a = 1;\n // comment\n");
        $this->assertEquals("a = 1;\n// comment\n", $js);

        // if/else statement with empty body
        $js = $this->_beautifier->parse("if (a) {\n// comment\n}else{\n// comment\n}");
        $this->assertEquals("a = 1;\n// comment\n", $js);

        // multiple comments indentation
        $js = $this->_beautifier->parse("if (a) {\n// comment\n// comment\n}");
        $this->assertEquals("if (a) {\n    // comment\n    // comment\n}", $js);
    }

    public function testPreventDuplicateNewLines()
    {
        $js = $this->_beautifier->parse("{\n\n    x();\n\n}");
        $this->assertEquals("{\n\n    x();\n\n}", $js);
    }

    public function testIfIn()
    {
        $js = $this->_beautifier->parse('if (a in b)');
        $this->assertEquals('if (a in b)', $js);

        $js = $this->_beautifier->parse('if (template.user[n] in bk)');
        $this->assertEquals('if (template.user[n] in bk)', $js);
    }

    public function testObjectSpacing()
    {
        $js = $this->_beautifier->parse('{a:1, b:2}');
        $this->assertEquals("{\n    a: 1,\n    b: 2\n}", $js);

        $js = $this->_beautifier->parse('var l = {\'a\':\'1\', \'b\':\'2\'}');
        $this->assertEquals("var l = {\n    'a': '1',\n    'b': '2'\n}", $js);

        $js = $this->_beautifier->parse('{{}/z/}');
        $this->assertEquals('return 45', $js);
    }

    public function testReturnInt()
    {
        $js = $this->_beautifier->parse('return 45');
        $this->assertEquals('return 45', $js);
    }

    public function testIfThenBrackets()
    {
        $js = $this->_beautifier->parse('If[1]');
        $this->assertEquals('If[1]', $js);

        $js = $this->_beautifier->parse('Then[1]');
        $this->assertEquals('Then[1]', $js);
    }

    public function testExponentialMath()
    {
        $js = $this->_beautifier->parse('a = 1e10');
        $this->assertEquals('a = 1e10', $js);

        $js = $this->_beautifier->parse('a = 1.3e10');
        $this->assertEquals('a = 1.3e10', $js);

        $js = $this->_beautifier->parse('a = 1.3e-10');
        $this->assertEquals('a = 1.3e-10', $js);

        $js = $this->_beautifier->parse('a = -1.3e-10');
        $this->assertEquals('a = -1.3e-10', $js);

        $js = $this->_beautifier->parse('a = 1e-10');
        $this->assertEquals('a = 1e-10', $js);

        $js = $this->_beautifier->parse('a = e - 10');
        $this->assertEquals('a = e - 10', $js);
    }

    public function testSimpleSub()
    {
        $js = $this->_beautifier->parse('a = 11-10');
        $this->assertEquals('a = 11 - 10', $js);
    }

    public function testIfFuncCall()
    {
        $js = $this->_beautifier->parse("if (a) {\n    do();\n}");
        $this->assertEquals("if (a) {\n    do();\n}", $js);
    }

    public function testIfNewLineRemoval()
    {
        $js = $this->_beautifier->parse("if\n(a)\nb()");
        $this->assertEquals("if (a) b()", $js);
    }

    public function testEmptyObject()
    {
        $js = $this->_beautifier->parse('{}');
        $this->assertEquals('{}', $js);

        $js = $this->_beautifier->parse("{\n\n}");
        $this->assertEquals("{\n\n}", $js);
    }

    public function testDoLoop()
    {
        $js = $this->_beautifier->parse('do { a(); } while ( 1 );');
        $this->assertEquals("do {\n    a();\n} while ( 1 );", $js);

        $js = $this->_beautifier->parse('do {} while ( 1 );');
        $this->assertEquals('do {} while ( 1 );', $js);

        $js = $this->_beautifier->parse("do {\n} while ( 1 );");
        $this->assertEquals('do {} while ( 1 );', $js);

        $js = $this->_beautifier->parse("do {\n\n} while ( 1 );");
        $this->assertEquals("do {\n\n} while ( 1 );", $js);
    }

    public function testComplexVarAssignmentWithFunc()
    {
        $js = $this->_beautifier->parse("var a, b, c, d = 0, c = function() {}, d = '';");
        $this->assertEquals( "var a, b, c, d = 0,\nc = function() {},\nd = '';", $js);

        $js = $this->_beautifier->parse("var a = x(a, b, c)");
        $this->assertEquals("var a = x(a, b, c)", $js);
    }

    public function testDeleteIf()
    {
        $js = $this->_beautifier->parse("delete x if (a) b();");
        $this->assertEquals("delete x\nif (a) b();", $js);

        $js = $this->_beautifier->parse("delete x[x] if (a) b();");
        $this->assertEquals("delete x[x]\nif (a) b();", $js);
    }

    public function testIndentFromConstruct()
    {
        $beautifier = new Zym_Js_Beautifier(' ');
        $js = $beautifier->parse('{ one_char() }');
        $this->assertEquals("{\n one_char()\n}", $js);

        $beautifier = new Zym_Js_Beautifier('    ');
        $js = $beautifier->parse('{ one_char() }');
        $this->assertEquals("{\n    one_char()\n}", $js);

        $beautifier = new Zym_Js_Beautifier(null);
        $js = $beautifier->parse('{ one_char() }');
        $this->assertEquals("{\n    one_char()\n}", $js);
    }

    public function testIndentFromSetIndent()
    {
        $beautifier = new Zym_Js_Beautifier();
        $beautifier->setIndent(' ');
        $js = $beautifier->parse('{ one_char() }');
        $this->assertEquals("{\n one_char()\n}", $js);

        $beautifier = new Zym_Js_Beautifier();
        $beautifier->setIndent('    ');
        $js = $beautifier->parse('{ one_char() }');
        $this->assertEquals("{\n    one_char()\n}", $js);
    }

    public function testBeautify()
    {
        $this->assertEquals('{}', Zym_Js_Beautifier::beautify('{}'));
    }

    public function testBeautifyFile()
    {
        $this->markTestIncomplete();
    }

    public function testBeautifyFromFile()
    {
        $this->markTestIncomplete();
    }
}