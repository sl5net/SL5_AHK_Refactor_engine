<?php
//@include_once("../SL5_preg_contentFinder.php");
//
//require("../SL5_preg_contentFinder.php");
$f = 'SL5_preg_contentFinder.php';
while(!file_exists($f)) {
    $f = '../' . $f;
    echo "$f exist.";
}
include_once $f;

include '../../lib/finediff.php';

$c = new Callback_Test_with_FineFiff();
$c->test_reformat_AutoHotKey();

class Callback_Test_with_FineFiff  {
//     $collectString = '';


    function test_reformat_AutoHotKey() {
        $source1 = '#IfWinActive ahk_class SciTEWindow
; Refactoring Engine
fun()
{

   Last_A_This:=""
   if(false)
      Too(Last_A_This)
   s := Com("{D7-2B-4E-B8-B54}")
   if !os
   {

      ExitApp
   }
   ; comment :) { { {
   ExitApp
}
fun2(do){

   dohaa
}
funZ(do){
   doZZ
}';
        $expected = '#IfWinActive ahk_class SciTEWindow
; Refactoring Engine
fun()
{

   Last_A_This:=""
   if(false)
      Too(Last_A_This)
   s := Com("{D7-2B-4E-B8-B54}")
   if !os
   {

      ExitApp
   }
   ; comment :) { { {
   ExitApp
}
fun2(do){

   dohaa
}
funZ(do){
   doZZ
}';
        $old_open = '^([^{;\n]*)\{[\s\n]';
        $old_close = '^([^};\n\r]*)\}[\s\n\r]';

        $new_open_default = '{ ';
        $new_close_default = '}';
        $charSpace = " ";
        $newline = "\r\n";
        $indentSize = 3;

        $source1 = trim(preg_replace('/^\s+/smi', '', $source1));

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $indentStr = $getIndentStr(1, $charSpace, $indentSize);
//    $pattern = '([\r\n](If|#if)[a-z]+[ ]*[ ]*[^\n\r{]+)[ ]*[\r\n]+[ ]*(\w)';
        $pattern = '([\r\n](If|#if)([a-z]+[ ]*,|\()[ ]*[^\n\r{]+)[ ]*[\r\n]+[ ]*(\w)';
        $source1 = preg_replace('/' . $pattern . '/is', "$1" . $newline . $indentStr . "$4", $source1);

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);
        $cf->setSearchMode('dontTouchThis');


        /*
         *             $cut = call_user_func($func['open'], $cut, $deepCount + 1, $callsCount, $C->foundPos_list[0], $C->content);

         */

        $actual = $cf->getContent_user_func_recursive(

          function ($cut, $deepCount, $callsCount, $posList0, $source1) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
              if($cut['middle'] === false) return $cut;
//          if($cut['middle'] === false || $cut['behind'] === false) {
//              return false;
//          }
//          $charSpace = '.';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

              if(!isset($posList0['begin_end'])) $posList0['begin_end'] = strlen($source1);
              $start = '' . substr($source1, $posList0['begin_begin'], $posList0['begin_end'] - $posList0['begin_begin']) . '';
              $end = '' . substr($source1, $posList0['end_begin'], $posList0['end_end'] - $posList0['end_begin']) . '';

              $cut['middle'] = '' . rtrim($start) . $n . $n . $indentStr
                . trim(preg_replace('/\n/', "\n" . $indentStr, $cut['middle']));
//          $charSpace = '.';
              $indentStr = $getIndentStr(0, $charSpace, $indentSize);
              $cut['middle'] .= $n . $indentStr . $end . $cut['behind'];

              return $cut;
          });

        $opcodes = FineDiff::getDiffOpcodes($actual, $expected);
        $to_text = FineDiff::renderToTextFromOpcodes($expected, $opcodes);
        echo $to_text;
        if(class_exists('PHPUnit_Framework_TestCase'))
            $this->assertEquals($expected, $actual);

    }


    function test_wrongSource_NIXNIX_callback() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':{NIX{}';
        $expected = $LINE__ . ':NIX{'; # but it gets back   :{NIX{}
        $old = ['{', '}'];
        $charSpace = "";
        $newline = "";
        $indentSize = 2;
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };
        $new_open_default = '{';
        $new_close_default = '}';
        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//              $n .= $deepCount.'|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);
              $cut['before'] .= $n . $indentStr . $new_open_default;
              // return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//              $n .= $deepCount.':';
//              $charSpace ='`';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//              $n .= $deepCount.';';
//              $charSpace ='´';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $cut['behind'];

              // return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}
//        $actual = $cBefore . $content . $cBehind;

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }

