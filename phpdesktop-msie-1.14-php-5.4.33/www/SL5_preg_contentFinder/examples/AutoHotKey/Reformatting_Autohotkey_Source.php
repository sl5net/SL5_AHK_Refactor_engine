little example with funny result :)
output_reformatted.ahk is generated from very compressed file input_compressed.ahk
<?php
require("../../SL5_preg_contentFinder.php");
# http://php.net/manual/de/features.commandline.php
//parse_str(implode('&', array_slice($argv, 1)), $_GET);
if(!isset($argv[1])) {
    $argv[1] = '--source1="E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\test.ahk" --renameSymbol="Mod" --renameSymbol_To="zzzzzzz"';
    $argv[1] = '--source1="E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\test.ahk" renameSymbol="zzzzzzz" renameSymbol_To="rrrrrrrrr"';
    $argv[1] = '--source1="E:\fre\private\HtmlDevelop\AutoHotKey\SL5_AHK_Refactor_engine_gitHub\test.ahk" --A_ThisLabel="Alt & Down"';
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
        $old_open = '(' . $fArgs . '\s*[^{;\n]*)\{[\s\n]';
    }
    else {
        # } else {
        $old_open = '([^\n{;]*)\{[^\w%$`]'; # problem this searchs in comments
        $old_open = '^([^{;]*?)\{[^\w%$`]'; #
//        $old_open = '\{';
        $old_open = '^([^{;\n]*)\{[\s\n]';# todo: problem. dont finds:  } else {
    }

    $old_close = '^[ ]*\}?([^{};\n\r]*?)\}?[\s\n\r]';
    $old_close = '^[ ]*\}?([^{};\n\r]*)\}';
    $old_close = '(\n\s*)\}';
//    $old_close = '^([^{};\n\r]*)\}[ ]*$';
//    $old_close = '\}';
//Send,{CtrlUp} {Blind}


    $new_open_default = '[ ';
    $new_close_default = ']';
    $new_open_default = '{ ';
    $new_close_default = '}';
    $charSpace = " ";
    $newline = "\r\n";
    $indentSize = 3;

//    $dirtyBugFix = ' a{b}c';
    $file_content = trim(preg_replace('/^[ ]+/smi', '', $file_content));
//    $file_content .= $dirtyBugFix;

    $getIndentStr = function ($indent, $char, $indentSize) {
        $multiplier = $indentSize * $indent;
        $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

        return $indentStr;
    };

    $indentStr = $getIndentStr(1, $charSpace, $indentSize);

    $file_content = preg_replace( '/^\s*\}\s*else(\s+if\s*\([^\n\r]+\)\s*)?\s*\{+/smi', "} \nelse $1 {", $file_content); // dirty BugFix .. need temporary newline that script later works correct

    $file_content = preg_replace(
      '/(\s*\bif\s*\([^\n\r)]+\)\s*)[\n\r]+([^{\s])/smi', "$1\n".$indentStr."$2", $file_content); // dirty BugFix

    $file_content = preg_replace(
      '/(\s*\belse\s*)[\n\r]+([^{\s])/smi', "$1\n".$indentStr."$2", $file_content); // dirty BugFix


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
//}   else if(RegExMatch(c, ":" )) {

//          $charSpace = '.';
          $indentStr = $getIndentStr(1, $charSpace, $indentSize);


          if(!isset($posList0['begin_end'])) $posList0['begin_end'] = strlen($source1);
          $start = '' . substr($source1, $posList0['begin_begin'], $posList0['begin_end'] - $posList0['begin_begin']) . '';
          $end = '' . ltrim(substr($source1, $posList0['end_begin'], $posList0['end_end'] - $posList0['end_begin'])) . '';

          if(@$arguments['A_ThisLabel'] == "Alt & Up" || @$arguments['A_ThisLabel'] == "Alt & Down"
            || !@empty($arguments['renameSymbol']) && !empty($arguments['renameSymbol_To'])
          ) {
              $markerXXXXstring = "xxxxxxxx" . "xxxxxxxx ";
              $strposMarker = strpos($cut['middle'], $markerXXXXstring);
              if($strposMarker > 0) {

                  if(@$arguments['A_ThisLabel'] == "Alt & Up" || @$arguments['A_ThisLabel'] == "Alt & Down") {
                      if(@$arguments['A_ThisLabel'] == "Alt & Down") {

                          preg_match_all('/\n/', substr($cut['middle'], $strposMarker + strlen($markerXXXXstring)), $matches);
                          $command = 'Down';
                      }
                      else {
                          preg_match_all('/\n/', substr($cut['middle'], 0, $strposMarker), $matches);
                          $command = 'Up';
                      }
                      $linesAboveMarker = count($matches[0]) + 1;

//                      $fileAddress = realpath('../../SL5_phpGeneratedRunOnChanged.ahk');
//                      $fileAddress = realpath('p.txt');
                      $fileAddress = 'SL5_phpGeneratedRunOnChanged.ahk';
                      $pathinfo = pathinfo($fileAddress);
                      if(!file_exists($fileAddress)) {
                          die("!file_exists($fileAddress) 15-07-06_14-26");
                      }
                      $contents = file_get_contents($fileAddress);
                      if(!$contents) {
                          die('!$contents 15-07-06_14-18 \n $contents=' . $contents . '$fileAddress=' . $fileAddress);
                      }
                      $ahkContent =
                        '
Suspend,on
; Send,^z
; Sleep,50
Send,{' . $command . ' ' . $linesAboveMarker . '}
Suspend,off
';
                      $contents = preg_replace('/<body>.*<\/body>/ism', "<body>\n" . $ahkContent . "\n;</body>", $contents);
//                      $fileAddressSaved = realpath('../../../../../' . $fileAddress . '.ahk');
                      $fileAddressSaved = '../../../../../' . $fileAddress . '.ahk';
                      echo $fileAddressSaved;
                      file_put_contents($fileAddressSaved, $contents);

                  }

                  # cut out markerString
                  $cut['middle'] = preg_replace('/;\s*' . $markerXXXXstring . '/', '', $cut['middle']);

                  if(!@empty($arguments['renameSymbol'])) {
                      $start = preg_replace('/\b(' . $arguments['renameSymbol'] . ')\b/', $arguments['renameSymbol_To'], $start);
                      $cut['middle'] = preg_replace('/\b(' . $arguments['renameSymbol'] . ')\b/', $arguments['renameSymbol_To'], $cut['middle']);
                  }
              }

          }


          $cut['middle'] = '' . rtrim($start) . $n . $indentStr
            . trim(preg_replace('/\n/', "\n" . $indentStr, $cut['middle']));
//          $charSpace = '.';
          $indentStr = $getIndentStr(0, $charSpace, $indentSize);
          $cut['middle'] .= $n . $indentStr . $end . $cut['behind'];


          return $cut;
      });

//    $actual = substr($actual, 0, -strlen($dirtyBugFix));
    $actual = preg_replace('/(\})[\s\n\r]*(else(\s+if\s*\([^\n\r]+\)\s*)?\s*\{+)/smi', "$1$2", $actual); // dirty BugFix
# ([^\n\r]+\)
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
