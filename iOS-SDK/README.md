### iOS接入指南

## 1 环境配置
#### 使用场景：第三方APP使用IMI APP，进行登录或者授权操作。第三方app需要按照以下步骤配置：
步骤 1：添加第三方依赖库:AFNetworking. 可以使用我方提供的资源包，导入到工程项目中使用；也可以通过CocoaPods集成。

步骤 2：设置白名单权限，以便调起IMI应用。  
配置代码如下：
右键打开plist文件,open as-->source code
 添加以下代码: 
 ```xml
 <key>LSApplicationQueriesSchemes</key>  
 <array>  
 <string>WFVportAllowLogin</string>  
 </array>  
```
iOS9.0之后由于隐私设置，需要配置此权限调起其他应用。  
配置后检查下是否设置成功：  
配置成功后，plist文件应该会有这个标识。 
  
<img src="https://raw.githubusercontent.com/imiapp/imi-sdk/master/iOS-SDK/1.png" width="660" />  
  
步骤 3：设置URL Schemes。  
打开工程，将工程的Bundle Identifier 设置为URL Schemes。如下图配置： 
  
<img src="https://raw.githubusercontent.com/imiapp/imi-sdk/master/iOS-SDK/2.png" width="660" />  
  
<img src="https://raw.githubusercontent.com/imiapp/imi-sdk/master/iOS-SDK/3.png" width="660" />  
提示：如果第三方app更新了Bundle Identifier，相应的URL Schemes也需要修改，两者要保持一致。
  
步骤 4：导入IMI资源包（BuildIMISdk.framework）。  
将资源包导入到第三方工程中，就可以使用IMI提供的相关功能了。
步骤 5：一定要阅读sdk中所有函数方法的注释，在BuildIMISdk.h文件中，这里简单介绍下sdk中方法的使用：
函数一：+(instancetype _Nullable )sharedInstance;单例对象，在整个程序中只存在一个对象，节省内存资源，提高运行效率。

## 2 SDK使用说明

对外暴露头文件：  
（1）	BuildIMISdk.h  
（2）	接口API暴露在BuildIMISdk.h头文件中。  
（3）  概述—获取topicId函数：  
函数名：createChannelReq，是一个需要第三方实现有返回值的block函数。  
第三方APP调用自己服务器接口，获取topicId成功，调用登录或授权方法返回给createChannelReq函数，sdk开始调起IMI。  
（4）  handler：IMI回调结果。  
如果回调结果有值，代表IMI已经跳转回了第三方APP。此函数可以拦截到IMI返回给第三方的信息。  
如果登录或者授权成功，IMI回调结果result为success，如果失败，error里会显示具体的失败信息。  
result为success时，代表IMI推送信息成功，第三方APP才能在回调函数里向第三方服务器请求登录或授权信息，

## 3 SDK API（详见BuildIMISdk.h） 
方法一 ：登录（方法名为reqLogin）  
方法二 ：授权（方法名为reqAuthorize）  
方法三 ：回调函数（方法名为IMI_HandleBackUrl）  
方法四： 检查IMI的安装（方法名为isIMIInstalled）  
方法五： API单例（方法名为sharedInstance）

## 4 Demo

https://github.com/imiapp/iOS-demo