    function test_wrongSource_NoX_callback() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':{NoX';
        $expected = $LINE__ . ':NoX';
        $old = ['{', '}'];
        $new_open_default = '{';
        $new_close_default = '}';

        $charSpace = "";
        $newline = "";
        $indentSize = 2;
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };
        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//              $n .= $deepCount.'|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);
              $cut['before'] .= $n . $indentStr . $new_open_default;
              // return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//              $n .= $deepCount.':';
//              $charSpace ='`';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//              $n .= $deepCount.';';
//              $charSpace ='´';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $cut['behind'];

              // return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });
        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);
    }


    function test_a_b_B_callback() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':a{b{B}}';
        $expected = $LINE__ . ':a<b<B>>';
        $old = ['{', '}'];

        $new_open_default = '<';
        $new_close_default = '>';
        $charSpace = "";
        $newline = "\r\n";
        $newline = "";
        $indentSize = 2;
        $source1 = $source1;
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//              $n .= $deepCount.'|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);
              $cut['before'] .= $n . $indentStr . $new_open_default;
              // return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//              $n .= $deepCount.':';
//              $charSpace ='`';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//              $n .= $deepCount.';';
//              $charSpace ='´';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $cut['behind'];

              // return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}
//        $actual = $cBefore . $content . $cBehind;

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_recursive_02() {
//        return true;
//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
        $LINE__ = __LINE__;

        $source1 = $LINE__ . ':a{b{c{o}c}b}a';

        $expected = $LINE__ . ':a[_´b[_´´c[_´´´o_´´`]_´´c_´`]_´b_`]_a';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = '[';
        $new_close_default = ']';
        $charSpace = ".";
        $newline = "_";
        $indentSize = 1;

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
              $charSpace = "'";
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);
              $cut['before'] .= $new_open_default;
//              return $cut  ;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
              $charSpace = '´';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) {
//                  return $cut['behind'];
//                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
              $charSpace = '`';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

//              $cut['behind'] .= $indentStr . $new_close_default . $n;
              $cut['middle'] .= $indentStr . $new_close_default . $n . $cut['behind'];

              // return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_simple3() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':
a{b{B}}';
        $old_open = '{';
        $old_close = '}';
        $new_open_default = '[';
        $new_close_default = ']';
        $charSpace = ".";
        $newline = "\r\n";
        $indentSize = 2;
        $source1 = $source1;
        $expected = $LINE__ . ':
a
1|[
1:..b
2|..[
2:....B
2:..]
1:]';

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        //        $openFunc = null,
//      $contentFunc = null,
//      $closeFunc = null

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
              $n .= $deepCount . '|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);
              $cut['before'] .= $n . $indentStr . $new_open_default;

              // return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
              $n .= $deepCount . ':';
//              $charSpace ='`';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
              $n .= $deepCount . ';';
//              $charSpace ='´';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $cut['behind'];

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}
//        $actual = $cBefore . $content . $cBehind;

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }
    function test_simple() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':
