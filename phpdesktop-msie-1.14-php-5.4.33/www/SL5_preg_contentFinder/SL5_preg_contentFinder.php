<?php
/*
  This SL5_ContentFinder class is part of the doSqlWeb project,
  a PHP Template Engine.
  Copyright (C) 2013 Sebastian Lauffer, http://SL5.net
 
  SL5_ContentFinder stands under the terms of the GNU General Public
 License as published by the Free Software Foundation, either version 3
 of the License, or (at your option) any later version.
 
  SL5_ContentFinder is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
 
  For GNU General Public License see <http://www.gnu.org/licenses/>.
 */
$bugIt = false;
// Add appgati.
if($bugIt) {
    require_once 'appgati.class.php';
// Initialize
    $app = new AppGati();
// A step should be a continous string.
    $app->Step('1');
}

//ContentFinder::selfTest_collection();

// Add another step.
if($bugIt) {
    $app->Step('2');
}

// Generate report between steps 1 and 2.
if($bugIt) {
    $report1 = $app->Report('1', '2');
}

if($bugIt) {
// Print reports.
    echo '<hr>';
    print_r($report1['Clock time in seconds']);
    echo '<hr>';
    print_r($report1);
}

if(basename($_SERVER["PHP_SELF"]) == basename(__FILE__)) {
    include_once("test/SL5_preg_contentFinderTest1.php");
    SL5_preg_contentFinder::selfTest_collection();
}
class SL5_preg_contentFinder {
    private static $selfTest_defaults = array(); # please fill that first time
    private static $selfTest_called_from_init_defaults = false;
    private static $selfTest_collection_finished = false;
    public $isUniqueSignUsed = false;
    private $content = null;
    private $regEx_begin = null;
    private $regEx_end = null;

    private $stopIf_EndBorder_NotExistInContent = false;
    private $stopIf_BothBorders_NotExistInContent = true;

    private $doOverwriteSetup_OF_pos_of_next_search = true;

    private $pos_of_next_search = null;
    private $searchMode = "lazyWhiteSpace";

    private $searchModes = array('lazyWhiteSpace', 'dontTouchThis', 'use_BackReference_IfExists_()$1${1}');
    private $CACHE_beginEndPos_2_findPosKey = array();
    private $history = array();

    private static $CACHE_id_arrayKeys = array('begin', 'end', 'pos_of_next_search', 'posArray');

    private static $posArray_arrayKeys = array('begin_begin', 'begin_end', 'end_begin', 'end_end', 'matches');

    private $foundPos_list_current_ID = null;

    public
    static $lastObject = null;

    private $foundPos_list;
    private $uniqueSignExtreme = null;

    /**
     * @param $content string e.g. source of your file
     * @param null $regEx_begin_mixed (could be a array
     * @param null $regEx_end
     */
    function __construct($content, $regEx_begin_mixed = null, $regEx_end = null) {
        $this->content = $content;
        $this->setBeginEnd_RegEx($regEx_begin_mixed, $regEx_end);
        self::$lastObject = $this;
    }

    /**
     * @param $pos_of_next_search integer
     * @return bool false if !is_numeric($pos_of_next_search)
     */
    public function setPosOfNextSearch($pos_of_next_search) {

        if(!is_numeric($pos_of_next_search)) {
            bad(__FUNCTION__ . __LINE__ . ' : !is_numeric($pos_of_next_search)' . $pos_of_next_search);

            return false;
        }
        $this->pos_of_next_search = $pos_of_next_search;

        return true;
    }

    private function getPosOfNextSearch() {
        $pos_of_next_search = $this->pos_of_next_search;
        if(!is_numeric($pos_of_next_search)) {
            $pos_of_next_search = 0;
        }

        return $pos_of_next_search;
    }

    /**
     * @param $RegEx_begin perl regular expression
     * @param $RegEx_end perl regular expression
     * @return bool always returns true - no meaning
     */
    public function setBeginEnd_RegEx($RegEx_begin = null, $RegEx_end = null) {
        if(is_array($RegEx_begin)) {
            $RegEx_end = $RegEx_begin[1]; // dont change this two lines!
            $RegEx_begin = $RegEx_begin[0]; // dont change this two lines!
        }
        if(!is_null($RegEx_begin)) {
            $this->setRegEx_begin($RegEx_begin);
        }
        if(!is_null($RegEx_end)) {
            $this->setRegEx_end($RegEx_end);
        }

        return true;
    }

    /**
     * @param $RegEx_begin perl regular expression
     * @return bool always returns true - no meaning
     */
    private function setRegEx_begin($RegEx_begin) {
        if(is_null($RegEx_begin)) {
            die(__FUNCTION__ . __LINE__ . ": is_null($RegEx_begin)");
        }
        $this->setRegEx($this->regEx_begin, $RegEx_begin);

        return true;
    }

    private static function implement_BackReference_IfExists(&$matchesReturn, &$RegEx_begin, &$RegEx_end) {
        foreach($matchesReturn['begin_begin'] as $nr => $valuePos) {
            $vQuote = preg_quote($valuePos[0], '/');
            $RegEx_end_new = str_replace(
              array('$' . ($nr + 1), '${' . ($nr + 1) . '}'),
              $vQuote,
              $RegEx_end
            );
            if($RegEx_end_new != $RegEx_end) {
                preg_match_all("/\([^)]*\)/", $RegEx_begin, $bb, PREG_OFFSET_CAPTURE);
                list($bb['found'], $bb['pos']) = $bb[0][$nr];
                $bb['len'] = strlen($bb['found']);
                $RegEx_begin =
                  substr($RegEx_begin, 0, $bb['pos'])
                  . '(' . $vQuote . ')' . substr($RegEx_begin, $bb['pos'] + $bb['len']);
                $RegEx_end = $RegEx_end_new;
            }
        }
        $pattern = '/(' . $RegEx_begin . '|' . $RegEx_end . ')(.*)/sm';
        $breakPoint = 'breakPoint';

        return true;
    }

    private function getRegEx_begin() {
        return $this->regEx_begin;
    }

    private function getRegEx_end() {
        return $this->regEx_end;
    }

    /**
     * @param $RegEx_end perl regular expression
     * @return bool always returns true - no meaning
     */
    private function setRegEx_end($RegEx_end) {
        $this->setRegEx($this->regEx_end, $RegEx_end);

        return true;
    }

    /**
     * @param string $searchMode 'lazyWhiteSpace', 'dontTouchThis', 'use_BackReference_IfExists_()$1${1}'
     * @return bool false if param not match
     */
    public function setSearchMode($searchMode) {
        $searchModes = $this->searchModes;
        if(!in_array($searchMode, $searchModes)) {
            bad(
              __FUNCTION__ . __LINE__ . ' this $searchMode is not possible. pleas use on of them: ' . implode(
                ', ',
                $searchModes
              )
            );

            return false;
        }
        $this->searchMode = $searchMode;

        return true;
    }

    /**
     * @return string 'lazyWhiteSpace', 'dontTouchThis', 'use_BackReference_IfExists_()$1${1}'
     */
    public function getSearchMode() {
        return $this->searchMode;
    }

