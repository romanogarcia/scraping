<?php
$arr = array("hey","hellow","world");
//$arr = "text";
if(is_array($arr))
   echo "im array";
else echo "not array";
$d = (is_array($arr))? $arr[0] : $arr;

echo PHP_EOL . $d . PHP_EOL;
