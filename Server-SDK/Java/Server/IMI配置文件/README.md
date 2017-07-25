# server-sdk-java configuration

> 本目录为java版server-sdk配置文件目录：

## 一、目录描述：
- “*[/META-INF/imi/imi-config.properties](META-INF/imi/imi-config.properties)*” 为server-sdk基础信息配置文件；
- ”*[/META-INF/imi/imi-ks](META-INF/imi/imi-ks)*“为server-sdk 私钥KeyStore文件；

## 二、默认配置存放路径：
- 基础配置文件路径：classpath:/META-INF/imi/imi-config.properties
- keystore文件路径：classpath:/META-INF/imi/imi-ks
- 配置文件也可放置于系统文件目录下，具体配置文件存放方式与读取方式请参考文档《*[数字身份Server-SDK接口设计x.x.x.pdf](../)*》

## 二、配置说明：
> 基础配置文件imi-config.properties中的数据说明：
- MappingServer服务器接口地址切换：
  `地址中域名为watchtower.service.azurenet.cn注释掉的是生产环境的MappingServer服务器接口地址；`
  `地址中域名为test.service.azurenet.cn的是测试环境的MappingServer服务器接口地址；`
  `用户根据开发需求自行进行注释切换。`

- 基本信息中imi.name=DefaultName需要用户自行填写对接应用程序简称。