    private static function content_before_behind_example($silentMode) {
        if(1) {
            $_source = str_repeat(implode('', range(0, 3)), 2) . '0';
            $numbers = str_repeat(implode('', range(0, 3)), 2) . '0';
            $_sourceArray = str_split($_source);
            $_source = '';
            $delimiters = array('(', 'anywayNumber', ')');
            foreach($_sourceArray as $b_pos => $valArray) {
                $_source .= (($b_pos + 1) % 3 == 2) ? $valArray : $delimiters[$b_pos % 3];
            }
            # find every second third
            $rebuild = '';
            $c = new SL5_preg_contentFinder($_source);
            if(!$silentMode) {
                info(__LINE__ . ": \$contentDemo = <br>" . htmlspecialchars($_source));
            }

            for($b_pos = 0; $b_pos < count($_sourceArray); $b_pos += 3) {

                $p = $c->getBorders($b1 = '(', $b2 = ')', $b_pos);
                echo "getBorders= " . var_export($p, true);


                if(!$silentMode) {
                    great('$cf->prev()=' . $c->getContent_Prev());
                }
                if(!$silentMode) {
                    info('$cf->next()=' . $c->getContent_Next());
                }
                info('pos_of_next_search=' . $c->pos_of_next_search);
                $rebuild .= '(' . $c->getContent() . ')';
            }
            if(!$silentMode) {
                echo __LINE__ . ':$rebuild= <br>' . $rebuild . '<br>';
            }
            if($_source != $rebuild) {
                die(__LINE__ . ": ERROR:<br>$_source != <br>$rebuild (rebuild)");
            }
            echo '<br>';

            SL5_preg_contentFinderTest1::before_behind_example($silentMode);
            SL5_preg_contentFinderTest1::test_getContent_setID();
        }
    }


    /**
     * @return self|SL5_preg_contentFinder
     */
    public function getLastObject() {
        $l = self::$lastObject;

        return self::$lastObject;
    }


    public function getContent_user_func_recursive(
      $openFunc = null,
      $contentFunc = null,
      $closeFunc = null,

      $before = null,
      $behind = null) {

//if(false)
//        $functions = ['open' => $openFunc,
//                      'content' => $contentFunc,
//                      'close' => $closeFunc];
//        else // old style
        $functions = array('open' => $openFunc,
                           'content' => $contentFunc,
                           'close' => $closeFunc);

//        $content = ['before' => $before, 'middle' => $this->content, 'behind' => $behind];
        $content = array('before' => $before, 'middle' => $this->content, 'behind' => $behind); // old style

        $callsCount = 0;
        $return = $this->getContent_user_func_recursivePRIV(
          $content,
          $functions, $callsCount);

        return $return;
    }
    /**
     * todo: proof performance. call by reference inside userFunc is not supported actually
     * @param null $content
     * @param null $func functions callback
     * @param int $deepCount
     * @param $callsCount
     * @return array
     */
    private function getContent_user_func_recursivePRIV(
      $content,
      $func,
      &$callsCount,
      $deepCount = -1
    ) {
        $bugIt = true;
//        $callsCount++;
        $deepCount++; # starts with $deepCount = 0
        if($content['middle'] == false || is_null($content['middle'])) {
            # create $r_content
            $r_content = (isset($content['before'])) ? $content['before'] : '' . $content['middle'] . (isset($content['behind'])) ? $content['behind'] : '';

            return $r_content;
        }

        # search in $content['middle'], create $cut Array
        $C = new SL5_preg_contentFinder($content['middle'], $this->regEx_begin, $this->regEx_end);
        $C->setSearchMode($this->getSearchMode());
        $cut = array(
          'before' => $C->getContent_Before(),
          'middle' => $C->getContent(),
          'behind' => $C->getContent_Behind());


        if($bugIt) $_cutInfoStr = $cut['before'] . $cut['middle'] . $cut['behind'];


        $terminate_seaching_inside_cut_because_nothing_found = $cut['middle'] === false;


        # search in $cut['behind'], create $r_cut_behind
//        $r2_cut_behind =
        if($cut['middle'] !== false) {
            $cut['behind'] = $this->getContent_user_func_recursivePRIV(
              array('middle' => $cut['behind']),
              $func, $callsCount, $deepCount - 1);
        }
//        $r_cut_behind = ''; __LINE__;

        if(is_null($C->foundPos_list[0]['end_begin'])) {
            # there is no beginning like {NIX
            if(!isset($content['before'])) $content['before'] = '';
//            if(!$terminate_seaching_inside_cut_because_nothing_found) {
//                return $content['before'] . $content['middle'];
//            }
//            else {

            $source1 = '{NOX'; # 79
            $expected = 'NoX'; # 79

            $source1 = '{NIX{}'; # 19
            $expected = 'NIX{'; # 19

            if(true || $deepCount == 1) {
//                $deepCount==0 && $callsCount == 0 &&

                $cut = call_user_func($func['open'], $cut, 0, $callsCount, $C->foundPos_list[0], $C->content);

                if($content['before'] == "" && $cut['before'] != "" && $cut['middle'] !== false && $cut['behind'] == "") {
//                    $cut = call_user_func($func['open'], $cut, $deepCount + 1, $callsCount, $C->foundPos_list[0], $C->content);
                    $returnA = $cut['before'] . $cut['middle'] . $cut['behind'];
                    $return = &$returnA;
                }
                else {
                    $returnB = $content['before'] . $content['middle'];
                    $return = &$returnB;
                }

                return $return;
            }
            else {
                $return = $content['before'] . $cut['middle'];

                return $return;
            }
//            }
        }


        # search in $cut['middle'], create $r_cut_middle
        if(!$terminate_seaching_inside_cut_because_nothing_found) {

            $cut_middle_backup = $cut['middle'];
            $cut['middle'] = $this->getContent_user_func_recursivePRIV(
              array('middle' => $cut['middle']),
              $func, $deepCount, $callsCount);
            if($bugIt) $_cutInfoStr = $cut['before'] . $cut['middle'] . $cut['behind'];
            $cut = call_user_func($func['open'], $cut, $deepCount + 1, $callsCount, $C->foundPos_list[0], $C->content);
            if($bugIt) $_cutInfoStr = $cut['before'] . $cut['middle'] . $cut['behind'];

        }
        else {
            $cut['middle'] = $content['middle'];
        }

        if($bugIt) $_cutInfoStr = $cut['before'] . $cut['middle'] . $cut['behind'];

        $r1_cut = $cut['before'] . $cut['middle'];// . $cut['behind'] ;

        if($bugIt) $_cutInfoStr = $cut['before'] . $cut['middle'] . $cut['behind'];

        # search in $content['behind'], create $r_behind
        $r3_behind = (isset($content['behind']) && $content['behind'] !== false) ? $this->getContent_user_func_recursivePRIV(
          array('middle' => @$content['behind']),
          $func, $deepCount, $callsCount) : '';

        $content['before'] = (isset($content['before'])) ? $content['before'] : '';
//        $return = $content['before'] . $r1_cut . $r2_cut_behind;

        $return = $content['before'] . $r1_cut; //. $r3_behind ; // . '' . $r2_cut_behind;

//        $return = (isset($content['before'])) ? $content['before'] : '' . '' . $r_cut . '' . $r_cut_behind . $r_behind; # last working version


        $break = 'break';
//        $return = (isset($content['before'])) ? $content['before'] : $r_cut . $r_behind ;
        // $r_cut_behind  . $r_behind

        // $r_cut_behind . $r_behind

//        $return = (isset($content['before'])) ? $content['before'] : '' . '' . $r_cut . '' . $r_cut_behind . $r_behind;

        $break = 'break';

        return $return;

    }


    /**
     * @return string Content
     * todo: discuss implementation performance. relevant?
     */
    public function getContent_Before() {
        if(is_null($this->content)) $this->getContent();
        if($this->content === false) return false;
        $borders = $this->getBorders();
        $begin_begin = &$borders['begin_begin'];
        if($begin_begin == 0) return '';

        return substr($this->content, 0, $begin_begin);
//        return substr($this->getContent(), 0, $this->getBorders()['begin_begin']);
    }
    /**
     * @return string Content
     * todo: discuss implementation performance. relevant?
     */
    public function getContent_Behind() {
        if(is_null($this->content)) $this->getContent();
        if($this->content === false) return false;
        $borders = $this->getBorders();
        $end_end = &$borders['end_end'];
//        $borders = $end_end;
        if($end_end == strlen($this->content)) return '';
        $sub_str = substr($this->content, $end_end);

        return $sub_str;
//        return substr($this->getContent(), $this->getBorders()['end_end']);
    }

