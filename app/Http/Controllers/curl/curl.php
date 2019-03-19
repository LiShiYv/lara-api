<?php
$url ="http://news.baidu.com";
$ch = curl_init();
//$post=[
//    'x'=>'ssss',
//        'q'=>'qqqq'
//];
$file_data=[
    'a'=>'b',
    'update'=>new CURLFile("a.jpg")
];
var_dump($file_data);
curl_setopt($ch,CURLOPT_URL,$url);
//curl_setopt($ch,CURLOPT_HEADER,false);
curl_setopt($ch,CURLOPT_POST,true);
//curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
curl_setopt($ch,CURLOPT_POSTFIELDS,$file_data);
curl_exec($ch);
curl_close($ch);
var_dump($ch);die;