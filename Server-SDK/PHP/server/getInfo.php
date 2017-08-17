<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods:POST');  
header('Access-Control-Allow-Headers:x-requested-with,content-type'); 
require_once('lib/Jwt.class.php');

$data = json_decode(file_get_contents("php://input"),true);
if(empty($data)||$data=='')die();

$jwt = new Jwt('imi-config.properties', 'imi-ks');//配置文件位置 

$topicId = $data['topicId'];
$scope = explode(',', $data['scope']);
$newScope = '';
foreach ($scope as $item){
	if(trim($item)!=''){
		$newScope[] = $item;
	}
}


$info=$jwt->getAuthorizationInfo($topicId,$newScope);

$newInfo = '';
foreach($info['authorizer'] as $key=>$item){
	$newInfo[$key] = $item;
}
if($info['identityCardInfo']){
	foreach($info['identityCardInfo'] as $key=>$item){
	        $newInfo[$key] = $item;
	}
}
if($info['loginUserInfo']){
	foreach($info['loginUserInfo'] as $key=>$item){
	        $newInfo[$key] = $item;
	}
}

$ret = array('retCode'=>'0000000','result'=>$newInfo);
echo json_encode($ret);
