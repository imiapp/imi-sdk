# server-sdk-java

> 本目录为java版server-sdk包目录：

## 一、目录描述：

- *server-sdk-x.x.x-RELEASE-with-dependencies.jar* 为sdk程序包；
- 《*数字身份Server-SDK接口设计x.x.x.pdf*》为sdk 业务接口设计说明文档；
- *server-sdk-doc-x.x.x.zip*为sdk API文档包；
- “*IMI配置文件*”目录为sdk相关配置文件；
- 上述描述中出现的**x.x.x**为版本号。

## 二、开发环境：

- server-sdk-x.x.x-RELEASE-with-dependencies.jar是基于jdk1.7开发与编译，故引用该包的工程环境必须为*jdk1.7+*。

## 三、Demo

https://github.com/imiapp/server-demo/tree/master/java

## 四、版本日志

  | 版本号   | 版本描述                                     |
  | ----- | ---------------------------------------- |
  | 1.0.0 | 基础版本发布                                   |
  | 1.0.1 | 去掉获取授权信息接口`IMIAuthorizationRouter.getAuthorizationInfo()`的回调推送授权信息方式，改为直接`AuthorizationInfo`中返回 |
  | 1.0.2 | 创建通信通道接口`IMIAuthorizationRouter.createChannel()`的返回信息`ChannelInfo`中添加生成二维码的原始数据信息属性`qrData`，方便前端直接用于生成二维码 |
  | 1.0.3 | 优化配置文件读取处理;将默认配置文件路径“META-INF”移除;将sdk依赖的第三方jar分离出来 |
