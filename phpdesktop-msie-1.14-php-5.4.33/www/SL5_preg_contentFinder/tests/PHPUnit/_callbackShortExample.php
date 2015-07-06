<?php


class callbackShortExample {
    public $newline = "\r\n";
    private $openOld = '';
    private $closeOld = '';
    private $openNew = '';
    private $closeNew = '';
    private $indentSize = 2;
    private $charSpace = '.';

    public function __construct($old_open, $old_close, $new_open_default, $new_close_default, $charSpace) {
        $this->openOld = $old_open;
        $this->closeOld = $old_close;
        $this->openNew = $new_open_default;
        $this->closeNew = $new_close_default;
        $this->charSpace = $charSpace;
    }
    public function start($source1) {
        $cf = new SL5_preg_contentFinder($source1);
        $cf->setBeginEnd_RegEx($this->openOld, $this->closeOld);
        list($cBefore, $content, $cBehind) = $cf->getContent_user_func_recursive(
          function ($before) { return $before . $this->openNew; },
          [$this, 'onContent'],
          [$this, 'onClose']);
        $source2 = $cBefore . $content . $cBehind;

        return $source2;
    }
    public function onOpen($before, $cut, $behind, $deepCount) {
        return $before . $this->openNew;
    }
    public function onClose($before, $cut, $behind, $deepCount) {
        if($cut === false) return $behind;
        $n = $this->newline;
        $indentStr = $this->getIndentStr($deepCount - 1);

        return $indentStr . $this->closeNew . $n . ltrim($behind);
        # todo: $behind dont need newline at the beginning
    }
    public function onContent($before, $cut, $behind, $deepCount) {
        if($cut === false) return $cut;
        $n = $this->newline;
        $indentStr = $this->getIndentStr($deepCount, $this->charSpace);;
        $cut = $n . $indentStr . preg_replace('/' . $n . '[ ]*([^\s\n])/', $n . $indentStr . "$1", $cut);
        $cut .= $n;

        return $cut;
    }
    /**
     * @param int $indent
     * @param string $char
     * @return string
     */
    private function getIndentStr($indent, $char = null) {
        if(is_null($char)) $char = $this->charSpace;
        $multiplier = $this->indentSize * $indent;
        $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

        return $indentStr;
    }

}

function recursion_simplyReproduction(
  $content,
  $before = null,
  $behind = null
) {
    $isFirstRecursion = is_null($before); # null is used as trigger for first round.
    $cf = new SL5_preg_contentFinder($content);
    if($cut = @$cf->getContent($b = '{', $e = '}')) {
        $before .= $cf->getContent_Before() . '{';
        $behindTemp = $cf->getContent_Behind() . $behind;


        if($isFirstRecursion) {
            list($c, $bf, $bh) =
              recursion_simplyReproduction($behindTemp); // this version of recursion also includes the rest of contentDemo.
            $behind = (is_null($c)) ? '}' . $behindTemp : '}' . $bf . $c . $bh;
        }
        else {
            $behind = '}' . $behindTemp;
        }

        $return = recursion_simplyReproduction(
          $cut,
          $before,
          $behind
        );

        return $return;
    }
    $return = array(($cut) ? $cut : $content, $before, $behind);

    return $return;
}

function getIndentStr($indent, $char, $indentSize) {
    $multiplier = $indentSize * $indent;
    $indentStr = str_repeat($char, (($multiplier < 0) ? 0 : $multiplier));

    return $indentStr;
}