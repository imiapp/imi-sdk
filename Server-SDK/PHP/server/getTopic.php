<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods:POST');  
header('Access-Control-Allow-Headers:x-requested-with,content-type'); 
require_once('lib/Jwt.class.php');

$data = json_decode(file_get_contents("php://input"),true);
if(empty($data)||$data=='')die();

$jwt = new Jwt('imi-config.properties', 'imi-ks');//配置文件位置 

$version = $data['version'];
$scope = explode(',', $data['scope']);
$newScope = '';
if(count($scope)<1){
	foreach ($scope as $item){
		if(trim($item)!=''){
			$newScope[] = $item;
		}
	}
}else{
	$newScope = $scope;
}

$channel = $jwt->createChannel($newScope,$version);
$channel['openId']=$channel['openid'];
$ret = array('retCode'=>'0000000','result'=>$channel);
echo json_encode($ret);

