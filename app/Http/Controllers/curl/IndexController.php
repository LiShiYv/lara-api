<?php
$url ="https://movie.douban.com/chart";
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_HEADER,0);
$re=curl_exec($ch);
var_dump($re);
$rs=file_put_contents('db.html',$re,FILE_APPEND);
var_dump($rs);die;
?>