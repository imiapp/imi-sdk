#### Android 接入说明

### 1 SDK使用说明
  
导入sdk相关的jar包，放在libs目录。

（1）第三方调用登录授权方法的时候，自身应先实现一个创建通道的接口，在此之前需要第三方调用自己服务器接口获取topicid， sdk里接口定义为CreateChannelService。

（2）调起IMI应用并且成功响应登录授权操作后，IMI会回到第三方app，在onActivityResult方法里，第三方调用获取授权信息的接口取数据。

### 2 SDK API

方法一 ：登录（方法名为reqLogin）  
方法二 ：授权（方法名为reqAuthorize）  
方法三： 检查IMI的安装（方法名为isIMIAppInstalled）  

### 3 Demo

https://github.com/imiapp/android-demo
