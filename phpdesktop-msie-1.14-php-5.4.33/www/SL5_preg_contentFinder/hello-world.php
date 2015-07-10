<?php
# http://php.net/manual/de/features.commandline.php
//parse_str(implode('&', array_slice($argv, 1)), $_GET);

$t = time();
function arguments($argv) {
    $_ARG = array();
    foreach($argv as $arg) {
        if(ereg('--([^=]+)=(.*)', $arg, $reg)) {
            $_ARG[$reg[1]] = $reg[2];
        }
        elseif(ereg('-([a-zA-Z0-9])', $arg, $reg)) {
            $_ARG[$reg[1]] = 'true';
        }

    }

    return $_ARG;
}

require("SL5_preg_contentFinder.php");
if(isset($argv)) {
    $arguments = arguments($argv);
    $fileAddress = (isset($arguments['source1'])) ? $arguments['source1'] : '';
}
if(!isset($fileAddress) || !$fileAddress || empty($fileAddress)) {
    $fileAddress = 'E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\keys_SL5_AHK_Refactor_engine.ahk';
//    $f_input_compressed = 'E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\dummy.ahk';
}
//$date = new DateTime();
$timeStamp = (new DateTime())->format('Y-m-d_H-i-s') ;
$file_content = file_get_contents($fileAddress);
file_put_contents($fileAddress . '.backup'.$timeStamp.'.ahk', $file_content);
$actual = reformat_AutoHotKey($file_content);
file_put_contents($fileAddress , $actual);

function reformat_AutoHotKey($file_content) {
    if(!isset($file_content)) die('15-06-25_15-07 $f_input');
//    $file_content = file_get_contents($f_input);

    $old_open = '^([^{;\n]*)\{[\s\n]';
    $old_close = '^([^};\n\r]*)\}[\s\n\r]';

    $new_open_default = '[ ';
    $new_close_default = ']';
    $new_open_default = '{ ';
    $new_close_default = '}';
    $charSpace = " ";
    $newline = "\r\n";
    $indentSize = 3;

    $file_content = trim(preg_replace('/^\s+/smi', '', $file_content));
    $cf = new SL5_preg_contentFinder($file_content);
    $cf->setBeginEnd_RegEx($old_open, $old_close);
    $cf->setSearchMode('dontTouchThis');

    $getIndentStr = function ($indent, $char, $indentSize) {
        $multiplier = $indentSize * $indent;
        $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

        return $indentStr;
    };

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

          $start = '' . substr($source1, $posList0['begin_begin'], $posList0['begin_end'] - $posList0['begin_begin']) . '';
          $end = '' . substr($source1, $posList0['end_begin'], $posList0['end_end'] - $posList0['end_begin']) . '';

          $cut['middle'] = $start . $n . $indentStr
            . trim(preg_replace('/\n/', "\n" . $indentStr, $cut['middle']));
//          $charSpace = '.';
          $indentStr = $getIndentStr(0, $charSpace, $indentSize);
          $cut['middle'] .= $n . $indentStr . $end . $n . $cut['behind'];

          return $cut;
      });

    return $actual;
}