    /**
     * @param null $RegEx_begin perl regular expression.
     * @param null $RegEx_end perl regular expression.
     * @param null $pos_of_next_search
     * @param null $txt
     * @param null $searchMode
     * @param bool $bugIt
     * @return mixed
     */
    public
    function getBorders(
      $RegEx_begin = null,
      $RegEx_end = null,
      $pos_of_next_search = null,
      &$txt = null,
      $searchMode = null,
      $bugIt = false
    ) {
        if(is_null($txt)) {
            $txt = $this->content;
        }
        if(is_null($searchMode)) {
            $searchMode = $this->getSearchMode();
        }
        $this->update_RegEx_BeginEndPos($RegEx_begin, $RegEx_end, $pos_of_next_search);

        $pos_of_next_search_backup = $pos_of_next_search;

        # please use selfTest of this class for understanding this function completely. it returns to positions.
        # it gives back the beginning of the borders (left). left beginning of each
        # it searchs from the beginning of the $txt
        # benchark tipps: http://floern.com/webscripting/geschwindigkeit-von-php-scripts-optimieren
        if($searchMode == 'lazyWhiteSpace') {
            $RegEx_begin_backup = $RegEx_begin;
            $RegEx_end_backup = $RegEx_end;
            $RegEx_begin = SL5_preg_contentFinder::preg_quote_by_SL5($RegEx_begin);
            $RegEx_end = SL5_preg_contentFinder::preg_quote_by_SL5($RegEx_end);
        }
        elseif(strrpos($searchMode, 'use_BackReference') !== false || strrpos(
            $searchMode,
            'dontTouchThis'
          ) !== false
        ) {
            # begin and end should are regular expressions! i could not proof this ... hmm
            $RegEx_begin_backup = $RegEx_begin;
            $RegEx_end_backup = $RegEx_end;
        }
        else {
            die(__LINE__ . ': actually "' . implode(', ',
                $this->searchModes) . '" are the only implemented search modes. not "' . $searchMode . '" ' . "\$begin=$RegEx_begin, \$end=$RegEx_end");
        }

        $RegEx_begin_CACHE = $RegEx_begin;
        $RegEx_end_CACHE = $RegEx_end;
        $findPosID = &$this->CACHE_beginEndPos_2_findPosKey[$RegEx_begin_CACHE][$RegEx_end_CACHE][$pos_of_next_search];
        if(isset($findPosID)) {
            $return = &$this->foundPos_list[$findPosID];

            return $return;
        }

        $emergency_Stop = 0;

        $findPos['begin_begin'] = null; // the begin is easy case. find the right end little more difficult.
        $findPos['end_begin'] = null;
        $matchesReturn = null; # optionally you could store parts inside of borders

        $strLen_txt = strlen($txt);
//        $strLen_begin = strlen($begin_backup); # may little long. long enough ...
//        $strLen_end = strlen($end_backup); # may little long. long enough ...
//        $strLen_begin_backup = strlen($begin_backup);

        $pattern = '/(' . $RegEx_begin . '|' . $RegEx_end . ')(.*)/sm';
        if($searchMode == 'dontTouchThis') {
            if($bugIt) {
                echo(__LINE__ . ": $RegEx_begin | $RegEx_end     \$pattern=" . $pattern);
            }
        }
        $count_begin = 0;
        $count_end = 0;

        if($this->isUniqueSignUsed && (!$this->uniqueSignExtreme || strpos($txt, $this->uniqueSignExtreme) !== false)) {
            # better unique sign is needed
            $this->setUniqueSignExtreme($txt);
        }

        while(($count_begin == 0 || $count_begin > $count_end)
          && $emergency_Stop < 1000
        ) {
            $emergency_Stop++;
            if($count_begin == 0) {
                # first search the startBorder

                /*
                 *  preg_match returns the number of times
        * <i>pattern</i> matches. That will be either 0 times
        * (no match) or 1 time because <b>preg_match</b> will stop
        * searching after the first match.
                 */
                $preg_match_result = preg_match(
                  '/'
                  . $RegEx_begin . '/sm',
                  $txt,
                  $matches_begin,
                  PREG_OFFSET_CAPTURE,
                  $pos_of_next_search
                );

                if(!$preg_match_result) {
                    # no first element found/exist
                    if(preg_match('/' . $RegEx_end . '/', $txt, $matches, PREG_OFFSET_CAPTURE, $pos_of_next_search)) {
                        $findPos['end_begin'] = $matches[0][1];
                    }
//                    die(__LINE__ . ':$findPos[end] = ' . $findPos['end_begin'] . " \$txt=$txt");
                    break;
                }
                $findPos['begin_begin'] = $matches_begin[0][1];
                $count_begin++;

                $pos_of_last_found = $matches_begin[0][1];
                $pos_of_next_search = $pos_of_last_found
                  + strlen($matches_begin[0][0]) + 0;

                $findPos['begin_end'] = $pos_of_next_search;

                $matchesReturn['begin_begin'] = array_splice($matches_begin, 1);

                if($searchMode == 'use_BackReference_IfExists_()$1${1}') {
                    self::implement_BackReference_IfExists($matchesReturn, $RegEx_begin, $RegEx_end, $pattern);
                }
                if(false) {
                    echo '<pre>';
                    var_export($matches_begin);
                    echo('13-09-20_07-10');
                    echo '</pre>';
                }
            }
            if(1 || $bugIt) {
                $temp = substr($txt, $pos_of_next_search);
            }
            if('1[2[]2>]3' == $txt) {
                info(__LINE__ . ': $count_begin=' . $count_begin);
            }
            if(preg_match($pattern, $txt, $matches, PREG_OFFSET_CAPTURE, $pos_of_next_search)) {
                $pos_of_last_found = $matches[1][1];
                $pos_of_next_search = $pos_of_last_found
                  + strlen($matches[1][0]); # you could also use + 0 it also works correct in the tests.
                if(preg_match('/' . $RegEx_end . '/sm', $matches[1][0])) {
                    $findPos['end_begin'] = $pos_of_last_found;
                    $findPos['end_end'] = $pos_of_next_search;
                    $count_end++;
                }
                else {
                    #$findPos['begin_begin'] = $pos_of_last_found;
                    $count_begin++;
                }
            }
            else {
                $pos_of_next_search = $pos_of_next_search_backup + $strLen_txt;
                break;
            }
        }
        if($matches && count($matches) > 0) {
            $matchesReturn['end_begin'] = array_splice($matches, 2, count($matches) - 3);
        }


//        echo('<br>' . __LINE__ . ':' . $findPos['end_begin'] . ", $count_begin = $count_end ");

        if(!isset($matches[1][0])) {
            $matches[1] = &$matches[0];
        }

        if(is_numeric($findPos['end_begin'])) {
            $findPos['end_end'] = $findPos['end_begin'] + strlen($matches[1][0]);
            if($findPos['end_begin'] >= $findPos['end_end']) {
                $findPos['end_end'] = $findPos['end_begin'] + strlen($RegEx_end_backup);
                if($findPos['end_begin'] >= $findPos['end_end']) {
                    die(__LINE__ . ': ups');
                }
            }
        }
        if(!isset($findPos['end_end']) || is_null($findPos['end_end'])) {
            $findPos['end_end'] = $strLen_txt;
        }
        if($RegEx_begin_backup == '[w') {
            'breakPoint';
        }

        $key_foundPos_list = $this->update_key_foundPos_list($findPos, $matchesReturn);

        $this->setCACHE_beginEndPos(
          $RegEx_begin_CACHE,
          $RegEx_end_CACHE,
          $pos_of_next_search_backup,
          $key_foundPos_list
        );

        if($bugIt || true) {
            $temp = substr($txt, $pos_of_next_search_backup);
            $content = (@$findPos['end_begin'])
              ? substr(
                $txt,
                @$findPos['begin_end'],
                @$findPos['end_begin'] - @$findPos['begin_end']
              )
              :
              substr(
                $txt,
                (isset($findPos['begin_end']) && is_numeric(
                    $findPos['begin_end']
                  )) ? $findPos['begin_end'] : $pos_of_next_search_backup
              );

        }
//    $findPos['matches'] = $matchesReturn;
        $this->foundPos_list[$findPosID]['matches'] = $matchesReturn;
        $return = &$this->foundPos_list[$findPosID];
        $dummy = 1;

        return $return;
    }

