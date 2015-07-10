<?php
//include_once 'SUtility.ahk'
$file_content_original = file_get_contents('SUtility.ahk');
$file_content_compressed_1 = preg_replace("/\s+/", " ", $file_content_original);
$file_content_compressed_2 = preg_replace("/[\r\n]\s+/", "\r\n", $file_content_original);
file_put_contents('input_compressed_1.ahk',$file_content_compressed_1);
file_put_contents('input_compressed_2.ahk',$file_content_compressed_2);