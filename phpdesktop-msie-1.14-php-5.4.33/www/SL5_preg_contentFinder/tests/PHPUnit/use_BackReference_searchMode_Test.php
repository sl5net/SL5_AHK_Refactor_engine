<?php
//@include_once("../SL5_preg_contentFinder.php");
//
$f = 'SL5_preg_contentFinder.php';
while(!file_exists($f)) {
    $f = '../' . $f;
    echo "$f exist.";
}
include_once "../create_1file_withAll_PHPUnit_tests.php"; # ok little overhead. sometimes ;) 15-06-19_12-35
include_once $f;
class BackReference_Test extends PHPUnit_Framework_TestCase {
    /*
     * suggestion: look inside https://github.com/sl5net/SL5_preg_contentFinder/blob/master/tests/PHPUnit/Callback_Test.php before using this technicals.
     */
    function test_AABBCC() {
        $source1 = '<A>.</A><B>..</B><C>...</C>';
        $expected = 'Aa: . Bb: .. Cc: ... ';
        $beginEnd = ['(<)([^>]*)(>)?', '<\/($2)>'];
        $maxLoopCount = $pos_of_next_search = 0;
        $cf = new SL5_preg_contentFinder($source1, $beginEnd);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $actual = '';
        while($maxLoopCount++ < 30) {
            $cf->setPosOfNextSearch($pos_of_next_search);
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) {
                break;
            }
            $tagName = $borders['matches']['begin_begin'][1][0];
            $actual .= $tagName . strtolower($tagName);
            $actual .= ': ' . $cf->getContent() . ' ';
            $pos_of_next_search = $borders['end_end'];
        }
        $this->assertEquals($expected, $actual);
    }


    function test_AA_xo_A() {
        /*
         * this test works not as expected. the misspelled source is not usable enough.
         */
        $source1 = ' some </A><A>xo1</A><A>xo2</A> thing ';
        $expected = 'A: xo1 A: xo2 ';
        $beginEndRegEx = ['(<)(A)(>)?', '<\/($2)>'];
        $cf = new SL5_preg_contentFinder($source1, $beginEndRegEx);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $maxLoopCount = 1000;
        $actual = '';
        while($maxLoopCount-- > 0) {
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) break;
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= ': ' . $cf->getContent() . ' ';
            $pos_of_next_search = $borders['end_end'];
            $cf->setPosOfNextSearch($pos_of_next_search);
        }
        $this->assertEquals($expected, $actual);
    }

    function test_A_A2_A_A2() {
        /*
         * in this example you really need for correct termination additionally:
         * is_null($borders['end_begin'])
         */
        $source1 = ' some <A>XO</A></A> thing ';
        $expected = 'A: XO ';
        $beginEndRegEx = ['(<)([^>]*)(>)', '<\/($2)>'];
        $cf = new SL5_preg_contentFinder($source1, $beginEndRegEx);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $maxLoopCount = 1000;
        $actual = '';
        while($maxLoopCount-- > 0) {
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])
              || is_null($borders['end_begin'])
            ) {
                break;
            }
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= ': ' . $cf->getContent() . ' ';
            $pos_of_next_search = $borders['end_end'];
            $cf->setPosOfNextSearch($pos_of_next_search);
        }
        $this->assertEquals($expected, $actual);
    }

    function test_A1_B2B_A() {
        $source1 = ' some <A>1<B>2</B></A> thing ';
        $expected = 'A: 1<B>2</B> ';
        $beginEndRegEx = ['(<)([^>]*)(>)', '<\/($2)>'];
        $cf = new SL5_preg_contentFinder($source1, $beginEndRegEx);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $maxLoopCount = 1000;
        $actual = '';
        while($maxLoopCount-- > 0) {
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) break;
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= ': ' . $cf->getContent() . ' ';
            $pos_of_next_search = $borders['end_end'];
            $cf->setPosOfNextSearch($pos_of_next_search);
        }
        $this->assertEquals($expected, $actual);
    }

    function test_ABBA() {
        $source1 = '<A>a<B>b</B></A>';
        $expected = 'Aa<B>b</B>';
        $beginEndRegEx = ['(<)([^>]*)(>)', '<\/($2)>'];
        $cf = new SL5_preg_contentFinder($source1, $beginEndRegEx);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $actual = '';
        $maxLoopCount = 1000;
        while($maxLoopCount-- > 0) {
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) break;
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= $cf->getContent();
            $pos_of_next_search = $borders['end_end'];
            $cf->setPosOfNextSearch($pos_of_next_search);
        }
        $this->assertEquals($expected, $actual);
    }


    function test_AA_BB() {
        $source1 = '<A>a</A><B>b</B>';
        $expected = 'AaBb';
        $actual = '';
        $beginEndRegEx = ['(<)([^>]*)(>)', '<\/($2)>'];
        $cf = new SL5_preg_contentFinder($source1, $beginEndRegEx);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $maxLoopCount = 1000;
        while($maxLoopCount-- > 0) {
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) break;
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= $cf->getContent();
            $pos_of_next_search = $borders['end_end'];
            $cf->setPosOfNextSearch($pos_of_next_search);
        }
        $this->assertEquals($expected, $actual);
    }


    function test_AaA_BbB() {
        $source1 = '<!--[A]-->a<!--[/A]--><!--[B]-->b<!--[/B]-->';
        $expected = 'AaBb';
        $actual = '';
        $maxLoopCount = $pos_of_next_search = 0;
        $beginEnd = ['(<!--)?\[([^>]*)\](-->)?', '<!--\[\/($2)\]-->'];
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($beginEnd);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        while($maxLoopCount++ < 30) {
            $cf->setPosOfNextSearch($pos_of_next_search);
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) {
                break;
            }
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= $cf->getContent();
            $pos_of_next_search = $borders['end_end'];
        }
        $this->assertEquals($expected, $actual);
    }

    function test_tags_AaA_BbB() {
        $source1 = '<A>a</A> some <B>b</B>';
        $expected = 'A: a B: b ';
        $beginEnd = ['(<)([^>]*)(>)?', '<\/($2)>'];
        $maxLoopCount = $pos_of_next_search = 0;
        $cf = new SL5_preg_contentFinder($source1, $beginEnd);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $actual = '';
        while($maxLoopCount++ < 30) {
            $cf->setPosOfNextSearch($pos_of_next_search);
            $borders = $cf->getBorders();
            if(is_null($borders['begin_begin'])) {
                break;
            }
            $actual .= $borders['matches']['begin_begin'][1][0];
            $actual .= ': ' . $cf->getContent() . ' ';
            $pos_of_next_search = $borders['end_end'];
        }
        $this->assertEquals($expected, $actual);
    }

}
?>