本SDK实现的功能是从keystore文件里面读取私钥、公钥、以太坊地址，和通过私钥、密码生成keystore文件。



开发准备：

	1：需要在php服务器端安装php-scrypt，secp256k1-php，sha3扩展，需打开PHP自带扩展mcrypt。

	2：php-scrypt：https://github.com/DomBlack/php-scrypt

	3：secp256k1-php：https://github.com/beyonderyue/secp256k1-php（注意PHP5请用v0.0分支，PHP7请用v0.1分支）

	4：sha3：https://github.com/beyonderyue/php-sha3



开发DEMO：
	
1：首先要用到读取或写入keystore的php文件需要加载到 KeyStore.class.php。
	例：require_once 'lib/KeyStore.class.php';

	2：调用需要的方法，目前本SDK对外使用的方法只有读取和写入方法。
			读取：KeyStore::decrypt($password, $fileName);  //返回数组包含：publicKey，privateKey，address。
	
		写入：KeyStore::createEncryptFile($password, $privateKey, $savePath); //返回字符串：fileName（文件保存路径+文件名）。
