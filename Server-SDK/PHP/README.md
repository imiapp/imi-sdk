PHP版本当前支持php5，php7

## 1 SDK介绍

- keyStore SDK  
实现从keystore文件里面读取私钥、公钥、以太坊地址  

- sign SDK  
实现通过公私钥对数据进行签名和验签  

- server SDK  
IMI功能集合的SDK，集成了以上1和2 
 
## 2 开发准备：

- 需要在php服务器端安装php-scrypt，secp256k1-php，sha3扩展，需打开PHP自带扩展mcrypt。 

- php-scrypt：https://github.com/DomBlack/php-scrypt 

- secp256k1-php：https://github.com/beyonderyue/secp256k1-php（注意PHP5请用v0.0分支，PHP7请用v0.1分支） 

- sha3：https://github.com/beyonderyue/php-sha3 

完整的demo功能请参考  
https://github.com/imiapp/server-demo/tree/master/PHP
