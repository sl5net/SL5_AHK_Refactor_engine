<?php
/*
	file: inline_example.php

	This file shows an example of the inline diff usage.

	The inline_diff code was written by Ciprian Popovici in 2004,
	as a hack building upon the Text_Diff PEAR package.
	It is released under the GPL.

	There are 3 files in this package: inline_example.php, inline_function.php, inline_renderer.php
*/
	
	include_once 'inline_function.php';

	$text1='This product comes with NO WARRANTY. There are no refnds.';
	$text2='This product comes with a 3yr warranty. There are no refunds.';


//	$text1 = file_get_contents('a.html');
//	$text2 = file_get_contents('b.html');
	$nl = '#**!)@#';
	$diff = inline_diff($text1, $text2, $nl);
//	echo str_replace($nl,"\n",$diff)."\n";
echo '<style>del{background:#fcc}ins{background:#cfc}</style>'.$diff."\n";
?>