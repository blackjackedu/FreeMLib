<?php
$a = array('ajax'=>'ajaxPhp1');
$a = json_encode($a);
header('Content-Type: application/json');
echo 'jsonpCallback('.$a.')';//回调函数callbackFunction
//echo $a;
?>