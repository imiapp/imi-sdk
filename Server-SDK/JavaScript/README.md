# JavaScript

> 本目录为页面版JavaScript demo程序目录：

## 一、目录描述：

- *vport.js* 为页面版的JavaScript demo程序；
- 《*vPort数字身份第三方获取身份信息文档.pdf*》为demo业务设计说明文档；

## 二、开发环境：

- JQuery

## 三、Demo

  https://github.com/imiapp/imi-sdk/blob/master/Server-SDK/JavaScript/vport.js

## 四、JavaScript实现的二维码显示的兼容性问题

- 兼容性更好的实现方式，可以参考 https://github.com/davidshimjs/qrcodejs

## 五、解决在IE浏览器下报错：SCRIPT5009: “JSON”未定义

- 在代码中引入json2.js文件  
 ```xml
    if (typeof JSON == 'undefined') {  
        $('head').append($("<script type='text/javascript' src='"+ context.value + "/js/common/json2.js'>"));  
    }
```
- json2.js文件下载地址  
https://github.com/douglascrockford/JSON-js
