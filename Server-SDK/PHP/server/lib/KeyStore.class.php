<?php
date_default_timezone_set('PRC'); 
class KeyStore{
	const N_STANDARD = 262144;//1 << 18
	const P_STANDARD = 1;
	const R = 8;
	const DKLEN = 32;
	
	/**
	 * 主函数，加密
	 * @param String $password
	 * @param String $privateKey
	 * @param String $fileDir
	 * @throws \Exception
	 * @return Credentials
	 */
	public static function createEncryptFile($password, $privateKey, $fileDir){
		$ecKeyPair = self::createEcKeyPair($privateKey);
		$fileName = self::generateWalletFile($password, $ecKeyPair, $fileDir);
		return $fileName;
	}
	/**
	 * 主函数，解密
	 * @param String $password
	 * @param String $file
	 * @throws \Exception
	 * @return Credentials
	 */
	public static function decrypt($password,$file){
		if($password ==''){ echo 'Invalid Password Provided!';die();}
		self::validate($file);
		$data = self::readFile($file);
		$mac = isset($data['crypto']['mac']) ? $data['crypto']['mac'] : '';
		$iv = isset($data['crypto']['cipherparams']['iv']) ? $data['crypto']['cipherparams']['iv'] : '';
		$ciphertext = isset($data['crypto']['ciphertext']) ? $data['crypto']['ciphertext'] : '';
		$kdfparams = isset($data['crypto']['kdfparams']) ? $data['crypto']['kdfparams'] : '';
		$dklen = isset($kdfparams['dklen']) ? $kdfparams['dklen'] : '';
		$n = isset($kdfparams['n']) ? $kdfparams['n'] : '';
		$p = isset($kdfparams['p']) ? $kdfparams['p'] : '';
		$r = isset($kdfparams['r']) ? $kdfparams['r'] : '';
		$salt = isset($kdfparams['salt']) ? $kdfparams['salt'] : '';
		$derivedKey = self::generateDerivedScryptKey($password, $salt, $n, $r, $p, $dklen);
		$derivedMac = self::generateMac($derivedKey, $ciphertext);

		if($derivedMac != $mac){ echo 'Invalid Password Provided!';die();}
		
		$encryptKey = substr($derivedKey,0,strlen($derivedKey)/2);
		$privateKey = self::ctr_crypt($ciphertext, $encryptKey,  $iv);
		$pubkey = self::generatePubKey($privateKey);
		$Credentials = array();
		$Credentials['address'] = $pubkey;
		$Credentials['ecKeyPair']['publicKey'] = $pubkey;
		$Credentials['ecKeyPair']['privateKey'] = $privateKey;
		return $Credentials;
	}
	public static function generatePubKey($privateKey){
		$context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
		$privateKey = pack("H*", $privateKey);
		$publicKey = '';
		$result = secp256k1_ec_pubkey_create($context, $publicKey, $privateKey);
		$serialized = '';
		if ($result === 1) {
			$compress = false;
	
			if (1 !== secp256k1_ec_pubkey_serialize($context, $serialized, $publicKey, $compress)) {
				echo 'secp256k1_ec_pubkey_serialize: failed to serialize public key';die();
			}
		} else {
			echo 'secp256k1_pubkey_create: secret key was invalid';die();
		}
		$input = substr(bin2hex($serialized), 2, 128);
		$pubkeyNative = substr(sha3(hex2bin($input), 256), 24, 40);
		secp256k1_context_destroy($context);
		return $pubkeyNative;
	}
	public static function validate($file){
		$data = self::readFile($file);
		
		$cipher = isset($data['crypto']['cipher']) ? $data['crypto']['cipher'] : '';
		$version = isset($data['version']) ? $data['version'] : '';
		$kdf = isset($data['crypto']['kdf']) ? $data['crypto']['kdf'] : '';
		
		if ($version != 3){ echo 'Wallet version is not supported!';die();}
		if ($cipher != 'aes-128-ctr'){echo 'Wallet cipher is not supported!';die();}
		if ($kdf != 'pbkdf2' && $kdf != 'scrypt'){ echo 'KDF type is not supported!';die();}
	}
	
