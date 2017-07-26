### H5接入指南

## 1 环境配置

使用场景：移动端html5使用IMI APP进行登录或者授权操作。
#### 具体操作流程和步骤：

步骤一：html5网页调用第三方服务器获取topicId接口。

步骤二：获取topicId之后，打开IMI（方式：url schema）路径拼接如下：
将topicId拼接至url中,向IMI进行注册,如果是登录拼接参数如下：
区别：scope值用于区分登录或者授权。
<ahref="WFVportAllowLogin://vportAllowLogin/website=1&scope=snsapi_info&v=2&topicId?= com03d992d66953408c8b919d2b346cdd15&backUrl=www.baidu.com ">打开app</ a>

如果是授权身份证信息拼接参数如下：        
<ahref="WFVportAllowLogin://vportAllowLogin/website=1&scope=snsapi_idcard&v=2&topicId?= com03d992d66953408c8b919d2b346cdd15&backUrl=www.baidu.com ">打开app</ a>

注意点：拼接五个参数介绍：website,scope，v，topicId，backUrl。
website是网站启动app的标志，scope类型区分授权或者登陆，topicId是第三方平台的唯一标识，v是版本号，目前传固定值2，backUrl是第三方网站需要回调的网址，即IMI返回到第三方网站的url，期望url简洁一些。第三方网站的backUrl后面可以拼接topicId参数，作为回调的标识。

步骤三：网页启动IMI应用后，IMI应用会进行登录授权操作，授权成功，IMI根据backUrl回调到网页。此时，网页接收到IMI的回调，开始调用pull/data接口，获取授权数据，刷新网页即可。

## 2 SDK使用说明

补充中...

## 3 SDK API 

补充中...

## 4 Demo

补充中...







