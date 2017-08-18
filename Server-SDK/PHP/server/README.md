本SDK实现的功能是从服务器获取通信topicId和读取jwt文件。本sdk内已实现jwt的验签功能。

## 开发准备

1：需要在php服务器端载入keystore，sign两个类库，安装方式请参照具体类库内的README.md文件。

## 开发DEMO

开发demo请参照test.php

demo代码的相关说明：	

1：首先要加载lib/jwt.class.php：
例：require_once 'lib/jwt.class.php';
	
2：初始化jwt类：
 ```xml
	// 生成需要获取的IMI权限，snsapi_idcard为用户身份证信息，snsapi_info为用户信息，参数未来会有扩展
	$scope = array('snsapi_idcard','snsapi_info');
	// 获取配置信息
	$jwt = new Jwt('/imi/imi-config.properties', '/imi/imi-ks');
```
	
3：调用需要的方法，目前本SDK对外使用的方法有获取topicId方法(createChannel)和获取信息方法(getAuthorizationInfo)：
 ```xml
	// 返回数组包含topicId
	$channel = $jwt->createChannel($scope);
	// 获取所有符合scope的用户数据
	$info= $jwt->getAuthorizationInfo($topicId, $scope);
```

## 在线测试地址

http://172.16.192.105:85/imi/client/vport.html
