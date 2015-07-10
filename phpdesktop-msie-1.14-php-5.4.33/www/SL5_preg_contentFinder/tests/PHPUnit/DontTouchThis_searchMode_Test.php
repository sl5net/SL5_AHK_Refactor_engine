<?php
//@include_once("../SL5_preg_contentFinder.php");
//
$f = 'SL5_preg_contentFinder.php';
while(!file_exists($f)) {
    $f = '../' . $f;
    echo "$f exist.";
}
include_once "../create_1file_withAll_PHPUnit_tests.php"; # ok little overhead. sometimes ;) 15-06-19_12-35

/*
 * bugs inside php regEx:
 * https://bugs.php.net/search.php?cmd=display&search_for=preg&x=0&y=0
 * https://bugs.php.net/bug.php?id=50887
 * http://andowebsit.es/blog/noteslog.com/post/how-to-fix-a-preg_match-bug-2/
 */

include_once $f;
class DontTouchThis_searchMode_Test extends PHPUnit_Framework_TestCase {

     function test_Grabbing_HTML_Tag() {
        return false;
//        $source1 = file_get_contents(__FILE__);
        $expected = 'hiHo';
        $source1 = '<P>hiHo</P>';
        $cf = new SL5_preg_contentFinder($source1);
        $rB = '<([A-Z][A-Z0-9]*)\b[^>]*>';
        $rE = '<\/{1}>';
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $cf->setBeginEnd_RegEx($rB, $rE);
//          '\s*\}\s*function\s+\s+function');
        $actual = $cf->getContent();
        $this->assertEquals($expected, $actual);
        $break = 'b';
    }

     function test_123_g() {
        $source1 = '123#g';
        $cf = new SL5_preg_contentFinder($source1);
        $sourceCF = @$cf->getContent(
          $begin = '\d+',
          $end = '\w+',
          $p = null,
          $t = null,
          $searchMode = 'dontTouchThis'
        );
        $expected = '#';
        $this->assertEquals($sourceCF, $expected);
    }
     function test_123_z() {
        $source1 = '123#z';
        $expected = '#';
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setSearchMode('dontTouchThis');
        $cf->setBeginEnd_RegEx('\d+', '\w+');
        $sourceCF = $cf->getContent();
        $this->assertEquals($sourceCF, $expected);
    }
     function test_123_abc_v3() {
        $source1 = '{
        hiHo
        }';
        $expected = 'hiHo';
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setSearchMode('dontTouchThis');
        $cf->setBeginEnd_RegEx('^\s*{\s*$\s*', '\s*^\s*}\s*$');
        $sourceCF = $cf->getContent();
        $this->assertEquals($sourceCF, $expected);
    }
     function test_123_abc_v4() {
        $source1 = '
class DontTouchThis_searchMode_Test extends PHPUnit_Framework_TestCase {
15-06-19_15-32';
        $expected = '15-06-19_15-32';
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setSearchMode('dontTouchThis');
        $cf->setBeginEnd_RegEx('\w\s*\{\s*', '^\s*\}\s*');
        $sourceCF = $cf->getContent();
        $levenshtein = levenshtein($expected, $sourceCF);
//        $this->assertEquals(0,$levenshtein);
        $this->assertEquals($expected . ' $levenshtein=' . $levenshtein, $sourceCF . ' $levenshtein=' . $levenshtein);
    }
     function test_123_abc_v5() {
        $source1 = '
class DontTouchThis_searchMode_Test extends PHPUnit_Framework_TestCase {
15-06-19_15-32';
        $expected = '15-06-19_15-32';
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setSearchMode('dontTouchThis');
        $cf->setBeginEnd_RegEx('\w\s*\{\s*', '^\s*\}\s*$');
        $sourceCF = $cf->getContent();
        $levenshtein = levenshtein($expected, $sourceCF);
//        $this->assertEquals(0,$levenshtein);
        $this->assertEquals($expected . ' $levenshtein=' . $levenshtein, $sourceCF . ' $levenshtein=' . $levenshtein);
    }
}
?>