    /**
     * @param string key pos_of_next_search, begin, end
     * @return bool|null
     */
    public function CACHE_current($key = null) {
        $t = &$this;
        if($key == 'pos_of_next_search') {
            return $t->pos_of_next_search;
        }
        if($key == 'begin') {
            return $t->regEx_begin;
        }
        if($key == 'end') {
            return $t->regEx_end;
        }

        return false;
    }

    /**
     * @param integer $id
     * @return string false if id not exist
     */
    public function getContent_ByID($id) {
        $t = &$this;
        if(is_nan($id) || $id < 0) {
            echo(__LINE__ . ": \$id=is_nan($id)\n");
            debug_print_backtrace();
            die(__FUNCTION__ . __LINE__ . ": \$id=is_nan($id)");
        }
        if(!isset($t->foundPos_list[$id])) {
            return false;
        }

        $C = $t->foundPos_list[$id];

        $backup = $t->doOverwriteSetup_OF_pos_of_next_search;
        $t->doOverwriteSetup_OF_pos_of_next_search = false;
        $content = $t->getContent(
          $C['begin_begin'],
          $C['end_begin'],
          (is_null(@$C['pos_of_next_search'])) ? 0 : $C['pos_of_next_search']
        );
        $t->doOverwriteSetup_OF_pos_of_next_search = $backup;

        return $content;
    }

    /**
     * @return string string before current ID. false if id not exist
     */
    public function getContent_Prev() {
        $id = $this->foundPos_list_current_ID;
        if(is_nan($id)) {
            $return = false;

            return $return;
        }
        $return = (--$id < 0) ? '' : $this->getContent_ByID($id);

        return $return;
    }

    /**
     * @return string string behind current ID. false if id not exist
     */
    public function getContent_Next() {
        $id = $this->foundPos_list_current_ID;
        if(is_nan($id)) {
            $return = false;

            return $return;
        }
        $return = $this->getContent_ByID($id + 1);

        return $return;
    }

    /**
     * @return current ID .
     */
    public function getID() {
        return $this->foundPos_list_current_ID;
    }

    /**
     * high performed unique sign generation.
     * changing incremental to better unique sign. (not only loop throw complete source ;)
     * @param $txt
     * @return string
     */
    private function setUniqueSignExtreme(&$txt) {
        if($this->isUniqueSignUsed == false) return false;

        if(is_null($this->uniqueSignExtreme)) {
            # probably a good unique . probably ;)
            $uniqueSignExtreme = chr(007); # try this start

            if(strpos($txt, $uniqueSignExtreme) === false && strpos($this->regEx_begin, $uniqueSignExtreme) === false && strpos($this->regEx_end, $uniqueSignExtreme) === false) {
                $this->uniqueSignExtreme = $uniqueSignExtreme;

                return;
                # everything fine :)
                # usually it should not trigger at first loop. but anyway.
            }
        }


        $loopMaxCount = 9000;
        $loopI = 0;
        $uniqueSignExtreme = $this->uniqueSignExtreme;
        $uniqueSignExtremeOLD = $uniqueSignExtreme;
        # reuse $this->uniqueSignExtreme inside new. worked for last text. so include it
        while($loopMaxCount-- > 0) {
            if($loopI > 30) {
                $uniqueSignExtreme .= chr(28 + $loopI) . $uniqueSignExtreme;
            }
            else {
                $uniqueSignExtreme = chr(28 + $loopI);
            }
            if(strpos($txt, $uniqueSignExtreme) === false && strpos($this->regEx_begin, $uniqueSignExtreme) === false && strpos($this->regEx_end, $uniqueSignExtreme) === false) {
                break;
                # everything fine :)
            }

            $loopI++;
        }
        $this->uniqueSignExtreme = $uniqueSignExtremeOLD . $uniqueSignExtreme;

        if($loopMaxCount < 1) {
            die("dont uniqueSignExtreme :( could found");
        }

        return true;
        # better unique sign is needed
        # http://stackoverflow.com/questions/1879860/most-reliable-split-character
        /*
         * Aside from 0x0, which may not be available (because of null-terminated strings, for example), the ASCII control characters between 0x1 and 0x1f
         * are good candidates. The ASCII characters 0x1c-0x1f are even designed for such a thing and have the names File Separator, Group Separator, Record Separator, Unit Separator. However, they are forbidden in transport formats such as XML.
In that case, the characters from the unicode private use code points may be used. ...
shareedit
answered Dec 10 '09 at 9:59
nd.
Decimal: 28 = 0b0011100 = 0x1C = CTRL-\
         */

        /*
         * lectures:
         * most unused char:
         * http://stackoverflow.com/questions/1879860/most-reliable-split-character
         * public const char Separator = ((char)007);
I think this is the beep sound, if i am not mistaken.
         *
         * http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string
         * http://php.net/manual/de/function.openssl-random-pseudo-bytes.php
         */
    }

    /**
     * high performed unique sign generation.
     * changing incremental to better unique sign. (not only loop throw complete source ;)
     */
    public function getUniqueSignExtreme() {
        # unique is checked during generation of borders. see getBorders.
        if(!$this->uniqueSignExtreme) {
            die(" pls call this function not before using getBorders or getContent... or so");
        }

        return $this->uniqueSignExtreme;

        if(!$this->isUniqueSignUsed) $this->isUniqueSignUsed = true; // customer is king.
        if(!$this->content) die("you need set content first before you try get a unique sign.");
        if(!$this->uniqueSignExtreme) {
            $this->setUniqueSignExtreme($this->content);
        }

        return $this->uniqueSignExtreme();
    }

    private
    static function selfTest_init_defaults() {
        $temp = self::$selfTest_defaults;
        if(count(self::$selfTest_defaults) > 0) {
            return true;
        } # we was already here. contentChange to do.

        # pseudo constructor
        # please call this in nearly every methods inside. for init the default values.
        self::$selfTest_called_from_init_defaults = true;
        self::selfTest();
        self::$selfTest_called_from_init_defaults = false;

        return true;
    }

    /**
     * @param $id
     */
    public function setID($id) {
        if(!isset($this->foundPos_list_current_ID)) {
            $this->foundPos_list_current_ID = $id;
        }
    }

    /**
     * @return string
     */
    public function getContent_BetweenNext2Current() {
        $current_ID = $this->foundPos_list_current_ID;
        $next_ID = $current_ID + 1;

        return $this->getContent_BetweenIDs($current_ID, $next_ID);
    }

    /**
     * @return string or false if is is_nan
     */
    public function getContent_BetweenPrev2Current() {
        $current_ID = $this->foundPos_list_current_ID;
        if(is_nan($current_ID)) {
            $return = false;

            return $return;
        }
        $prev_ID = $this->foundPos_list_current_ID - 1;

        $doInclusive = false;


        if($prev_ID < 0) {
            $p = $this->foundPos_list[$current_ID];
            if(!$doInclusive) {
                return substr($this->content, 0, $p['begin_begin']);
            }

            return substr($this->content, 0, $p['end_end']);
        }

        $foundPos_list = &$this->foundPos_list;
        if(!isset($foundPos_list[$prev_ID])) {
            $prev_end_end = $foundPos_list[$prev_ID]['end_end'];

            $current_begin = $foundPos_list[$current_ID]['begin_begin'];;
//        $CACHE = & $this->CACHE;
            $c = substr($this->content, $prev_end_end, $current_begin - $prev_end_end);

            return $c;
        }

        return $this->getContent_BetweenIDs($prev_ID, $current_ID);
    }

