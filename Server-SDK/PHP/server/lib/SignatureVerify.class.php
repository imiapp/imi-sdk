<?php

class SignatureVerify{
	function __construct(){
		
	}
	
	/**
	 * 生成签名数据
	 * @param String $msg 要签名的数据
	 * @param String $privateKey 私钥
	 * @return String $signatureNative 签名后的数据
	 */
	public static function createSign($msg, $privateKey){
		$signature  = '';
		$recId = 0;
		$output = '';
		
		$context = self::getContext();
		$msg32 = sha3($msg, 256);
		$privateKey = pack("H*", $privateKey);
		$msgByte = hex2bin($msg32);
		if (secp256k1_ecdsa_sign_recoverable($context, $signature, $msgByte, $privateKey) != 1) {
			echo "Failed to create recoverable signature";die();
		}
		
		secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $output, $recId);
		$signatureNative = bin2hex($output).dechex($recId + 27);
		return $signatureNative; 
	}
	
	/**
	 * 验证签名数据是否正确
	 * @param String $msg
	 * @param String $publicKey
	 * @param String $sigFromServer
	 * @return boolean
	 */
	public static function verifySign($msg, $publicKey, $signFromServer){
		$signature = '';
		$pubKey = '';
		$serialized = '';
		$compress = false;

		$context = self::getContext();
		$msg32 = sha3($msg, 256);
		$msgByte = hex2bin($msg32);
		$recId = hexdec(substr($signFromServer, 128, 2)) - 27;
		$siginput = hex2bin(substr($signFromServer, 0, 128));
		
		secp256k1_ecdsa_recoverable_signature_parse_compact($context, $signature, $siginput, $recId);
		secp256k1_ecdsa_recover($context, $pubKey, $signature, $msgByte);
		secp256k1_ec_pubkey_serialize($context, $serialized, $pubKey, $compress);
		
		$A = bin2hex($serialized);

		$B = substr($A, 2, 128);
		$pubkeyH = sha3(hex2bin($B), 256);
		$pubkeyNative = '0x'.substr($pubkeyH, 24, 40);
		if (strcmp($publicKey, $pubkeyNative) == 0) {
			return true;
		} 
		return false;
	}
	public static function getContext(){
		return secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
	} 
}
