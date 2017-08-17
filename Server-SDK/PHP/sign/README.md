本SDK实现的功能是通过公私钥对数据进行签名和验签。

开发准备：
1：需要在php服务器端安装php-scrypt，secp256k1-php，sha3扩展，需打开PHP自带扩展mcrypt。
2：php-scrypt：https://github.com/DomBlack/php-scrypt
3：secp256k1-php：https://github.com/beyonderyue/secp256k1-php（注意PHP5请用v0.0分支，PHP7请用v0.1分支）
4：sha3：https://github.com/beyonderyue/php-sha3

开发DEMO：
1：首先用到本SDK的PHP文件需要载入lib/SignatureVerify.class.php
        例：require_once 'lib/SignatureVerify.class.php';
2：调用需要的方法，目前本SDK对外使用的方法只有生成签名和验证签名。
        生成：SignatureVerify::createSign($msg, $privatKey);  //返回签名后的字符串：$sign,。
                  参数：$msg（需要签名的字符串数据），$privatKey（签名时要用到的私钥）
        验证：SignatureVerify::verifySign($msg, $publicKey, $signFromServer); //返回：true OR false;
                  参数：$msg（需要签名的字符串数据），$publicKey（验证签名时要用到的公钥），$signFromServer(要验证的数据)
