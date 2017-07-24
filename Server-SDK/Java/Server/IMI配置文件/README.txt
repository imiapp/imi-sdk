
一、配置文件存放路径：

	1、基础配置文件路径：
		classpath:/META-INF/imi/imi-config.properties
		
	2、keystore文件路径：
		classpath:/META-INF/imi/imi-ks
		
	具体配置文件存放方式与读取方式请参考文档《数字身份Server-SDK接口设计v1.0.0.pdf》
		
二、配置说明：

	基础配置文件imi-config.properties中的数据说明：
	
	1、MappingServer服务器接口地址切换：
		地址中域名为watchtower.service.azurenet.cn注释掉的是生产环境的MappingServer服务器接口地址；
		地址中域名为test.service.azurenet.cn的是测试环境的MappingServer服务器接口地址；
		用户根据开发需求自行进行注释切换。

	2、基本信息中imi.name=DefaultName需要用户自行填写对接应用程序简称。
