#### Android 接入说明

### 1 SDK使用说明
  
 导入sdk相关的jar包，放在libs目录。
 
（1）第三方调用登录授权方法的时候，自身应先实现一个创建通道的接口，在此之前需要第三方调用自己服务器接口获取topicid， sdk里接口定义为CreateChannelService。

（2）调起IMI应用并且成功响应登录授权操作后，IMI会回到第三方app，在onActivityResult方法里，第三方调用获取授权信息的接口取数据。

### 2 SDK API

调用API之前先实例化，只传入上下文，也可以选择开启日志输出，LOG打印默认为关闭状态。

IMIAPI imiapiFactory = IMIAPIFactory.createIMIAPI(MainActivity.this, true);

方法一 ：登录（方法名为reqLogin）

imiapiFactory.reqLogin(name, MainActivity.this);

传入参数为第三方APP名称和上下文对象。

方法二 ：授权（方法名为reqAuthorize）

imiapiFactory.reqAuthorize(scope, name, ReservationActivity.this);

参数说明：

scope为第三方授权想要拿到的数据类型， snsapi_info为登录信息，snsapi_idcard为身份证信息，支持单传单取和多传多取，多传时候多个字符串用，隔开。
然后是传入第三方APP名称和上下文对象。

注意事项：

登录授权操作成功后（IMI弹框消失）第三方是在 回调onActivityResult方法里面调用自己服务端的接口获取。

方法三： 检查IMI的安装（方法名为isIMIAppInstalled）

用于判断IMI是否安装，第三方需给出必要提示，在登录授权操作之前。

### 3 Demo

https://github.com/imiapp/android-demo