    /**
     * @param integer $id1
     * @param integer $id2
     * @return string
     */
    public function getContent_BetweenIDs($id1, $id2) {
        if(is_nan($id1) || is_nan($id2)) {
            die("(is_nan($id1) || is_nan($id2)");
        }

        $t = &$this;

        $id_1_CH = &$t->foundPos_list[$id1];
        $id_2_CH = &$t->foundPos_list[$id2];
        if(!@isset($id_1_CH) || !@isset($id_2_CH)) {
            debug_print_backtrace();
            die(__FUNCTION__ . __LINE__ . ": !isset(...) $id1, $id2");
        }


        $p1begin = $id_1_CH['begin_begin'];
        $p2begin = $id_2_CH['begin_begin'];
        if($p1begin < $p2begin) {
            $p1end_end = $id_1_CH['end_end'];
            $c = substr($t->content, $p1end_end, $p2begin - $p1end_end);
        }
        else {
            $p2end_end = $id_2_CH['end_end'];
            $c = substr($t->content, $p2end_end, $p1begin - $p2end_end);
        }

        return $c;
    }

    private function setCACHE_beginEndPos($begin, $end, $pos_of_next_search, $key_foundPos_list) {
        $t = &$this;
        if(true) {
            #;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
            # plausibilitiy checks
            if(!is_string($begin) || !is_string($end)) {
                echo(__LINE__ . ': ' . "!is_string($begin) || !is_string($end)");
                debug_print_backtrace();
                die(__FUNCTION__ . '>' . __LINE__);
            }
            if(!is_numeric($pos_of_next_search) && $pos_of_next_search !== false) {
                echo(__LINE__ . ': ' . "!is_numeric($pos_of_next_search)");
                debug_print_backtrace();
                die(__FUNCTION__ . '>' . __LINE__);
            }
            if(!is_numeric($key_foundPos_list)) {
                echo(__LINE__ . ': ' . "!is_numeric($key_foundPos_list)");
                debug_print_backtrace();
                die(__FUNCTION__ . '>' . __LINE__);
            }
            #;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        }
        if(is_null($pos_of_next_search)) {
            $pos_of_next_search = 0;
        }
        $t->CACHE_beginEndPos_2_findPosKey[$begin][$end][$pos_of_next_search] = $key_foundPos_list;
        $t->setID($key_foundPos_list);

        return true;
    }


    /**
     * @return bool no meaning
     */
    public static function selfTest_collection() {
        if(self::$selfTest_collection_finished) {
            return true;
        }
        self::$selfTest_collection_finished = true;

        $silentMode = false; # only shows errors 13-10-23_13-39
//    $silentMode = true; # only shows errors 13-10-23_13-39

        list($source, $content1, $maxLoopCount, $pos_of_next_search, $begin, $end, $cf, $findPos, $sourceCF, $expectedContent) = SL5_preg_contentFinderTest1::selfTest_Tags_Parsing_Example(
          $silentMode
        );


        if(true) {
            $sourceCF = "(2)";
            $cf = new SL5_preg_contentFinder($sourceCF);
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $result = '(' . @$cf->getContent($b = '(', $e = ')') . ')';
            if(!$silentMode) {
                great($result);
            }
            if($sourceCF != $result) {
                die(__LINE__ . " : #$sourceCF# != #$result#");
            }
        }


        if(true) {
            $sourceCF = "(1((2)1)8)";
            $cf = new SL5_preg_contentFinder($sourceCF);
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $result = '(' . @$cf->getContent($b = '(', $e = ')') . ')';
            if(!$silentMode) {
                great($result);
            }
            if($sourceCF != $result) {
                die(__LINE__ . " : #$sourceCF# != #$result#");
            }
        }


        if(true) {
            $content1 = $sourceCF = '<body>
ha <!--{01}-->1<!--{/01}-->
hi {02}2<!--{/02}-->
ho  <!--{03}-->3<!--{/03}-->
</body>';
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $maxLoopCount = 0;
            $pos_of_next_search = 0;
            $begin = '(<!--)?{([^}>]*)}(-->)?';
            $end = '<!--{\/($2)}-->';
            $cf = new SL5_preg_contentFinder($sourceCF);
            $cf->setBeginEnd_RegEx($begin, $end);
            $cf->setSearchMode('use_BackReference_IfExists_()$1${1}');
            while($maxLoopCount++ < 5) {

                $cf->setPosOfNextSearch($pos_of_next_search);
//                echo __LINE__ . ": \$maxLoopCount=$maxLoopCount<br>";
                $findPos = $cf->getBorders();
                $sourceCF = @$cf->getContent();
//                echo '' . __LINE__ . ': $contentDemo=' . $contentDemo . '<br>';
                $expectedContent = $maxLoopCount;
                if($maxLoopCount > 3) {
                    $expectedContent = '';
                }
                if($sourceCF != $expectedContent) {
                    die(__LINE__ . 'ERROR :   $contentDemo != $expectedContent :' . " '$sourceCF'!= '$expectedContent ");
                }
                if(is_null($findPos['begin_begin'])) {
                    break;
                }
                if(!$silentMode) {
                    great(__LINE__ . ': ' . $content1 . ' ==> "' . $sourceCF . '"');
                }

                $pos_of_next_search = $findPos['end_end'];
            }
        }

        list($cf, $b, $e, $sourceCF) = SL5_preg_contentFinderTest1::simple123example($silentMode);

        if(true) list($cf, $b, $e, $sourceCF) = SL5_preg_contentFinderTest1::simple123example($silentMode);
        if(true) {
            # problem: Finally, even though the idea of nongreedy matching comes from Perl, the -U modifier is incompatible with Perl and is unique to PHP's Perl-compatible regular expressions.
            # http://docstore.mik.ua/orelly/webprog/pcook/ch13_05.htm
            $content1 = '<!--123_abc-->dings1<!--dings2<!--';
            $cf = new SL5_preg_contentFinder($content1);
            $sourceCF = @$cf->getContent(
              $begin = '<!--[^>]*-->',
              $end = '<!--',
              $p = null,
              $t = null,
              $searchMode = 'dontTouchThis'
            );
            if(!$silentMode) {
                info(__LINE__ . ': ' . "$content1 => $sourceCF");
            }
            $expectedContent = 'dings1';
            if($sourceCF != $expectedContent) {
                bad(" $sourceCF != $expectedContent");
                die(__LINE__);
            }
        }
        if(true) {
            # problem: Finally, even though the idea of nongreedy matching comes from Perl, the -U modifier is incompatible with Perl and is unique to PHP's Perl-compatible regular expressions.
            # http://docstore.mik.ua/orelly/webprog/pcook/ch13_05.htm
            $content1 = '123#abc';
            $cf = new SL5_preg_contentFinder($content1);
            $sourceCF = @$cf->getContent(
              $begin = '\d+',
              $end = '\w+',
              $p = null,
              $t = null,
              $searchMode = 'dontTouchThis'
            );
            if(!$silentMode) {
                info(__LINE__ . ': ' . "$content1 => $sourceCF");
            }
            $expectedContent = '#';
            if($sourceCF != $expectedContent) {
                bad(" $sourceCF != $expectedContent");
                die(__LINE__);
            }
        }
        if(true) {
            $sourceCF = 'A (i) B (i) C';
            $sourceCF = preg_replace('/\d/', 'i', $sourceCF);
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $cut = SL5_preg_contentFinderTest1::recursionExample5_search_also_in_rest_of_the_string(
              $sourceCF,
              array('[', ']')
            );
            $result = $cut[1] . $cut[0] . $cut[2];
            if(!$silentMode) {
                great(__LINE__ . ": \n$result (result)");
            }
            if(false === strpos($result, 'A [1] B [1] C') || strpos($result, '(i)')
            ) {
                die(__LINE__ . ': ' . " ERROR (i) found: \n$result (result)");
            }
        }
        if(true) {
            # recursionExample4_search_also_in_rest_of_the_string
//            $contentDemo = ' A ' . $contentDemo . ' B ' . $contentDemo . ' C ';
            $sourceCF = 'A (i) B (i) C';
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $cut = SL5_preg_contentFinderTest1::recursionExample4_search_also_in_rest_of_the_string($sourceCF);
            $result = $cut[1] . $cut[0] . $cut[2];
            if(!$silentMode) {
                great(__LINE__ . ": \n$result (result)");
            }
            $sourceCF = 'A (1) B (1) C';
            if($result != $sourceCF
            ) {
//                die(__LINE__ . ': ' . " ERROR (i) found: (proof) => \n$result (result)");
                die(__LINE__ . ': ' . " ERROR (i) found: \n'$sourceCF' (proof) => \n'$result' (result)");
            }
        }


        $sourceCF = "((2)1)";
        $cf = new SL5_preg_contentFinder($sourceCF);
        if(!$silentMode) {
            info(__LINE__ . ': ' . $sourceCF);
        } //
        $result = '(' . @$cf->getContent($b = '(', $e = ')') . ')';
        if(!$silentMode) {
            great($result);
        }
        if($sourceCF != $result) {
            die(__LINE__ . " : #$sourceCF# != #$result#");
        }

        if(true) {
            $sourceCF = "(1(1(2)1)8)";
            $cf = new SL5_preg_contentFinder($sourceCF);
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $result = '(' . @$cf->getContent($b = '(', $e = ')') . ')';
//       if(!$silentMode)great($result);
            if($sourceCF != $result) {
                die(__LINE__ . " : #$sourceCF# != #$result#");
            }
        }

        if(true) {
            # recursion example 4
            $sourceCF = SL5_preg_contentFinderTest1::getExampleContent(1);
            $sourceCF = ' A ' . $sourceCF . ' B ' . $sourceCF . ' C ';
            $sourceCF = preg_replace('/\d/', 'i', $sourceCF);
            if(!$silentMode) {
                info(__LINE__ . ': ' . $sourceCF);
            }
            $cut = SL5_preg_contentFinderTest1::recursionExample4_search_also_in_rest_of_the_string($sourceCF);
            $result = $cut[1] . $cut[0] . $cut[2];
            $proof = ' A (11(22(3)(2)22)11)(1) B (11(22(3)(2)22)11)(1) C ';
            if(!$silentMode) {
                great(__LINE__ . ": \n$proof  (proof)\n?=\n$result");
            }
            if($result != $proof) { # || strpos($result, $proof) === false
                die(__LINE__ . ': ' . " ERROR: \n'$proof' (proof) => \n'$result' (result)");
            }
        }
//        die('' . __LINE__);

        # recursion example 3
        $sourceCF = SL5_preg_contentFinderTest1::getExampleContent(1);
        $sourceCF = preg_replace('/\d/', 'i', $sourceCF);
        if(!$silentMode) {
            info(__LINE__ . ':' . $sourceCF);
        }
        $cut = SL5_preg_contentFinderTest1::recursionExample3_search_NOT_in_rest_of_the_string($sourceCF);
        $result = $cut[1] . $cut[0] . $cut[2];
//       if(!$silentMode)great("$contentDemo\n?=\n$result");
        if(strpos($result, '(11(22(3)(2)22)11)(i)') === false) {
            die(__LINE__ . ': ' . " ERROR: \n$sourceCF => \n$result");
        }

        # recursion example 2
        $sourceCF = SL5_preg_contentFinderTest1::getExampleContent(1);
        $cut = SL5_preg_contentFinderTest1::recursion_example2($sourceCF);
        $result = $cut[1] . $cut[0] . $cut[2];
        if(!$silentMode) {
            great("$sourceCF\n?=\n$result");
        }
        if($sourceCF !== $result) {
            die(__LINE__ . ': ' . " ERROR: \n$sourceCF => \n$result");
        }

        # recursion example

        $sourceCF = SL5_preg_contentFinderTest1::getExampleContent(1);

        $silentMode = false;
        if(!$silentMode) {
            echo(__LINE__ . ': <u>recursion_example</u>:');
        }
        $cut = SL5_preg_contentFinderTest1::recursion_example($sourceCF);
        if(false !== $cut) {
            die(__LINE__ . ': ' . " != $cut");
        }

        #;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $sourceCF = 'contentChange special';
        $cf = new SL5_preg_contentFinder($sourceCF);
        $noContent = @$cf->getContent($begin = 'bla', $end = 'noooo');
        if($noContent !== false) {
            die(__LINE__ . ': $noContent!==false');
        }

        #;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
# some tests 13-09-17_12-05
# selfTest($begin = '[', $end = ']', $txt = '[123456789]', $expectedString = '123456789'
        $t = new SL5_preg_contentFinder('');
        $t->selfTest('[1', ']', 0, 'asdf[12]fdsa', $expectedBehind = 'fdsa', '2');

        $t->selfTest(
          '{d w>',
          '{/d paul>',
          0,
          "{d w>something{/d paul>"
          ,
          $expectedBehind = "",
          'something',
          true
        ); # new lines should be ignored
        $t->selfTest('[', ']c', 0, 'ab[1]cd', $expectedBehind = 'd', '1');
        $t->selfTest('{', ']', 3, '{1234]' . __LINE__, $expectedBehind = __LINE__, '34');
        $t->selfTest('[', ']n', 0, '[12]n', $expectedBehind = '', '12');
        $t->selfTest('[', ']n', 0, '[12]nbb', $expectedBehind = 'bb', '12');
        # rest is not well formated yet 13-09-25_14-44
        $t->selfTest(
          '{d l>',
          '{/d ju>',
          0,
          "{d   l>something{/d ju>",
          $expectedBehind = "",
          'something'
        ); # new lines should be ignored
        $t->selfTest(
          '{d paul>',
          '{/d paul>',
          0,
          "{d \n paul>something{/d paul>",
          $expectedBehind = "",
          'something'
        ); # new lines should be ignored
        $t->selfTest(
          '[',
          ']',
          0,
          'ulm]]]uu',
          $expectedBehind = ']]uu',
          'ulm'
        ); # this is very special. because its without beginning delimiter. may you want it not work?
        $t->selfTest(
          '[',
          ']',
          0,
          ']]]',
          $expectedBehind = ']]',
          ''
        ); # this is very special. because its without beginning delimiter. may you want it not work?
        $t->selfTest();
        $t->selfTest('[A', ']', 0, "[A\n2]", $expectedBehind = "", '2');
        $line = __LINE__;
        $t->selfTest(
          '[w',
          ']',
          0,
          '[w' . $line,
          $expectedBehind = '',
          $line
        ); # this is very special. becouse its without ending delimiter. may you want it not work?
        $t->selfTest('[', ']', 0, '[]]' . __LINE__, $expectedBehind = ']' . __LINE__, '');
        $t->selfTest('[', ']', 0, '[[[]]]' . __LINE__, $expectedBehind = __LINE__, '[[]]');
        $t->selfTest('[', ']', 0, '[1]2]3]' . __LINE__, $expectedBehind = '2]3]' . __LINE__, '1');
        $t->selfTest('[', ']', 0, '[]2]3]' . __LINE__, $expectedBehind = '2]3]' . __LINE__, '');
        $t->selfTest('[', ']', 0, '123[]2]3]', $expectedBehind = '2]3]', '');
        $t->selfTest('[', ']', 0, '[1[2]3]4]]]][[', $expectedBehind = '4]]]][[', '1[2]3');
        $t->selfTest('[', ']', 0, '[123]4]]]][[', $expectedBehind = '4]]]][[', '123');
        $t->selfTest('[', ']', 0, '[12[3]4]', $expectedBehind = '', '12[3]4');
        $t->selfTest('[1', ']', 0, '[12]______', $expectedBehind = '______', '2');
        $t->selfTest('[1', ']', 0, '[123]______', $expectedBehind = '______', '23');
        $t->selfTest('[1', ']a', 0, '[12]a', $expectedBehind = '', '2');
        $t->selfTest('[1', ']a', 0, '[12]abcd', $expectedBehind = 'bcd', '2');
        $t->selfTest('[1', ']ab', 0, '[123]abcd', $expectedBehind = 'cd', '23');
        $t->selfTest('[', '3]', 0, '__[123]_', $expectedBehind = '_', '12');
        $t->selfTest('[1', '56]', 0, '__[123456]_' . __LINE__, $expectedBehind = '_' . __LINE__, '234');
        $t->selfTest('<d2>', '</d2>', 0, '<d1><d2><d3></d3></d2></d1>', $expectedBehind = '</d1>', '<d3></d3>', true);
        $t->selfTest('[1', '9]', 0, '[123456789]', $expectedBehind = '', '2345678', true);


        $rebuild = SL5_preg_contentFinderTest1::recursion_example4($silentMode);


        SL5_preg_contentFinderTest1::bordersBeetweenExample($cf, $silentMode, $rebuild, $source);


        self::content_before_behind_example($silentMode);

        if(!$silentMode) {
            great(__LINE__ . ' Everything OK. No errors :-)');
        }

        return true;
    }

    /**
     * @param null $RegEx_begin perl regular expression
     * @param null $RegEx_end perl regular expression
     * @param null $pos_of_next_search
     * @param null $txt
     * @param null $searchMode
     * @param bool $bugIt
     * @return bool|string
     */
    public function getContent(
      &$RegEx_begin = null,
      &$RegEx_end = null,
      $pos_of_next_search = null,
      &$txt = null,
      $searchMode = null,
      $bugIt = false
    ) {
        if(false && !is_null($this->content)
          && !is_null($this->foundPos_list)
        ) {
            return $this->content;
        }
        if(is_null($txt)) {
            $txt = $this->content;
        }
        $this->update_RegEx_BeginEndPos($RegEx_begin, $RegEx_end, $pos_of_next_search);
        count_null(array($RegEx_begin, $RegEx_end, $pos_of_next_search));
        if(!$searchMode) {
            $searchMode = $this->getSearchMode();
        }
        $p = $this->getBorders(
          $RegEx_begin,
          $RegEx_end,
          $pos_of_next_search,
          $txt,
          $searchMode
        );
        $count_null = count_null(array($p['begin_begin'], $p['end_begin']), false);
        if($count_null > 0) {
            if($count_null == 2 && $this->stopIf_BothBorders_NotExistInContent === true) {
                return false;
            }
            if($count_null == 2) {
                return substr($txt, $pos_of_next_search);
            }

            if(is_null($p['end_begin'])) {
                if($this->stopIf_EndBorder_NotExistInContent === true) {
                    return false;
                }
                else {
                    $p['end_begin'] = strlen($txt);
                }
            }

            if(is_null($p['begin_begin'])) {
                return substr(
                  $txt,
                  $pos_of_next_search,
                  $p['end_begin'] - $pos_of_next_search
                );
            }

        }
        $content = substr($txt, $p['begin_end'], $p['end_begin'] - $p['begin_end']);

        return $content;
    }

    /**
     * @param string $begin
     * @param string $end
     * @param int $pos_of_next_search
     * @param string $txt
     * @param null $expectedBehind
     * @param string $expectedContent
     * @param null $searchMode
     * @param bool $bugIt
     * @return bool
     */
    public
    static function selfTest(
      $begin = '[',
      $end = ']',
      $pos_of_next_search = 0,
      $txt = '_[123]_',
      $expectedBehind = null,
      $expectedContent = '123',
      $searchMode = null,
      $bugIt = false
    ) {
        if(false) {
            $bugIt = (basename(__FILE__) == basename(
                $_SERVER['PHP_SELF']
              ));
        } # // TODO attention: bugIt parameter is ignored here 13-09-20_08-59
        echo '</pre>';
//        var_export(get_func_argNames_of_Method('ContentFinder', 'selfTest')); # it works :)
//        var_export(get_func_argValues_of_Method('ContentFinder', 'selfTest')); # gives back null :(
//        die('13-09-17_11-19');
        $silentMode = true;
        $argNames = self::get_func_argNames_of_Method(__CLASS__, __FUNCTION__);
        if(!$silentMode) {
            foreach($argNames as $k) {
                echo " $k=" . ${$k} . '  ';
            }
        }

//        var_export($argNames);
        $cf = new SL5_preg_contentFinder($txt);

        if(is_null($searchMode)) {
            $searchMode = $cf->getSearchMode();
        }

        #;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        # the following code use enables you to use null in argument list for using default value.
        # it needs a defined empty class  array called $selfTest_defaults
        # $selfTest_defaults will be filled in the moment of first empty call.
        # useful, because you could use defaults at only one pace and you could use null for using default values.
        # what you need first time is an empty call of the function. you need to fill the default values.
        $func = func_get_args();
        $behind = self::$selfTest_defaults;
        $t = new SL5_preg_contentFinder($txt);
        if(count(self::$selfTest_defaults) == 0) {
            $temp2 = self::$selfTest_called_from_init_defaults;
            if($temp2 === true) {
                self::$selfTest_defaults =
                  array($begin, $end, $pos_of_next_search, $txt, $expectedBehind, $expectedContent, $bugIt);

                return true; # this call from constructor was only for init default values.
            }
            else {

                self::selfTest_init_defaults();
//                $t::selfTest_init_defaults();
            }
        }
        # set args with value null to default value.
        foreach($func as $k => $arg) {
            if(is_null($arg)) {
                $argNames = $t->get_func_argNames_of_Method(__CLASS__, __FUNCTION__);
                ${$argNames[$k]} = SL5_preg_contentFinder::$selfTest_defaults[$k];
            }
        }
        #;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if($bugIt) {
            list($findPos['begin_begin'], $findPos['end_begin']) = $t->getBorders(
              $begin,
              $end,
              $pos_of_next_search,
              $txt,
              $searchMode
            );
        }
        $content = $t->getContent($begin, $end, $pos_of_next_search, $txt, $searchMode, $bugIt);

        if($bugIt) {
            echo '<font style="font-family: monospace">';
        }

        if($content != $expectedContent) {
            echo '</pre>';
            bad("$content != $expectedContent");
            if(true) {
                info(__LINE__ . ': ' . $txt);
                echo '<hr>line <u>' . __LINE__ . '</u>: </pre>' . "   b= '<b>" . htmlspecialchars(
                    $begin
                  ) . "</b>' , e= '<b>" . htmlspecialchars($end) . "</b>' , from pos= $pos_of_next_search </pre>";
                echo "<b>" . htmlspecialchars($txt) . '</b> => ' . "<b>" . htmlspecialchars(
                    $content
                  ) . '</b> (' . $findPos['begin_begin'] . '-' . $findPos['end_begin'] . ")";
                echo '<br>' . implode('', range(0, 9)) . implode('', range(0, 9)) . implode('', range(0, 9));
            }
            if($content != $expectedContent) {

                $findPos = $t->getBorders(
                  $begin,
                  $end,
                  0,
                  $txt,
                  $searchMode
                );

                echo '<br>' . __LINE__ . ':' . 'list(' . $findPos['begin_begin'] . ', ' . $findPos['end_begin'] . ') ';
                die("\n" . '<br><b>ERROR \'' . htmlspecialchars($content) . '\' != \'' . htmlspecialchars(
                    $expectedContent
                  ) . "'</b> (expected)");

            }
            if(!is_null($expectedBehind)) {
                $end_end = $t->CACHE_current('end_end');
                $behind = substr($txt, $end_end);
                if(true && $expectedBehind != $behind) {
                    die('<br>' . __LINE__ . ": 13-09-25_16-28");
                }
            }

            # try to find contentDemo before
//            $content_before;


        }

        return true;
    } # EndOf selfTest

