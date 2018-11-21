### iOS接入指南

## 1 环境配置
#### 使用场景：第三方APP使用IMI APP，进行登录或者授权操作。第三方app需要按照以下步骤配置：
步骤 1：添加第三方依赖库:AFNetworking. 可以使用我方demo中提供的资源包AFNetworking，导入到工程项目中使用；也可以通过CocoaPods集成。

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

## 重要提示：如果第三方app更新了Bundle Identifier，相应的URL Schemes也需要修改，两者要保持一致。
  
  
步骤 4：导入IMI资源包（BuildIMISdk.framework）。  
将资源包导入到第三方工程中，就可以使用IMI提供的相关功能了。

步骤 5：一定要阅读sdk中所有函数方法的注释和介绍，在BuildIMISdk.h文件中。
前提：需要第三方app向自己后台服务器获取topicid，拿到topicid后，才能使用IMI的登录或者授权操作。

步骤6：一定要阅读demo，包名为PoliceFoShan，此demo模拟第三方app，使用IMI的登录或授权功能，代码详细写清了如何使用IMI登录或授权功能。
文末附有demo的下载地址。


## 2 SDK使用说明

对外暴露头文件：  
（1）	BuildIMISdk.h  
（2）	接口API暴露在BuildIMISdk.h头文件中。  
（3） 前提—---获取topicId：  
demo中的方法名为- (void)postTopicIDparams:(NSDictionary*)params success:(void (^)(id json))success failure:(void (^)(NSError *error))failure；这是一个需要第三方app实现的网络请求方法。  即向自己服务器注册获取topicid。
两个必传参数：版本号：@"version"= @"2.6.0";使用的IMI的版本号。
类型：@"scope" = @"snsapi_card"：代表使用IMI授权。@"scope" = @"snsapi_info";代表使用IMI登录。
第三方APP调用自己服务器接口，获取topicId成功，调用登录或授权方法返回给createChannelReq函数，sdk开始调起IMI。

（4） sdk中的方法的使用和介绍：
方法一：+(instancetype _Nullable )sharedInstance;单例对象：在整个程序中只存在一个对象，节省内存资源，提高运行效率。

方法二：- (BOOL)isIMIinstalled;检查用户是否安装了IMI APP：安装返回YES，否则返回NO。

方法三：- (void)reqLogin:(NSString *_Nullable)name andCreateChannelBlock:(createChannelBlock _Nullable )createChannelReq  andIMIResponseHandler:(nullable handlerBlock)handler;使用IMI登录，可以获取IMI用户的用户名和电话号码。

参数介绍：
andCreateChannelBlock:这个block需要第三方app向sdk：return也就是传递topicid。sdk拿到topicid后向IMI请求登录数据。
andIMIResponseHandler:登录成功或者失败的回调。成功后获取到的数据是json数据，解析展示即可。

方法四：- (void)reqAuthorize:(NSString *_Nullable)type name:(NSString *_Nullable)name andCreateChannelBlock:(createChannelBlock _Nullable )createChannelReq  andIMIResponseHandler:(nullable handlerBlock)handler;使用IMI授权，可以获取IMI用户的授权信息：身份证或电话相关的信息。 
参数介绍：
type:用于区分授权类型,目前支持类型为:1.type = "snsapi_idcard,snsapi_info"，代表获取登录信息和身份证信息。2.type="snsapi_idcard",代表只获取身份证信息;
name:第三方app的名称；
andCreateChannelBlock:这个block需要第三方app向sdk：return也就是传递topicid。sdk拿到topicid后向IMI请求授权数据。
andIMIResponseHandler:授权成功或者失败的回调。成功后获取到的数据是json数据，解析展示即可。
       
方法五：- (void)IMI_HandleBackUrl:(NSString *_Nullable)url;IMI授权完成后的回调函数。需要需要在application: openURL方法实现。  
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







