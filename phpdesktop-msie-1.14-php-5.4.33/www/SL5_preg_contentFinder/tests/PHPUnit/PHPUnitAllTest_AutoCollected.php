 <h1>Dont edit this file. its overwritten next !</h1> \n  <?php
 $f = 'SL5_preg_contentFinder.php';
 while(!file_exists($f)) {
    $f = '../' . $f;
    echo "$f exist.";
}
include_once $f;
include_once '_callbackShortExample.php';
   class TestAll extends PHPUnit_Framework_TestCase {
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


    /**
     * empty means it found an empty.
     * false means nothing was found.
     */
    function test_false_versus_empty() {

        $cfEmpty_IfEmptyResult = new SL5_preg_contentFinder("{}");
        $cfEmpty_IfEmptyResult->setBeginEnd_RegEx('{', '}');
        $contentEmpty = $cfEmpty_IfEmptyResult->getContent();
        $this->assertTrue($contentEmpty === "");
        $this->assertTrue($contentEmpty !== false);
        $this->assertTrue($contentEmpty !== null);

        $cf_False_IfNoResult = new SL5_preg_contentFinder("mi{SOME}mo");
        $cf_False_IfNoResult->setBeginEnd_RegEx('[', ']');
        $contentFalse = $cf_False_IfNoResult->getContent();
        $this->assertTrue($contentFalse !== "");
        $this->assertTrue($contentFalse === false);
        $this->assertTrue($contentFalse !== null);
    }


    /**
     * echo and return from a big string a bit of the start and a bit from the end.
     */
    function test_echo_content_little_excerpt() {
        $cf = new SL5_preg_contentFinder("dummy");
        $this->assertEquals("12...45", $cf->echo_content_little_excerpt("12345", 2, 2));
    }

    /**
     * nl2br_Echo returns nothong, returns null. it simly echo
     */
    function test_nl2br_Echo() {
        $cf = new SL5_preg_contentFinder(123456);
        $this->assertEquals($cf->nl2br_Echo(__LINE__, "filename", "<br>"), null);
    }
    /**
     * getContent_Next returns false if there is not a next contentDemo
     */
    function test_getContentNext() {
        $cf = new SL5_preg_contentFinder(123456);
        $this->assertEquals(false, $cf->getContent_Next());
    }
    /**
     * false if parameter is not  'pos_of_next_search' or 'begin' or 'end'
     */
    function test_CACHE_current() {
        $cf = new SL5_preg_contentFinder(123456);
        $this->assertEquals(false, $cf->CACHE_current());
    }
    /**
     * CACHE_current: false if there is no matching cache. no found contentDemo.
     */
    function test_CACHE_current_begin_end_false() {
        $cf = new SL5_preg_contentFinder(123456);
        $this->assertEquals(false, $cf->CACHE_current("begin"));
        $this->assertEquals(false, $cf->CACHE_current("end"));
    }
    /**
     * CACHE_current: simply the string of the current begin / end quote
     */
    function test_CACHE_current_begin_end() {
        $cf = new SL5_preg_contentFinder(00123456);
        $cf->setBeginEnd_RegEx('2', '4');
        $this->assertEquals(2, $cf->CACHE_current("begin"));
        $this->assertEquals(4, $cf->CACHE_current("end"));
    }


    /**
     * getContent ... gives false if there isn't a contentDemo. if it found a contentDemo it gives true
     */
    function test_getContent() {
        $cf = new SL5_preg_contentFinder("00123456");
        $cf->setBeginEnd_RegEx('2', '4');
        $this->assertEquals(false, $cf->getContent_Prev());
        $this->assertEquals(false, $cf->getContent_Next());
        $this->assertEquals(3, $cf->getContent());
    }
    function test_wrongSource_NIX_getContent() {
        $source1 = '{NIX';
        $expected = 'NIX';
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx('{', '}');
        $this->assertEquals($expected, $cf->getContent());
    }

    function test_wrongSource_NIXNIX_getContent() {
        $source1 = "{NIX{}";
        $expected = 'NIX{';
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx('{', '}');
        $this->assertEquals($expected, $cf->getContent());
    }

    function test_getUniqueSignExtreme() {
        $cf = new SL5_preg_contentFinder(123456);
        $cf->isUniqueSignUsed = true; # needs to switched on first !! performance reasons
        $cf->setBeginEnd_RegEx('2', '4');
        $cf->getContent(); # needs to be searched first !! performance reasons
        $probablyUsedUnique = chr(007);
        $this->assertEquals($probablyUsedUnique, $cf->getUniqueSignExtreme());
    }

    function test_protect_a_string() {
        $cf = new SL5_preg_contentFinder('"{{mo}}"');
        $cf->isUniqueSignUsed = true; # needs to switched on first !! performance reasons
        $cf->setBeginEnd_RegEx('{', '}');
        $content = $cf->getContent(); # needs to be searched first !! performance reasons
        $mo = '{mo}';
        $this->assertEquals('_' . $mo, '_' . $content);
        $uniqueSignExtreme = $cf->getUniqueSignExtreme();

        $o = $uniqueSignExtreme . 'o';
        $c = $uniqueSignExtreme . 'c';
        $content1 = str_replace(['{', '}'], [$o, $c], $content);
        $contentRedo = str_replace([$o, $c], ['{', '}'], $content1);
        $this->assertEquals('-' . $contentRedo, '-' . $content);

        $cf2 = new SL5_preg_contentFinder($content1);
        $cf2->setBeginEnd_RegEx('{', '}');
        $content2 = $cf2->getContent();
        $content_Before = $cf2->getContent_Before();
        $content_Behind = $cf2->getContent_Behind();
        $content3 = str_replace([$o, $c], ['{', '}'], $content2);
        $this->assertEquals('', $content_Before . $content3 . $content_Behind); # means cut is not  found / created.
    }

    /**
     * get_borders ... you could get contents by using substr.
     * its different to getContent_Prev (matching contentDemo)
     */
    function test_content_getBorders_before() {
        $content = "before0[in0]behind0,before1[in1]behind1";
        $cf = new SL5_preg_contentFinder($content);
        $cf->setBeginEnd_RegEx('[', ']');
        $this->assertEquals("before0", substr($content, 0, $cf->getBorders()['begin_begin']));
    }

    /**
     * get_borders ... you could get contents by using substr.
     * its different to getContent_Prev (matching contentDemo)
     *
     * todo: discuss getContent_Next ?? discuss getContent_Behind ?? (15-06-16_10-28)
     */
    function test_content_getBorders_behind() {
        $content = "before0[in0]behind0,before1[in1]behind1";
        $cf = new SL5_preg_contentFinder($content);
        $cf->setBeginEnd_RegEx('[', ']');
        $this->assertEquals("behind0,before1[in1]behind1", substr($content, $cf->getBorders()['end_end']));
//        $this->assertEquals(false, $cf->getContent_Next());
    }
    /**
     * gets contentDemo using borders with substring
     */
    function test_getContentBefore_delimiterWords() {
        $cf = new SL5_preg_contentFinder("1_before0_behind0_2");
        $cf->setBeginEnd_RegEx('before0', 'behind0');
        $this->assertEquals("1_", $cf->getContent_Before());
        $this->assertEquals("_2", $cf->getContent_Behind());
    }
    /**
     * gets contentDemo using borders with substring
     */
    function test_getContentBefore() {
        $cf = new SL5_preg_contentFinder("before0[in0]behind0,before1[in1]behind1");
        $cf->setBeginEnd_RegEx('[', ']');
        $this->assertEquals("before0", $cf->getContent_Before());
    }
    /**
     *  gets contentDemo using borders with substring
     */
    function test_getContentBehind() {
        $cf = new SL5_preg_contentFinder("before0[in0]behind0,before1[in1]behind1");
        $cf->setBeginEnd_RegEx('[', ']');
        $this->assertEquals("behind0,before1[in1]behind1", $cf->getContent_Behind());

    }


    /**
     * todo: needs discussed
     */
    function test_getContent_ByID_1() {
        $cf = new SL5_preg_contentFinder("{2_{1_2}_");
        $cf->setBeginEnd_RegEx('{', '}');
        $this->assertEquals(null, $cf->getID());
        $this->assertNotEquals('-' . 0 . '-', '-' . $cf->getID() . '-');
    }


    /**
     * setID please use integer not text. why?
     * todo: needs discussed
     */
    function test_getContent_setID() {
        $cf = new SL5_preg_contentFinder("{2_{1_2}_");
        $cf->setBeginEnd_RegEx('{', '}');
        $cf->setID(1);
        $content1 = $cf->getContent();
        $this->assertEquals('1: ' . "2_{1_2", '1: ' . $content1);;
//        $this->assertEquals($content1, $cf->getContent_ByID(1));# todo: dont work like expected
        $cf->setBeginEnd_RegEx('1', '2');
        $cf->setID(2);
//        $this->assertEquals($content1, $cf->getContent_ByID(1));;
//        $this->assertEquals("1_2", $cf->getContent_ByID(2));;
//        $this->setExpectedException('InvalidArgumentException');
    }
    /**
     * todo: needs discussed
     */
    function test_getContent_ByID_3() {
        $cf = new SL5_preg_contentFinder("{2_{1_2}_");
        $cf->setBeginEnd_RegEx('{', '}');
//        $this->assertEquals("2_{1_2", $cf->getContent_ByID(0)); # dont work like expected
    }
    /**
     * getContent takes the first. from left to right
     */
    function test_getContent_2() {
        $cf = new SL5_preg_contentFinder("{2_{1_2}_2}_3}{_4}");
        $cf->setBeginEnd_RegEx('{', '}');
        $this->assertEquals("2_{1_2}_2", $cf->getContent());
    }
    /**
     * Prev and Next using getContent_ByID
     * todo: discuss
     */
    function test_getContent_Prev_Next() {
        $cf = new SL5_preg_contentFinder("(1_3)_2_3_(_a)o");
        $cf->setBeginEnd_RegEx('(', ')');
        $this->assertEquals("1_3", $cf->getContent());
//        $this->assertEquals(false, $cf->getContent_Prev()); # todo: dont work like expected
//        $this->assertEquals("_a", $cf->getContent_Next()); # todo: dont work like expected
    }
    /**
     * Prev and Next using getContent_ByID
     * todo: discuss
     */
    function test_getContent_Prev_Next_3() {
        $source1 = "{1_4}_2_3_{_b}o";
        $expected = "1_4";
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx('{', '}');
        $this->assertEquals($expected, $cf->getContent());
        $this->assertEquals(false, $cf->getContent_Prev());
        $this->assertEquals(false, $cf->getContent_Next());
    }

    function test_128() {
        $source1 = "(1((2)1)8)";
        $expected = "(1((2)1)8)";
        $cf = new SL5_preg_contentFinder($source1);
        $actual = '(' . $cf->getContent($b = '(', $e = ')') . ')';
        $this->assertEquals($expected, $actual);
    }

    function test_123_abc() {
        # problem: Finally, even though the idea of nongreedy matching comes from Perl, the -U modifier is incompatible with Perl and is unique to PHP's Perl-compatible regular expressions.
        # http://docstore.mik.ua/orelly/webprog/pcook/ch13_05.htm
        $content1 = '123#abc';
        $cf = new SL5_preg_contentFinder($content1);
        $expected = @$cf->getContent(
          $begin = '\d+',
          $end = '\w+',
          $p = null,
          $t = null,
          $searchMode = 'dontTouchThis'
        );
        $expectedContent = '#';
        $this->assertEquals($expected, $expectedContent);
    }

    function test_2_1() {
        $expected = "((2)1)";
        $cf = new SL5_preg_contentFinder($expected);
        $actual = '(' . @$cf->getContent($b = '(', $e = ')') . ')';
        $this->assertEquals($expected, $actual);
    }


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