    private static function get_func_argNames_of_Method($className, $funcName) {
        # // TODO this function not really net to be a part of a this class, but this class use it.
        $f = new ReflectionMethod($className, $funcName);
        $result = array();
        foreach($f->getParameters() as $param) {
            $result[] = $param->name;
        }

        return $result;
    }

    /**
     * @param $Content
     * @param int $size1
     * @param int $size2
     */
    public function echo_content_little_excerpt(
      $Content
      ,
      $size1 = 60,
      $size2 = 50
    ) {
        great(
          __LINE__ . ': echo_content_little_excerpt: <b>'
          . htmlspecialchars(substr($Content, 0, $size1))
          . " ...\n   "
          . htmlspecialchars(substr($Content, -$size2)) . '</b>',
          false
        );

        return substr($Content, 0, $size1) . "..." . (substr($Content, -$size2));

    }

    /**
     * @param integer $fromLine callers lineNumber. may help debuging
     * @param string $file fileName . may help debuging
     * @param string $s
     */
    public function nl2br_Echo($fromLine, $file, $s) {
        # // TODO this function not really net to be a part of a this class, but this class use it.
        echo '' . $fromLine . ': ' . nl2br($s) . '<br>';
    }

    /**
     * @param $string
     * @return string Quoted regular expression
     */
    public static function preg_quote_by_SL5(&$string) {
        # btw must have lib: http://regexlib.com/Search.aspx?k=email
        $r = preg_quote($string);
        # preg_quote Quote regular expression characters
        # @link http://php.net/manual/en/function.preg-quote.php
        $r = str_replace('/', '\/', $r);
        $r = preg_replace('/\s+/sm', '\s+', $r);

        return $r;
    }

