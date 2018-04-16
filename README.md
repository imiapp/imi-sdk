# imi-sdk
## 1 背景介绍

  IMI系统是一个开放的平台，它提供了相关的SDK/开发库并允许第三方应用和平台来使用、建设和完善数字身份和数字空间。



  当前IMI系统对于第三方应用平台的开发支持如下：

1. Java后端平台（JDK1.7及以上）
2. PHP后端开发（PHP5.6及以上）
3. Javascript前端开发
4. Android应用开发（Android 5.0及以上）
5. iOS应用开发（iOS 9.0及以上）

  第三方APP/平台要能对接和使用IMI系统的功能，其网站或者APP需要有对应的后台服务器做支撑，并由第三方服务器和客户端二者使用相关的SDK按照各自逻辑分工协作完成：  
  1. 从APP数据交互的角度来看整个过程，第三方APP和IMI APP是一种直接数据交互的过程（背后通过Mapping Server和IMI系统的服务支撑）
  <img src="https://github.com/imiapp/imi-sdk/blob/master/app_interactive_represent.png" width="660" />  
  2. 从服务器数据交互的角度来看整个过程，第三方服务器和IMI服务器之间的数据交互通过Mapping Server来完成
  <img src="https://github.com/imiapp/imi-sdk/blob/master/setup_channel.png" width="660" />  

##### 

  IMI SDK主要分为客户端和服务器端二大类：

- 客户端IMI SDK

  客户端的SDK由三个独立的部分构成：

  - Android SDK
  - iOS SDK
  - 浏览器JavaScript SDK

- 服务器端IMI SDK

  服务器端的SDK由二个独立的部分构成：

  - Java SDK
  - PHP SDK



第三方应用平台在对接IMI系统时，使用服务器端IMI SDK和客户端IMI SDK来完成自己的业务逻辑对接功能：

- 获取IMI用户的基本信息

- 获取IMI用户的实名认证身份证信息



## 2 文档介绍

  这里有一个IMI系统和SDK总体介绍文档，需要大家在使用和集成IMI系统功能之前，先阅读一下此文档，以便能全面了解IMI系统和SDK的总体设计思想：

  《IMI系统第三方平台使用说明文档》



**注意：**

1. IMI系统针对第三方平台现在只开放了身份认证平台的实名认证用户的信息获取/使用功能，更多的开放能力还在整理之中
2. 《IMI系统第三方平台使用说明文档》中的伪代码描述和SDK说明性内容，是为了让本文的阅读对象能理解整体设计思想，具体SDK使用方法和定义，请以SDK目录下的文档说明为准



## 3 SDK介绍

  为了便于大家阅读内容，我们把SDK按照编译和集成的特点进行了分类：

1. Server-SDK

   这里主要包括了以下三个不同的SDK开发工具包：

   - Java SDK

     服务器端的Java SDK开发工具包

   - PHP SDK

     服务器端的PHP SDK开发工具包

   - JavaScript SDK
   
     浏览器端（网站页面版）的JavaScript SDK开发工具包
 
2. Android-SDK

   Android系统的SDK开发工具包

3. iOS-SDK

   iOS系统的SDK开发工具包
   
4. HTML5-SDK
  
   HTML5页面（包括微信公众号）的SDK开发工具包