	public static function ctr_crypt($str, $key, $iv) {
        $p = self::mcrypt_decrypt(hex2bin($str), hex2bin($iv), hex2bin($key));
        return bin2hex($p);
	}

	public static function generateMac($derivedKey, $ciphertext){
		$result = substr($derivedKey,strlen($derivedKey)/2,strlen($derivedKey)).$ciphertext;
		$result = sha3(hex2bin($result),256);
		return $result;
	}
	public static function generateDerivedScryptKey($password, $salt, $n, $r, $p, $dklen){
		return scrypt($password, hex2bin($salt), $n, $r, $p, $dklen);
	}
	public static function readFile($file){
		$fileData = json_decode(file_get_contents($file),true); 
		if($fileData){
			return $fileData;
		}else{
			echo 'readFile: the file is error!';die();
		}
	}
	public static function mcrypt_encrypt($input, $iv, $key) {
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', "ctr", '');
		mcrypt_generic_init($td, $key, $iv);
		$data = mcrypt_generic($td, $input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return $data;
	}
	
	public static function mcrypt_decrypt($sStr, $iv, $sKey) {
		$decrypted= mcrypt_decrypt(
				MCRYPT_RIJNDAEL_128,
				$sKey,
				$sStr,
				"ctr",
				$iv
		);
		return $decrypted;
	}
	public static function createEcKeyPair($privateKey){
		$publicKey  = self::generatePubKey($privateKey);
		return array('publicKey'=>$publicKey,'privateKey'=>$privateKey);
	}
	public static function generateWalletFile($password, $ecKey, $fileDir){
		$salt = bin2hex(Password::generateSalt(24));
		$derivedKey = self::generateDerivedScryptKey($password, $salt, self::N_STANDARD, self::R, self::P_STANDARD, self::DKLEN);
		$encryptKey = substr($derivedKey,0,strlen($derivedKey)/2);
		$iv = bin2hex(Password::generateSalt(12));
		$cipherText = bin2hex(self::mcrypt_encrypt(hex2bin($ecKey['privateKey']), hex2bin($iv), hex2bin($encryptKey)));
		$mac = self::generateMac($derivedKey, $cipherText);

		$walletFIle = array();
		$walletFIle['address']=$ecKey['publicKey'];
		$walletFIle['crypto']['cipher']='aes-128-ctr';
		$walletFIle['crypto']['ciphertext']=$cipherText;
		$walletFIle['crypto']['cipherparams']=array('iv'=>$iv);
		$walletFIle['crypto']['kdf']='scrypt';
		$walletFIle['crypto']['kdfparams']=array('dklen'=>self::DKLEN,'n'=>self::N_STANDARD,'p'=>self::P_STANDARD,'r'=>self::R,'salt'=>$salt);
		$walletFIle['crypto']['mac']=$mac;
		$walletFIle['id']=self::getUUID();
		$walletFIle['version']=3;
		if(substr($fileDir,strlen($fileDir)-1,strlen($fileDir))=='/'){
			$fileName = $fileDir.'UTC--'.date('c',time()).'.json';
		}else{
			$fileName = $fileDir.'/UTC--'.date('c',time()).'.json';
		}
		$file = fopen($fileName, "w");
		fwrite($file, json_encode($walletFIle));
		fclose($file);
		return $fileName;
	}
	public static function getUUID(){
		$chars = md5(uniqid(mt_rand(), true));
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);
		return $uuid;
	}
}