    private function update_RegEx_BeginEndPos(&$RegEx_begin, &$RegEx_end, &$pos_of_next_search) {
        $doOverwriteSetup = false;
        $t = &$this;
        $doOverwriteSetup_OF_pos_of_next_search = $t->doOverwriteSetup_OF_pos_of_next_search;
        if(is_null($RegEx_begin)) {
            if(is_null($t->getRegEx_begin())) {
                die(__LINE__ . ':is_null(BeginRegEx');
            }
            $RegEx_begin = $t->getRegEx_begin();
        }
        elseif($doOverwriteSetup || is_null($t->getRegEx_begin())) {
            $t->setRegEx_begin($RegEx_begin);
        }
        if(is_null($RegEx_end)) {
            if(is_null($t->getRegEx_end())) {
                die(__LINE__ . ':is_null(EndRegEx');
            }
            $RegEx_end = $t->getRegEx_end();
        }
        elseif($doOverwriteSetup || is_null($t->getRegEx_end())) {
            $t->setRegEx_end($RegEx_end);
        }

        if(is_null($pos_of_next_search)) {
            if(is_null(
              $t->pos_of_next_search
            )
            ) {
                $t->pos_of_next_search = 0;
            } // that's default value. if you want start search from the beginning. 13-10-25_12-38
            $pos_of_next_search = $t->getPosOfNextSearch();
        }
        elseif($doOverwriteSetup_OF_pos_of_next_search || is_null($t->getRegEx_begin())) {
            $t->setPosOfNextSearch(
              $pos_of_next_search
            );
        }

        return true;
    }

