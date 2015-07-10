<?php
#     walks throw dir PHPUnit copies all unitTest into PHPUnitAllTest_AutoCollected.php
# pls dont use any other functions inside the source files there together with unit test functions (in same file). thanks.
$allTestsFileName = 'PHPUnitAllTest_AutoCollected.php';
$dir = pathinfo(__FILE__)['dirname'] . '/PHPUnit';
include_once pathinfo(__FILE__)['dirname'] . '/../SL5_preg_contentFinder.php';
$extendsUniTest = 'extends PHPUnit_Framework_TestCase';
$contentAll = str_repeat(' <h1>Dont edit this file. its overwritten next !</h1> \n ', 1);
$contentAll .= ' <?php
 $f = \'SL5_preg_contentFinder.php\';
 while(!file_exists($f)) {
    $f = \'../\' . $f;
    echo "$f exist.";
}
include_once $f;
include_once \'_callbackShortExample.php\';';
$contentAll .= '
   class TestAll ' . $extendsUniTest . ' {';
echo __FILE__;
$basename = pathinfo(__FILE__)['basename']; // redundant but for any future reasons. evantually ;)
//$p['filename'.'.'.$p['extension'];
$r = $dh = opendir($dir);
if(!$r) {
    die('15-06-18_21-44');
}
$fileCount = 0;
while(($file = readdir($dh)) !== false
) {
    if(in_array($file, array($basename, $allTestsFileName, $basename))
      || substr($file, -4) != '.php'
    ) {
        continue;
    }
    $file_content = file_get_contents($dir . '/' . $file);
    // class SL5_preg_callbackTest extends PHPUnit_Framework_TestCase
    echo strlen($file_content). ' \n<br>';
    $contentClass = preg_replace('/.*?extends\s+PHPUnit_Framework_TestCase\s*\{(.*)\}[^}]*/s', "$1", $file_content);
    if(strlen($contentClass) != strlen($file_content)) {
        echo "<h1>filename: $file  </h1>\n";
        $contentAll .= $contentClass;
        $fileCount++;
    }
}
$contentAll .= ' }';
//$contentAll .= "\n // 15-06-19_13-58 \n } // \$fileCount=$fileCount \n";
closedir($dh);
if($fileCount < 2) die('$fileCount =' . $fileCount . ' :( 15-06-19_12-00');
//$contentAll = preg_replace('/\}.*?$/', '', $contentAll); # dirty bugFix
echo htmlspecialchars($contentAll);
//$contentAll = preg_replace('/\}\s*\}\s*\}\s*$/', '\}', $contentAll);
file_put_contents($dir . '/' . $allTestsFileName, $contentAll);