class Password
{
    /**
     *
     * @var int The key length
     */
    private static $_keyLength = 32;
    /**
     * Get the byte-length of the given string
     *
     * @param string $str Input string
     *
     * @return int
     */
    protected static function strlen( $str ) {
        static $isShadowed = null;
        if ($isShadowed === null) {
            $isShadowed = extension_loaded('mbstring') &&
                ini_get('mbstring.func_overload') & 2;
        }
        if ($isShadowed) {
            return mb_strlen($str, '8bit');
        } else {
            return strlen($str);
        }
    }
    /**
     * Generates a random salt
     *
     * @param int $length The length of the salt
     *
     * @return string The salt
     */
    public static function generateSalt($length = 8)
    {
        $buffer = '';
        $buffer_valid = false;
        if (function_exists('random_bytes')) {
            try {
                $buffer = random_bytes($length);
                $buffer_valid = true;
            } catch (Exception $ignored) { }
        }
        if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }
        if (!$buffer_valid && is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = static::strlen($buffer);
            while ($read < $length) {
                $buffer .= fread($f, $length - $read);
                $read = static::strlen($buffer);
            }
            fclose($f);
            if ($read >= $length) {
                $buffer_valid = true;
            }
        }
        if (!$buffer_valid) {
            throw new Exception("No suitable random number generator available");
        }
        $salt = str_replace(array('+', '$'), array('.', ''), base64_encode($buffer));
        return $salt;
    }
    /**
     * Create a password hash
     *
     * @param string $password The clear text password
     * @param string $salt     The salt to use, or null to generate a random one
     * @param int    $N        The CPU difficultly (must be a power of 2, > 1)
     * @param int    $r        The memory difficultly
     * @param int    $p        The parallel difficultly
     *
     * @return string The hashed password
     */
    public static function hash($password, $salt = false, $N = 16384, $r = 8, $p = 1)
    {
        if ($N == 0 || ($N & ($N - 1)) != 0) {
            throw new \InvalidArgumentException("N must be > 0 and a power of 2");
        }
        if ($N > PHP_INT_MAX / 128 / $r) {
            throw new \InvalidArgumentException("Parameter N is too large");
        }
        if ($r > PHP_INT_MAX / 128 / $p) {
            throw new \InvalidArgumentException("Parameter r is too large");
        }
        if ($salt === false) {
            $salt = self::generateSalt();
        } else {
            // Remove dollar signs from the salt, as we use that as a separator.
            $salt = str_replace(array('+', '$'), array('.', ''), base64_encode($salt));
        }
        $hash = scrypt($password, $salt, $N, $r, $p, self::$_keyLength);
        return $N . '$' . $r . '$' . $p . '$' . $salt . '$' . $hash;
    }
    /**
     * Check a clear text password against a hash
     *
     * @param string $password The clear text password
     * @param string $hash     The hashed password
     *
     * @return boolean If the clear text matches
     */
    public static function check($password, $hash)
    {
        // Is there actually a hash?
        if (!$hash) {
            return false;
        }
        list ($N, $r, $p, $salt, $hash) = explode('$', $hash);
        // No empty fields?
        if (empty($N) or empty($r) or empty($p) or empty($salt) or empty($hash)) {
            return false;
        }
        // Are numeric values numeric?
        if (!is_numeric($N) or !is_numeric($r) or !is_numeric($p)) {
            return false;
        }
        $calculated = scrypt($password, $salt, $N, $r, $p, self::$_keyLength);
        // Use compareStrings to avoid timeing attacks
        return self::compareStrings($hash, $calculated);
    }
    /**
     * Zend Framework (http://framework.zend.com/)
     *
     * @link      http://github.com/zendframework/zf2 for the canonical source repository
     * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     *
     * Compare two strings to avoid timing attacks
     *
     * C function memcmp() internally used by PHP, exits as soon as a difference
     * is found in the two buffers. That makes possible of leaking
     * timing information useful to an attacker attempting to iteratively guess
     * the unknown string (e.g. password).
     *
     * @param string $expected
     * @param string $actual
     *
     * @return boolean If the two strings match.
     */
    public static function compareStrings($expected, $actual)
    {
        $expected    = (string) $expected;
        $actual      = (string) $actual;
        $lenExpected = static::strlen($expected);
        $lenActual   = static::strlen($actual);
        $len         = min($lenExpected, $lenActual);
        $result = 0;
        for ($i = 0; $i < $len; $i ++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        $result |= $lenExpected ^ $lenActual;
        return ($result === 0);
    }
}
