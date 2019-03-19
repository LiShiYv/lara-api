<?php
$str="hello";
$key='pass';
$api='AES-128-CBC';
$argc=OPENSSL_RAW_DATA;
$iv=mt_rand(11111,99999).'aaaaaaaa111';
$enc_str=openssl_encrypt($str,$api,$key,$argc,$iv);
var_dump($enc_str);
$dec_str=openssl_decrypt($enc_str,$api,$key,$argc,$iv);
echo $dec_str;die;
?>