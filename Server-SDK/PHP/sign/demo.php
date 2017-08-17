<?php
require_once 'lib/SignatureVerify.class.php';
$msg = 'this is a message!';
$privatKey = 'e4ce35c1ccf7f5d79a838bd527a0888fefb1523ce2fca52abd681d0e493bd5ad';
$publicKey = '743352f77078a12f30d37d01783706d5b6dff809';
$signFromServer = '3a6f21e17b981d8d08677e0d3010f3aa9c2b8844f0b583eb0f0d992592601c1c6698980277a4b401541250a192a316cb681e7571aa883d2974626f662c83fcc31c';


$sign = SignatureVerify::createSign($msg, $privatKey);
var_dump($sign);
$signVerify = SignatureVerify::verifySign($msg, $publicKey, $signFromServer);
var_dump($signVerify);