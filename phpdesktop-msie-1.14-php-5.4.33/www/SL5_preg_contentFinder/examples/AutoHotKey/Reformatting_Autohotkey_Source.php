little example with funny result :)
output_reformatted.ahk is generated from very compressed file input_compressed.ahk
<?php
require("../../SL5_preg_contentFinder.php");
# http://php.net/manual/de/features.commandline.php
//parse_str(implode('&', array_slice($argv, 1)), $_GET);
if(!isset($argv[1])) {
    $argv[1] = '--source1="E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\test.ahk" --renameSymbol="Mod" --renameSymbol_To="zzzzzzz"';
    $argv[1] = '--source1="E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\test.ahk" renameSymbol="zzzzzzz" renameSymbol_To="rrrrrrrrr"';
}
if(isset($argv)) {
    $arguments = arguments($argv);
    $fileAddress = (isset($arguments['source1'])) ? $arguments['source1'] : '';
//    $fileAddress = (isset($arguments['source1'])) ? $arguments['source1'] : '';
}

if(!isset($fileAddress) || !$fileAddress || empty($fileAddress)) {

    $fileAddress = 'input_compressed_2.ahk';
    $file_content = file_get_contents($fileAddress);
    $fileAddress = 'output_reformatted_2.ahk';
    $arguments = null;
}
else {
    $file_content = file_get_contents($fileAddress);

}
$timeStamp = (new DateTime())->format('s'); // Y-m-d_H-s
file_put_contents($fileAddress . '.backup' . $timeStamp . '.ahk', $file_content);
$actual_content = reformat_AutoHotKey($file_content, $arguments);
file_put_contents($fileAddress, $actual_content);

function reformat_AutoHotKey($file_content, $arguments = null) {
    if(!isset($file_content)) die('15-06-25_15-07 $f_input');
    if(!@empty($arguments['renameSymbol'])) {
        $fArgs = '\([^)]*\)';
        $old_open = '('.$fArgs.'\s*[^{;\n]*)\{[\s\n]';
    }
    else {
        $old_open = '^([^{;\n]*)\{[\s\n]';
    }
    $old_close = '^([^};\n\r]*)\}[\s\n\r]';

    $new_open_default = '[ ';
    $new_close_default = ']';
    $new_open_default = '{ ';
    $new_close_default = '}';
    $charSpace = " ";
    $newline = "\r\n";
    $indentSize = 3;

    $file_content = trim(preg_replace('/^[ ]+/smi', '', $file_content));

    $getIndentStr = function ($indent, $char, $indentSize) {
        $multiplier = $indentSize * $indent;
        $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

        return $indentStr;
    };

    $indentStr = $getIndentStr(1, $charSpace, $indentSize);
//    $pattern = '([\r\n](If|#if)[a-z]+[ ]*[ ]*[^\n\r{]+)[ ]*[\r\n]+[ ]*(\w)';
    $pattern = '([\r\n](If|#if)([a-z]+[ ]*,|\()[ ]*[^\n\r{]+)[ ]*[\r\n]+[ ]*(\w)';
    $file_content = preg_replace('/' . $pattern . '/is', "$1" . $newline . $indentStr . "$4", $file_content);

    $cf = new SL5_preg_contentFinder($file_content);
    $cf->setBeginEnd_RegEx($old_open, $old_close);
    $cf->setSearchMode('dontTouchThis');


    /*
     *             $cut = call_user_func($func['open'], $cut, $deepCount + 1, $callsCount, $C->foundPos_list[0], $C->content);

     */
    $actual = $cf->getContent_user_func_recursive(

      function ($cut, $deepCount, $callsCount, $posList0, $source1) use ($arguments, $new_open_default, $new_close_default, $charSpace, $newline, $indentSize, $getIndentStr) {
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

          if(!@empty($arguments['renameSymbol']) && !empty($arguments['renameSymbol_To'])) {
              $markerXXXXstring = "xxxxxxxx" . "xxxxxxxx";
              if(strpos($cut['middle'], $markerXXXXstring) > 0) {

                  $cut['middle'] = preg_replace('/;\s*' . $markerXXXXstring . '[^\n]*/', '', $cut['middle']);

                  $start = preg_replace('/\b(' . $arguments['renameSymbol'] . ')\b/', $arguments['renameSymbol_To'], $start);
                  $cut['middle'] = preg_replace('/\b(' . $arguments['renameSymbol'] . ')\b/', $arguments['renameSymbol_To'], $cut['middle']);
              }

          }


          $cut['middle'] = '' . rtrim($start) . $n . $indentStr
            . trim(preg_replace('/\n/', "\n" . $indentStr, $cut['middle']));
//          $charSpace = '.';
          $indentStr = $getIndentStr(0, $charSpace, $indentSize);
          $cut['middle'] .= $n . $indentStr . $end . $cut['behind'];


          return $cut;
      });

    return $actual;
}

function arguments($argv) {
    $_ARG = array();
    foreach($argv as $arg) {
        if(preg_match_all('/--([^=]+)="?([^"]*)"?/', $arg, $reg)) {
            foreach($reg[1] as $k => $v) {
                $var = $reg[2][$k];
                $_ARG[$v] = $var;
            }

        }
        elseif(preg_match_all('/-([^=]+)="?([^"]*)"?/', $arg, $reg)) {
            $_ARG[$reg[1]] = 'true';
        }

    }

    return $_ARG;
}