a{A}b{B}';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = '[';
        $new_close_default = ']';
        $charSpace = ".";
        $newline = "\r\n";
        $indentSize = 2;

        $source1 = $source1;

//        $expected = $LINE__.':1[_.2[_..3[_...o_...]_3_..]_2_.]_1';
        $expected = $LINE__ . ':
a
1|[
1:..A
1:]
1;b
1|[
1:..B
1:]
1;';

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        //        $openFunc = null,
//      $contentFunc = null,
//      $closeFunc = null
        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
              $n .= $deepCount . '|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $n . $indentStr . $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
              $n .= $deepCount . ':';
//              $charSpace ='`';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
              $n .= $deepCount . ';';
//              $charSpace ='´';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

//              $cut['behind'] .= $indentStr . $new_close_default . $n;
//              $cut['behind'] = $indentStr . $new_close_default . $n . $cut['behind'];
              $cut['middle'] .= $indentStr . $new_close_default . $n . $cut['behind'];

              // return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}
//        $actual = $cBefore . $content . $cBehind;

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_15_() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':
if(a1){$A1;}if(a2){$A2;}';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = '[';
        $new_close_default = ']';
        $charSpace = ".";
        $newline = "\r\n";
        $indentSize = 2;

        $source1 = $source1;

//        $expected = $LINE__.':1[_.2[_..3[_...o_...]_3_..]_2_.]_1';
        $expected = $LINE__ . ':
if(a1)
1|[
1:..$A1;
1:]
1;if(a2)
1|[
1:..$A2;
1:]
1;';

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        //        $openFunc = null,
//      $contentFunc = null,
//      $closeFunc = null

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
              $n .= $deepCount . '|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $n . $indentStr . $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
              $n .= $deepCount . ':';
//              $charSpace ='`';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
              $n .= $deepCount . ';';
//              $charSpace ='´';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

//              $cut['behind'] .= $indentStr . $new_close_default . $n;
              $cut['middle'] .= $indentStr . $new_close_default . $n . $cut['behind'];

              // return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}
//        $actual = $cBefore . $content . $cBehind;

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_shortest_new_close_recursive() {
//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
        $LINE__ = __LINE__;

        $source1 = $LINE__ . ':{}';
        $expected = $LINE__ . ':{#';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = $old_open;
        $new_close_default = '#';
//        $new_close_default = $old_close; // this line is reason for endless loop
        $charSpace = "";
        $newline = "";
        $indentSize = 1;

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
//          $charSpace ='´';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
//          $charSpace ='`';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $n;

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_reformat_compressed_AutoHotKey() {
//        return true;
//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
        $LINE__ = __LINE__;

        $source1 = $LINE__ . ':{{o}}';
        $expected = $LINE__ . ':{n {n on >n >';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = $old_open;
        $new_close_default = '>';
//        $new_close_default = $old_close; // this line is reason for endless loop
        $charSpace = " ";
        $newline = "n";
//        $newline = "aölsdkfjösaldkjfsöalfdkj"; // see closure functions
        $indentSize = 1;

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
//          $charSpace = "'";
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
//          $charSpace ='´';
              $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
//          $charSpace ='`';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $cut['behind'];

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });
//      {{o}}
//    $actual = $cBefore . $content . $cBehind;

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_shortest_lette_in_middle_recursive() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':{l}';
        $expected = $LINE__ . ':{l}';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = '{';
        $new_close_default = '}';
//        $new_close_default = $old_close; // this line is reason for endless loop
        $charSpace = "";
        $newline = "";
        $indentSize = 1;

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
//          $charSpace = "'";
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
//          $charSpace ='´';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // , function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
//          $charSpace ='`';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $n;

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_recursive_01() {
//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':{k}';
        $expected = $LINE__ . ':[k]';
        $old_open = '{';
        $old_close = '}';
        $charSpace = ' ';
        $charSpace = '';
        $newline = "\n";
        $newline = "";
        $indentSize = 1;
        $new_open_default = '[';
        $new_close_default = ']';

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
//          $charSpace = "'";
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
//          $charSpace ='´';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
//          $charSpace ='`';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $n;

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });
        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);
    }

    function test_shortest_new_open_recursive() {
        $LINE__ = __LINE__;
        $source1 = $LINE__ . ':{}';
        $expected = $LINE__ . ':#}';
        $old_open = '{';
        $old_close = '}';

        $new_open_default = '#';
        $new_close_default = '}';
//        $new_close_default = $old_close; // this line is reason for endless loop
        $charSpace = "";
        $newline = "";
        $indentSize = 1;

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
//          $charSpace = "'";
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
//          $charSpace ='´';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut['middle']);
              $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
//          $charSpace ='`';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);

              $cut['middle'] .= $indentStr . $new_close_default . $n;

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

//      {{o}}

        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);

    }


    function test_recursion_simplyReproduction() {
        # this recursion is deprecated and not implemented into the core class. so dont waste time ;)
//        return false;
        $expected = 'A {11{22{3}{2}22}11}{1} B';
        $cf = new SL5_preg_contentFinder($expected);
        list($c, $bf, $bh) = recursion_simplyReproduction($expected);
        $actual = $bf . $c . $bh;
        $cf->setBeginEnd_RegEx('{', '}');
        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);
    }


    /**
     * using class SL5_preg_contentFinder
     * and ->getContent_user_func_recursive.
     * in a case i don't like this style using closures to much. so you only need one function (advantage) from the outside. but looks more ugly from the inside. not best way for debugging later (inside). you need to compare, decide for your business.
     */

    function test_callback_with_closures() {
        $source1 = '_if(X1){$X1;if(X2){$X2;}}';
        $expected = '_if(X1)[
..$X1;if(X2)[
....$X2;
..]
]';
        $old_open = '{';
        $old_close = '}';
        $new_open_default = '[';
        $new_close_default = ']';
        $charSpace = ".";
        $newline = "\r\n";
        $indentSize = 2;

        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($old_open, $old_close);

        $getIndentStr = function ($indent, $char, $indentSize) {
            $multiplier = $indentSize * $indent;
            $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

            return $indentStr;
        };

        $actual = $cf->getContent_user_func_recursive(
          function ($cut, $deepCount) use ($new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
              $n = $newline;
//          $n .= $deepCount.'|';
//          $charSpace = "'";
              $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

              $cut['before'] .= $new_open_default;
// return $cut;
              // }, function ($cut, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
              if($cut['middle'] === false) return $cut;
              $n = $newline;
//          $n .= $deepCount.':';
//          $charSpace ='´';
              $indentStr = $getIndentStr(1, $charSpace, $indentSize);
              $cut['middle'] = $n . $indentStr . preg_replace('/' . preg_quote($n) . '[ ]*([^\s\n]+)/', $n . $indentStr . "$1", $cut['middle']);

//          $cut['middle'] .= $n;

              // return $cut;
              // }, function ($cut, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
              if($cut['middle'] === false || $cut['behind'] === false) {
//                  return $cut['behind'];
                  return false;
              }
              $n = $newline;
//          $n .= $deepCount.';';
//          $charSpace ='-';
//          $indentStr = $getIndentStr(0, $charSpace, $indentSize);

              $cut['middle'] .= $n . $new_close_default;

// return $cut;
              return $cut; # todo: $cut['behind'] dont need newline at the beginning
          });

        if(class_exists('PHPUnit_Framework_TestCase')) {
            $this->assertEquals($expected,
              $actual);
        }
    }


    function test_reformatCode_recursion_add() {
        $source1 = "if(InStr(tc,needle)){win:=needle}else{win:=needle2}";
        $expected =
          "if(InStr(tc,needle)){
   win:=needle;
}else{
   win:=needle2;
}";
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx('{', '}');
        list($c, $bf, $bh) = self::recursion_add($source1, "{\r\n   ", ";\r\n}");
        $actual = $bf . $c . $bh;
        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals($expected, $actual);
        if(class_exists('PHPUnit_Framework_TestCase')) $this->assertEquals(strlen($expected), strlen($actual));
    }


    static function recursion_add(
      $content,
      $addBefore = null,
      $addBehind = null,
      $before = null,
      $behind = null
    ) {
        $isFirstRecursion = is_null($before); # null is used as trigger for first round.
        $cf = new SL5_preg_contentFinder($content);
        if($cut['middle'] = @$cf->getContent($b = '{', $e = '}')) {
            $before .= $cf->getContent_Before() . $addBefore;
            $behindTemp = $cf->getContent_Behind() . $behind;

            if($isFirstRecursion) {
                list($c, $bf, $bh) =
                  self::recursion_add($behindTemp,
                    $addBefore,
                    $addBehind); // this version of recursion also includes the rest of contentDemo.
                $behind = (is_null($c)) ? $addBehind . $behindTemp : $addBehind . $bf . $c . $bh;
            }
            else {
                $behind = $addBehind . $behindTemp;
            }

            $return = self::recursion_add(
              $cut['middle'],
              $addBefore,
              $addBehind,
              $before,
              $behind
            );

            return $return;
        }
        $return = array($content, $before, $behind); // core element.
        return $return;
    }


}

?>