<?php
require("../../SL5_preg_contentFinder.php");
//{{o}}

//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
//test_reformat_compressed_AutoHotKey();
test_shortest_lette_in_middle_recursive();
function test_shortest_lette_in_middle_recursive() {
//        return true;
//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
    $file_content_compressed = '{
    l
    }';
    $expected = '{l}';
    $old_open = '[';
    $old_close = ']';

    $new_open_default = '{';
    $new_close_default = '}';
//        $new_close_default = $old_close; // this line is reason for endless loop
    $charSpace = "";
    $newline = "";
    $indentSize = 1;

    $cf = new SL5_preg_contentFinder($file_content_compressed);
    $cf->setBeginEnd_RegEx($old_open, $old_close);

    $getIndentStr = function ($indent, $char, $indentSize) {
        $multiplier = $indentSize * $indent;
        $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

        return $indentStr;
    };

    list($cBefore, $content, $cBehind) = $cf->getContent_user_func_recursive(
      function ($before, $cut, $behind, $deepCount) use ($new_open_default) {
          if($deepCount>50)
          {
              die(__LINE__. ':to much for this example. $deepCount=' . $deepCount);
          }
          return $before . $new_open_default; },
      function ($before, $cut, $behind, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
          if($deepCount>55)
          {
              die(__LINE__. ':to much for this example. $deepCount=' . $deepCount);
          }
          if($cut === false) return $cut;
          $n = $newline;
          $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
          $cut = $n . $indentStr . preg_replace('/' . $n . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut);
          $cut .= $n;

          return $cut;
      },
      function ($before, $cut, $behind, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
          if($deepCount>50)
          {
              die(__LINE__. ':to much for this example. $deepCount=' . $deepCount);
          }
          if($cut === false || $behind === false) return $behind;
          $n = $newline;
          $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

          return $indentStr . $new_close_default . $n . ltrim($behind);
          # todo: $behind dont need newline at the beginning
      });

//      {{o}}
    $file_content_reformatted = $cBefore . $content . $cBehind;

    $this->assertEquals($expected, $file_content_reformatted);

}

function test_reformat_compressed_AutoHotKey() {
//    include_once 'input_compressed.ahk'
//    $file_content_original = file_get_contents('SciTEUpdate.ahk');
    $file_content_compressed = '1{2{o}2}1';

    $expected = '1{_..2{_....o_>2_..>1';
    $old_open = '{';
    $old_close = '}';

    $new_open_default = $old_open;
    $new_close_default = '>';
    $charSpace = ".";
    $newline = "_";
    $indentSize = 2;

    $cf = new SL5_preg_contentFinder($file_content_compressed);
    $cf->setBeginEnd_RegEx($old_open, $old_close);

    $getIndentStr = function ($indent, $char, $indentSize) {
        $multiplier = $indentSize * $indent;
        $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

        return $indentStr;
    };

    list($cBefore, $content, $cBehind) = $cf->getContent_user_func_recursive(
      function ($before, $cut, $behind, $deepCount) use ($new_open_default) {
          if($deepCount>50)
          {
              die(__LINE__. ':to much for this example. $deepCount=' . $deepCount);
          }
          return $before . $new_open_default; },
      function ($before, $cut, $behind, $deepCount) use ($charSpace, $newline, $indentSize, $getIndentStr) {
          if($deepCount>55)
          {
              die(__LINE__. ':to much for this example. $deepCount=' . $deepCount);
          }
          if($cut === false) return $cut;
          $n = $newline;
          $indentStr = $getIndentStr($deepCount, $charSpace, $indentSize);
          $cut = $n . $indentStr . preg_replace('/' . $n . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut);
          $cut .= $n;

          return $cut;
      },
      function ($before, $cut, $behind, $deepCount) use ($new_close_default, $newline, $charSpace, $indentSize, $getIndentStr) {
          if($deepCount>50)
          {
              die(__LINE__. ':to much for this example. $deepCount=' . $deepCount);
          }
          if($cut === false || $behind === false) return $behind;
          $n = $newline;
          $indentStr = $getIndentStr($deepCount - 1, $charSpace, $indentSize);

          return $indentStr . $new_close_default . $n . ltrim($behind);
          # todo: $behind dont need newline at the beginning
      });

//      {{o}}
    $file_content_reformatted = $cBefore . $content . $cBehind;
    if($expected!=$file_content_reformatted)
        echo(':( 15-06-19_20-37');
    $file_content_reformatted = $cBefore . $content . $cBehind;
//    $this->assertEquals('{{o}}', $file_content_reformatted);

}

