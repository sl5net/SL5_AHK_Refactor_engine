<?php

class SL5_preg_contentFinderTest1 {

    /**
     * setID please use integer not text. why?
     * todo: needs discussed
     */
    public static function test_getContent_setID() {
        $cf = new SL5_preg_contentFinder("{2_{1_2}_");
        $cf->setBeginEnd_RegEx('{', '}');
        $cf->setID(1);
        $content1 = $cf->getContent();
//        $this->assertEquals('1: ' . "2_{1_2", '1: ' . $content1);;
//        $this->assertEquals($content1, $cf->getContent_ByID(1));;
        $cf->setBeginEnd_RegEx('1', '2');
        $cf->setID(2);
//        $this->assertEquals($content1, $cf->getContent_ByID(1));;
//        $this->assertEquals("1_2", $cf->getContent_ByID(2));;
//        $this->setExpectedException('InvalidArgumentException');
    }


    public static function recursion_example4($silentMode) {
        if (true) {
            if (!$silentMode) {
                echo '<font style="font-family: monospace">';
            }

            $source = str_repeat(implode('', range(0, 9)), 2) . '0';
            $numbers = str_repeat(implode('', range(0, 9)), 2) . '0';
            $sourceArray = str_split($source);
            $source = '';
            $delimiters = array('(', 'anywayNumber', ')');
            foreach ($sourceArray as $pos => $v) {
                $source .= (($pos + 1) % 3 == 2) ? $v : $delimiters[$pos % 3];
            }
            if (!$silentMode) {
                echo __LINE__ . ':$contentDemo=<br>' . $source . '<br>';
            }
            if (!$silentMode) {
                echo $numbers . '<br><br>';
            }
            $cf = new SL5_preg_contentFinder($source);

            for ($i = 0; $i < 2; $i++) {
                # rebuild with search tool. find every number
                # do this many times should be no problem
                $rebuild = '';
                for ($pos = 0; $pos < count($sourceArray); $pos += 3) {
                    $p = $cf->getBorders( $b = '(', $e = ')', $pos);
                    if (is_null($p['begin_begin'])) {
                        die(__FUNCTION__ . __LINE__);
                    }

                    $rebuild_1 = '(' . $cf->getContent($b, $e, $pos) . ')';
                    $rebuild_2 = '(' . $cf->getContent() . ')';
                    if ($rebuild_1 != $rebuild_2) {
                        die(__FUNCTION__ . __LINE__ . ": '$rebuild_1' != '$rebuild_2' (rebuild_1 != rebuild_2");
                    }
                    $rebuild .= $rebuild_1;
                }
                if (!$silentMode) {
                    echo __LINE__ . ':$rebuild= <br>' . $rebuild . '<br>';
                }
                if (!$silentMode) {
                    echo $numbers . '<br>';
                }
                if ($source != $rebuild) {
                    die(__LINE__ . ":ERROR <br>$source != <br>$rebuild");
                }
                if (!$silentMode) {
                    echo '<hr>';
                }
                if (!$silentMode) {
                    echo '--:' . $numbers . '<br>';
                }
                for ($pos = 0; $pos < count($sourceArray); $pos += 3) {
                    if ($pos == 3) {
                        132465789;
                    }
                    $p = $cf->getBorders( '(', ')', $pos);
                    if (is_null($p['begin_begin'])) {
                        die(__FUNCTION__ . __LINE__);
                    }
                    if (!$silentMode) {
                        echo ($pos > 9) ? "$pos:" : "0$pos:";
                    }
                    if ($pos - 2 >= 0) {
                        if (!$silentMode) {
                            echo str_repeat('_', $pos - 2);
                        }
                    }
                    if (!$silentMode) {
                        echo $cf->getContent_Prev();
                    }
                    if ($pos > 0) {
                        if (!$silentMode) {
                            echo ')';
                        }
                    }
                    $cf->getBorders( '(', ')', $pos);
                    if (!$silentMode) {
                        echo '(' . @$cf->getContent();
                    }
                    if (!$silentMode) {
                        echo ')(';
                    }
                    $cf->getBorders( '(', ')', $pos);
                    if (!$silentMode) {
                        echo '' . $cf->getContent_Next();
                    }

                    if (!$silentMode) {
                        echo '<br>';
                    }
                }
            }

        }

        if (1) {
            ######## borders beetween #########
            $cf->getBorders( '(1)', '(7)', 0);
            $c = @$cf->getContent();
            if (!$silentMode) {
                echo __LINE__ . ': ' . $c . '<br>';
                echo __LINE__ . ':$rebuild= <br>' . $rebuild . '<br>';
                echo __LINE__ . ': BetweenID 0,2<br>=' . $cf->getContent_BetweenIDs(0, 2) . '<br>';
                echo __LINE__ . ': BetweenID 1,3<br>=' . $cf->getContent_BetweenIDs(1, 3) . '<br>';
                echo __LINE__ . ': BetweenID 2,4<br>=' . $cf->getContent_BetweenIDs(2, 4) . '<br>';
                echo __LINE__ . ': BetweenID 0,4<br>=' . $cf->getContent_BetweenIDs(0, 4) . '<br>';
                echo __LINE__ . ': BetweenNext2Current<br>=' . $cf->getContent_BetweenNext2Current() . '<br>';
                echo __LINE__ . ': BetweenPrev2Current<br>=' . $cf->getContent_BetweenPrev2Current() . '<br>';
                echo '<br>';
            }
            if ($rebuild != $source) {
                die(__FUNCTION__ . __LINE__ . ': $rebuild != $source');
            }
            ######## borders beetween #########
        }


        return $rebuild;
    }
    public static function bordersBeetweenExample($cf, $silentMode, $rebuild, $source) {
    }
    public static function simple123example($silentMode = false) {
        if (true) {
            $content = '123';
            $cf = new SL5_preg_contentFinder($content);

            $c = @$cf->getContent($b1 = 'q', $b2 = 'x');
            if (!$silentMode) {
                info(__LINE__ . ': $c="' . $c . '"');
            }
            if ($c != '') {
                die("$c!=''");
            }

            $c = @$cf->getContent($b1 = '1', $b2 = 'x');
            if (!$silentMode) {
                info(__LINE__ . ': $c="' . $c . '"');
            }
            if ($c != '23') {
                die(" '$c' != '23'");
            }

            $c = @$cf->getContent($b1 = 'q', $b2 = '3');
            if (!$silentMode) {
                info(__LINE__ . ': $c="' . $c . '"');
            }
            if ($c != '12') {
                die("'$c' != '12' ");
            }

            return array($cf, $b1, $b2, $c);
        }
    }
    /**
     * @param $silentMode
     * @return array
     */
    public static function selfTest_Tags_Parsing_Example($silentMode = false) {


        $content1 = $source = '<body>
ha <!--[01.o0]-->1<!--[/01.o0]-->
hi [02.o0]2<!--[/02.o0]-->
ho  <!--[03.o0]-->3<!--[/03.o0]-->
</body>';
        if (!$silentMode) {
            info(__LINE__ . ': ' . $source);
        }
        $pos_of_next_search = 0;
        $begin = '(<!--)?\[([^\]>]*\.o0)\](-->)?';
        $end = '<!--\[\/($2)\]-->';
        $cf = new SL5_preg_contentFinder($source);
        $cf->setBeginEnd_RegEx($begin, $end);
        $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
        $loopCount = 0;
        while ($loopCount++ < 5) {
            $cf->setPosOfNextSearch($pos_of_next_search);
            $findPos = $cf->getBorders();
            $sourceCF = @$cf->getContent();
            $expectedContent = $loopCount;
            if ($loopCount > 3) {
                $expectedContent = '';
            }
            if ($sourceCF != $expectedContent) {
                info('$sourceCF=' . $sourceCF);
                $str = __LINE__ . ': ' . "loop=$loopCount: '$sourceCF' != '$expectedContent' <br>(source != expected) ";
                bad($str);
                die(__LINE__ . $str);
            }
            if (is_null($findPos['begin_begin'])) {
                break;
            }
            if (!$silentMode) {
                great(__LINE__ . ': ' . $content1 . ' ==> "' . $sourceCF . '"');
            }
            $pos_of_next_search = $findPos['end_end'];
        }

        return array(
          $source,
          $content1,
          $loopCount,
          $pos_of_next_search,
          $begin,
          $end,
          $cf,
          $findPos,
          $sourceCF,
          $expectedContent
        );
    }
    public static function before_behind_example($silentMode) {
        if (true) {
            switch (3) {
                case 1:
                    $b1 = 'b';
                    $E = ')';
                    $e = '}';
                    break;
                case 2:
                    $b1 = 'b';
                    $E = '>';
                    $e = 'e';
                    break;
                case 3:
                    $b1 = '[';
                    $E = '>';
                    $e = ']';
                    break;
            }
            $b2 = "$E$e";

            $source_before = '1';
            $content = "2$b1$e" . '2';
            $behind_source = '3';
            $_source = $source_before . "$b1" . $content . "$E$e" . $behind_source;
            if (!$silentMode) {
                great(__LINE__ . ': $source = ' . str_replace($b1, "<i>$b1</i>", $_source), false);
            }
            if (!$silentMode) {
                great(__LINE__ . ': $b1 = ' . $b1 . ' $end = ' . $b2);
            }

            $cf = new SL5_preg_contentFinder($_source);
//            $cf->setBeginEnd_RegEx($begin, $end);
            $content1 = @$cf->getContent($b1, $b2);
            if ($content != $content1) {
                die("$content!=$content1");
            }
            $findPos = $cf->getBorders();
            if ($findPos['end_begin'] == $findPos['end_end']) {
                die('<br>die in line: ' . __LINE__ . ': ' . var_export(
                    $findPos,
                    true
                  ));
            }


            $before_content = substr($_source, 0, $findPos['begin_begin']);
            $behind_content = substr($_source, $findPos['end_end']);
            if ($source_before != $before_content) {

                die("before: $source_before != $before_content");
                if (!$silentMode) {
                    info(__LINE__ . ': $content_before = "' . $before_content . '"');
                }
                if (!$silentMode) {
                    info(__LINE__ . ': contentDemo _ _ _ _ = "' . $content1 . '"');
                }
                bad_little(__LINE__ . ': was muss contentDemo sein???? inklusive rest??? ');
            }
            if (!$silentMode) {
                info(__LINE__ . ': $content_behind = "' . $behind_content . '"');
            }
            if ($findPos['end_begin'] >= $findPos['end_end']
            ) {
                bad_little("<br>end_begin >= end_end<br> {$findPos['end_begin']} >= {$findPos['end_end']}");
                info(__LINE__ . ': ' . $_source . ' ==> ' . $content1);
                if ($behind_source != $behind_content) {
                    bad_little(
                      __LINE__ . ':behind: <br>' . $behind_source . ' ==> ' . $behind_content . '   $source_behind != $content_behind '
                    );
                    die('<br>die in line: ' . __LINE__);
                }
                else {
                    info(__LINE__ . ':behind: <br>' . $behind_source . ' ==> ' . $behind_content);
                }
                info(__LINE__ . ':before: <br>' . $source_before . ' ==> ' . $before_content, 'yellow', false);
                die('<br>die in line: ' . __LINE__);
            }

        }
    }
    /**
     * @param $content
     * @param null $before
     * @param null $behind
     * @return array
     */
    public static function recursion_example2($content, $before = null, $behind = null) {
        $silentMode = true;
        if (is_null($before)) {
            if (!$silentMode) {
                echo('<u>' . __FUNCTION__ . '</u>:');
            }
        }

        echo '<pre>';
        echo '<font style="font-family: monospace">';
        $cf = new SL5_preg_contentFinder($content);
        $delimiters = array('(', ')');
        $delimiters[1];
        if ($cut = @$cf->getContent($delimiters[0], $delimiters[1])) {
            $p = $cf->getBorders( $delimiters[0], $delimiters[1]);
            if (is_null($p['begin_begin'])) {
                die(__FUNCTION__ . __LINE__);
            }

            $before .= substr($content, 0, $p['begin_begin']) . $delimiters[0];
            $behind = $delimiters[1] . substr($content, $p['end_end']) . $behind;

//            great(__LINE__ . ":\n" . '$before.$cut.$behind=' . "\n$contentDemo ==> " . "$before#$cut#$behind");
//            @$cf->getContent($b,$e,0,$cut);


            return self::recursion_example2($cut, $before, $behind);
        }

//        info(__LINE__ . ":\n \$cut= \n" . var_export($cut, true));
        return array(($cut) ? $cut : $content, $before, $behind);
    }
    public static function recursionExample6_search_also_in_rest_of_the_string(
      $content,
      $delimiters = array('(', ')'),
      $newDelimiter = null,
      $before = null,
      $behind = null
    ) {
        $isFirsRecursion = is_null($before);
        $cf = new SL5_preg_contentFinder($content);
        if (is_null($newDelimiter)) {
            $newDelimiter =& $delimiters;
        }
        if ($cut = @$cf->getContent($delimiters[0], $delimiters[1])) {
            $function = 'self::' . __FUNCTION__;

            $p = $cf->getBorders( $delimiters[0], $delimiters[1]);
            if (is_null($p['begin_begin'])) {
                die(__FUNCTION__ . __LINE__);
            }

            $before .= substr($content, 0, $p['begin_begin']) . $newDelimiter[0];
            $behindTemp = substr($content, $p['end_end']);

            if (!$isFirsRecursion) {
                $behind = $newDelimiter[1] . $behindTemp;
            }
            if ($isFirsRecursion) {
                $return = call_user_func(
                  $function,
                  $behindTemp
                  ,
                  $delimiters,
                  $newDelimiter
                );
                list($c, $bf, $bh) = $return;
                $behind = (is_null($c)) ? $newDelimiter[1]
                  . $behindTemp : $newDelimiter[1] . $bf . $c . $bh;
            }

            # change cut a little
            $dataExample = 1;
            if (preg_match("/\d/", $cut, $e)) {
                $dataExample = $e[0] + 1;
            }
            $cut = preg_replace("/\w/", ($dataExample > 9) ? $dataExample - 10 : $dataExample, $cut);

            great(
              __LINE__ . ": \n" . "\n$content (contentDemo) ==> \n" . "$before<u><b>$cut</b></u>$behind" . ' (before.cut.behind)',
              false
            );
            $return = call_user_func(
              $function,
              $cut,
              $delimiters,
              $newDelimiter,
              $before,
              $behind
            );

            return $return;
        }

//        info(__LINE__ . ":\n \$cut=" . var_export($cut, true) . ' $contentDemo=' . var_export($contentDemo, true) . "\n \$before=$before, \$cut=" . (($cut) ? $cut : $contentDemo) . " ,  \$behind=$behind");
        $return = array(($cut) ? $cut : $content, $before, $behind);

        return $return;
    }
    public static function recursionExample5_search_also_in_rest_of_the_string(
      $content,
      $newDelimiter = null,
      $before = null,
      $behind = null
    ) {
        $silentMode = true;
        $isFirsRecursion = is_null($before);
        $cf = new SL5_preg_contentFinder($content);
        $delimiters = array('(', ')');
        if (is_null($newDelimiter)) {
            $newDelimiter =& $delimiters;
        }
        if ($cut = @$cf->getContent($delimiters[0], $delimiters[1])) {
            $p = $cf->getBorders( $delimiters[0], $delimiters[1]);
            if (is_null($p['begin_begin'])) {
                die(__FUNCTION__ . __LINE__);
            }
            $before .= substr($content, 0, $p['begin_begin']) . $newDelimiter[0];
            $behindTemp = substr($content, $p['end_end']);

            if (!$isFirsRecursion) {
                $behind = $newDelimiter[1] . $behindTemp;
            }
            if ($isFirsRecursion) {
                list($c, $bf, $bh) =
                  self::recursionExample5_search_also_in_rest_of_the_string(
                    $behindTemp
                    ,
                    $newDelimiter
                  );
                $behind = (is_null($c)) ? $newDelimiter[1]
                  . $behindTemp : $newDelimiter[1] . $bf . $c . $bh;
            }

            # change cut a little
            $dataExample = 1;
            if (preg_match("/\d/", $cut, $e)) {
                $dataExample = $e[0] + 1;
            }
            $cut = preg_replace("/\w/", ($dataExample > 9) ? $dataExample - 10 : $dataExample, $cut);


            if (!$silentMode) {
                great(
                  __LINE__ . ": \n" . "\n$content (contentDemo) ==> \n" . "$before<u><b>$cut</b></u>$behind" . ' (before.cut.behind)',
                  false
                );
            }
            $return = self::recursionExample5_search_also_in_rest_of_the_string(
              $cut,
              $newDelimiter,
              $before,
              $behind
            );

            return $return;
        }

//        info(__LINE__ . ":\n \$cut=" . var_export($cut, true) . ' $contentDemo=' . var_export($contentDemo, true) . "\n \$before=$before, \$cut=" . (($cut) ? $cut : $contentDemo) . " ,  \$behind=$behind");
        $return = array(($cut) ? $cut : $content, $before, $behind);

        return $return;
    }
    /**
     * @param integer $nr
     * @return string
     */
    public static function getExampleContent($nr = null) {
        $bugIt = false;


        $content = str_repeat(implode('', range(0, 9)), 2) . '0';
        $numbers = str_repeat(implode('', range(0, 9)), 2) . '0';
        $contentArray = str_split($content);
        $content = '';
        $delimiters = array('(', ')', ')');
        foreach ($contentArray as $pos => $v) {
            if ($nr == 1) {
                $temp = (($pos + 1) % 4 == 2) ? $v : $delimiters[$pos % 3];
                $temp = (($pos + 1) % 3 == 2) ? $v : $delimiters[$pos % 3];
                if (in_array($v, array(5, 2))) {
                    $temp = ($pos > 9) ? ($pos - 10) : $pos;
                }
                $content .= $temp;
            }
            else {
                $content .= (($pos + 1) % 3 == 2) ? $v : $delimiters[$pos % 3];
            }
        }
        if ($bugIt) {
            echo __LINE__ . ':$contentDemo=<br>' . $content . '<br>';
        }
        if ($bugIt) {
            echo $numbers . '<br><br>';
        }

        return $content . (($nr == 1) ? '' : "\n" . $numbers);
    }
    public static function recursionExample4_search_also_in_rest_of_the_string(
      $content,
      $before = null,
      $behind = null
    ) {
        $silentMode = true;
        $isFirsRecursion = is_null($before);

        if ($isFirsRecursion) {
            if (!$silentMode) {
                echo('<u>' . __FUNCTION__ . '</u>:');
            }
        }

        echo '<pre>';
        echo '<font style="font-family: monospace">';
        $cf = new SL5_preg_contentFinder($content);
        if ($cut = @$cf->getContent($b='(', $e=')')) {
            $before .= $cf->getContent_Before() . '(';
            $behindTemp = $cf->getContent_Behind() . $behind;

            if (!$isFirsRecursion) {
                $behind = ')' . $behindTemp;
            }
            if ($isFirsRecursion) {
                list($c, $bf, $bh) =
                  self::recursionExample4_search_also_in_rest_of_the_string($behindTemp);
                $behind = (is_null($c)) ? ')' . $behindTemp : ')' . $bf . $c . $bh;
            }

            # change cut a little
            # why? only for demonstration different result as Original source
            if(true) {
                $dataExample = 1;
                if (preg_match("/\d/", $cut, $e)) {
                    $dataExample = $e[0] + 1;
                }
                $cut = preg_replace("/\w/", ($dataExample > 9) ? $dataExample - 10 : $dataExample, $cut);
            }
            $return = self::recursionExample4_search_also_in_rest_of_the_string(
              $cut,
              $before,
              $behind
            );
            return $return;
        }

//        info(__LINE__ . ":\n \$cut=" . var_export($cut, true) . ' $contentDemo=' . var_export($contentDemo, true) . "\n \$before=$before, \$cut=" . (($cut) ? $cut : $contentDemo) . " ,  \$behind=$behind");
        $return = array(($cut) ? $cut : $content, $before, $behind);

        return $return;
    }
    /**
     * @param $content
     * @param null $before
     * @param null $behind
     * @return array
     */
    public static function recursionExample3_search_NOT_in_rest_of_the_string(
      $content,
      $before = null,
      $behind = null) {
        $silentMode = true;
        if (is_null($before)) {
            if (!$silentMode) {
                echo('<u>' . __FUNCTION__ . '</u>:');
            }
        }

        echo '<pre>';
        echo '<font style="font-family: monospace">';
        $cf = new SL5_preg_contentFinder($content);
        $delimiters = array('(', ')');
        $delimiters[1];
        if ($cut = @$cf->getContent($delimiters[0], $delimiters[1])) {
            $p = $cf->getBorders( $delimiters[0], $delimiters[1]);
            if (is_null($p['begin_begin'])) {
                die(__FUNCTION__ . __LINE__);
            }


            $before .= substr($content, 0, $p['begin_begin']) . $delimiters[0];
            $behind = $delimiters[1] . substr($content, $p['end_end']) . $behind;


//            great(__LINE__ . ": \n" . '$before.$cut.$behind=' . "\n$contentDemo ==> " . "$before#$cut#$behind");
//            @$cf->getContent($b,$e,0,$cut);

            if (true) {
                # change cut a little
                $dataExample = 1;
                if (preg_match("/\d/", $cut, $e)) {
                    $dataExample = $e[0] + 1;
                }
                $cut = preg_replace("/\w/", ($dataExample > 9) ? $dataExample - 10 : $dataExample, $cut);
            }

            return self::recursionExample3_search_NOT_in_rest_of_the_string($cut, $before, $behind);
        }

//        info(__LINE__ . ":\n \$cut= \n" . var_export($cut, true));

        return array(($cut) ? $cut : $content, $before, $behind);
    }
    /**
     * @param $content
     * @return bool|string
     */
    public static function recursion_example($content) {

        $silentMode = true;
        if (!$silentMode) {
            echo '<pre>';
        }
        if (!$silentMode) {
            echo '<font style="font-family: monospace">';
        }
        $cf = new SL5_preg_contentFinder($content);
        if ($cut = @$cf->getContent($b = '(', $e = ')')) {
//            great(__LINE__ . ":\n \$cut= \n$contentDemo ==> " . $cut);
//            @$cf->getContent($b,$e,0,$cut);
            return self::recursion_example($cut);
        }

//        info(__LINE__ . ":\n \$cut= \n" . var_export($cut, true));
        return $cut;
    }
}