    private function update_key_foundPos_list(&$findPos, &$matchesReturn) {
        $count = count($this->foundPos_list);
        $key_foundPos_list = (is_numeric($count)) ? $count : 0;
        $this->foundPos_list[$key_foundPos_list] = $findPos;
        if($this->foundPos_list_current_ID === $key_foundPos_list) {
            die(__FUNCTION__ . __LINE__ . ': $this->foundPos_list_current_ID == $key_foundPos_list = ' . $key_foundPos_list);
        }

        $this->foundPos_list[$key_foundPos_list]['matches'] = $matchesReturn;
        $this->setID($key_foundPos_list);

        return $key_foundPos_list;
    }

    private function setRegEx(&$RegEx_old, &$RegEx_new) {
        if(!is_null($RegEx_new) && !is_string($RegEx_new)) {
            die(__FUNCTION__ . __LINE__ . ': !is_string(' . htmlspecialchars($RegEx_new) . ')');
        }
        $RegEx_old = $RegEx_new;

        return true;
    }

}

function get_func_argValues_of_Method($className, $funcName) {
    # // TODO unused function
    $f = new ReflectionMethod($className, $funcName);
    $result = array();
    foreach($f->getParameters() as $param) {
        $result[] = $param->value;
    }

    return $result;
    function wwwSearchResults() {
        echo '

google search: "php parser yiidecoda +milesj.me"

$code->setBrackets(\'{\', \'}\');
Decoda by milesj http://milesj.me/code/php/decoda
http://bakery.cakephp.org/articles/view/4cb57d06-6a34-4cab-86e7-4eadd13e7814/lang:deu

http://www.yiiframework.com/extension/yiidecoda/#hh1
\'brackets\' => array({, }),

https://gist.github.com/johnkary/5596493

http://php.net/manual/de/function.json-decode.php

        ';
    }
}

function get_func_argNames($funcName) {
# // TODO unused function
    $f = new ReflectionFunction($funcName);
    $result = array();
    foreach($f->getParameters() as $param) {
        $result[] = $param->name;
    }

    return $result;
# print_r(get_func_argNames('get_func_argNames'));
}

function bad($message) {
    echo "<div style='background-color: #ff0000'>:-( $message</div><p>";
    echo '</pre>';
    debug_print_backtrace(); # http://www.php.net/manual/de/function.debug-print-backtrace.php
    #PHP_com
}

function bad_little($message) {
    echo "<div style='background-color: #ff5555'>:-( $message</div><p>";
    #debug_print_backtrace(); # http://www.php.net/manual/de/function.debug-print-backtrace.php
    #PHP_com
}


function great($message, $htmlSpecialChars = true) {
    echo "\n";
    if($htmlSpecialChars) {
        $messageNEW = htmlspecialchars($message);
        if($messageNEW != '') {
            $message = $messageNEW;
        }
    }
    echo "<div style='background-color: greenyellow'>" . $message . "</div><p>";
    echo "\n";

    return true;
}

function info($message, $color = 'yellow', $htmlSpecialChars = true) {
    if($htmlSpecialChars) {
        $messageNEW = htmlspecialchars($message);
        if($messageNEW != '') {
            $message = $messageNEW;
        }
    }
    echo "<div style='background-color: $color'>"
      . $message . "</div><p>";
}


function count_null($arr, $dieIfIsNull = true) {

    $countNull = 0;
    if(!is_bool($dieIfIsNull)) {
        die(__FUNCTION__ . __LINE__ . ': is_bool($dieIfIsNull)');
    }
    if(!is_array($arr)) {
        return is_null($arr);
    }
    foreach($arr as $n => $v) {
        if(is_null($v)) {
            if($dieIfIsNull !== true) {
                $countNull++;
            }
            else {
                echo(__FUNCTION__ . '>' . __LINE__ . ": $n => is_null($v)");
                debug_print_backtrace();
                die(__FUNCTION__ . __LINE__);
            }
        }
    }

    return $countNull;
}