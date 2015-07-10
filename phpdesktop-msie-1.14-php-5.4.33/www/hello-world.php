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

$root = $_SERVER['DOCUMENT_ROOT'];
$str = $root . "\n\n\n" . $t . "\n\n\n" . 'address=' . implode("\n", arguments($argv));
echo $str;
file_put_contents($t.'.txt', $str);