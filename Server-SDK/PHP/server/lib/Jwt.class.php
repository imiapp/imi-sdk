<?php
date_default_timezone_set('PRC');
require_once 'KeyStore.class.php';
require_once 'SignatureVerify.class.php';

class Jwt{
	public $imiConfigPath = '';
	public $imiKsPath = '';
	public $topicId = '';
	public $getTopicId= '';
	public $pullData = '';
	public $openid = '';
	public $vportId = '';
	public $name = '';
	public $pass = '';
	public $baseJwt = '';
	public $version = "2.0";
	public $keyStore = '';
	public $scope = '';
	public $tmpVportId = '';
	
	public $dataInfo = array('authorizer'=>array('vportId'=>'','name'=>''),'identityCardInfo'=>'','loginUserInfo'=>'');
	
	public function __construct($imiConfigPath,$imiKsPath){
		$this->imiConfigPath = $imiConfigPath;
		$this->imiKsPath = $imiKsPath;
		$this->readConfigParam();
		$this->readKeyStore();
	}

	public function createChannel($scope, $version = '2.0'){
		$this->version = $version;
		$this->scope = $scope;
		$params = array("scope"=>$this->scope,"version"=>$this->version);
		$idAddr = $this->getIdAddr();
		$uuid = $this->getUUID();
		$sequenceId = $this->getSequenceId();
		$req = array("openId"=>$this->openid,"uuid"=>$uuid,"sequenceId"=>$sequenceId);
		
		$paramsJsonStr = $this->bean2JSONStrWithJWT($req);
		$resBody = $this->curl_post($this->getTopicId, $paramsJsonStr);
		$res = json_decode($resBody,true);
		if (null == $res) {
			echo "IMI server call exception: response is empty";die();
		}
		if ($res['retCode']!='0000000') {
			echo "IMI server call exception: [retCode=".$res['retCode'].", retInfo=" . $res['retInfo'] . "]";die();
		}
		if (null == $res['result']) {
			echo "IMI server call exception: response result is empty";die();
		}
		$info = '';
		$info['topicId'] = $res['result']['topicId'];
		$info['openid'] = $this->openid;
		$info['name'] = $this->name;
		$info['version'] = $this->version;
		if(count($params['scope'])>1){
			$info['scope'] = $params['scope'][0].','.$params['scope'][1];
		}else{
			$info['scope'] = $params['scope'][0];
		}
		//$info['scope'] = $params['scope'][0].','.$params['scope'][1];
		return $info;
	}
	public function getAuthorizationInfo($topicId,$scope){
		$this->topicId = $topicId;
		$this->scope = $scope;
		$params = array("scope"=>$this->scope,"topicId"=>$this->topicId);
		$idAddr = $this->getIdAddr();
		$uuid = $this->getUUID();
		$sequenceId = $this->getSequenceId();
		$req = array("openId"=>$this->openid,"uuid"=>$uuid,"sequenceId"=>$sequenceId,"topicId"=>$this->topicId);
		$paramsJsonStr = $this->bean2JSONStrWithJWT($req);
		$resBody = $this->curl_post($this->pullData, $paramsJsonStr);
		$res = json_decode($resBody,true);
		if (null == $res) {
			echo "IMI server call exception: response is empty";die();
		}
		if ($res['retCode']!='0000000') {
			echo "IMI server call exception: [retCode=".$res['retCode'].", retInfo=" . $res['retInfo'] . "]";die();
		}
		if (null == $res['result']) {
			echo "IMI server call exception: response result is empty";die();
		}
		if (null == $res['result']['data'] || empty($res['result']['data'])) {
			echo "IMI server call exception: response result is empty";die();
		}
		//解析JWT数据
		$data = $res['result']['data'];
		$info = $this->getItem($data);
		return $info;
	}
	public function getItem($data){
		$data = json_decode($data,true);
		$dataInfo = $this->dataInfo;
		foreach($this->scope as $sc){
			if(array_key_exists($sc,$data)){
				if($sc == 'snsapi_idcard'){
					$dataInfo['identityCardInfo'] = $this->parseJWT($sc,$data[$sc]);
					$this->dataInfo = $dataInfo;
				}elseif($sc == 'snsapi_info'){
					$dataInfo['loginUserInfo'] = $this->parseJWT($sc,$data[$sc]);
					$this->dataInfo = $dataInfo;
				}
			}
		}
		$dataInfo['authorizer']['name'] = $this->name;
		$dataInfo['authorizer']['vportId'] = $this->tmpVportId;
		$this->dataInfo = $dataInfo;
		return $dataInfo;
	}
	public function parseJWT($sc,$jwt){
		$data = '';
		if($sc == 'snsapi_idcard'){
			$jwtArr = explode('.', $jwt);
			$tmpInfo = base64_decode($jwtArr[1]);
			$tmpInfoArr = json_decode($tmpInfo,true);
			$payLoad = $jwtArr[0].'.'.$jwtArr[1];
			$tmpPublicKey = $tmpInfoArr['issuer']['publicKey'];
			$tmpSign = bin2hex(base64_decode($jwtArr[2]));
			$verifySign = SignatureVerify::verifySign($payLoad , $tmpPublicKey, $tmpSign);
			if($verifySign){
				$data = $tmpInfoArr;
				$newJwt = $tmpInfoArr['jwt'];
				$newJwtArr = explode('.', $newJwt);
				$newJwtInfo = base64_decode($newJwtArr[1]);
				$newJwtInfoArr = json_decode($newJwtInfo,true);
				$newPayLoad = $newJwtArr[0].'.'.$newJwtArr[1];
				$newTmpPublicKey = $newJwtInfoArr['issuer']['publicKey'];
				$tmpOldSign = bin2hex(base64_decode($newJwtArr[2]));
				$newVerifySign = SignatureVerify::verifySign($newPayLoad, $newTmpPublicKey, $tmpOldSign);
				if($newVerifySign){
					$data['jwt'] = $newJwtInfoArr;
				}else{
					echo "IMI server call exception: data sign is error";die();
				}
			}else{
				echo "IMI server call exception: data sign is error";die();
			}
		}else{
			$jwtArr = explode('.', $jwt);
			$tmpInfo = base64_decode($jwtArr[1]);
			$tmpInfoArr = json_decode($tmpInfo,true);
			$verifySign = SignatureVerify::verifySign($jwtArr[0].'.'.$jwtArr[1], $tmpInfoArr['issuer']['publicKey'], bin2hex(base64_decode($jwtArr[2])));
			if($verifySign){
				$data = $tmpInfoArr;		
			}
		}
		$data = $this->formatData($sc, $data);
		return $data;
	}
	public function formatData($sc, $data){
		$vdataInfo = $this->dataInfo;
		$newData = '';
		if($sc == 'snsapi_idcard'){
			$newData['cin'] = $data['jwt']['claim']['assertion']['cin'];
			$newData['name'] = $data['jwt']['claim']['assertion']['name'];
			$newData['sex'] = $data['jwt']['claim']['assertion']['sex'];
			$newData['authority']  = $data['jwt']['claim']['assertion']['authority'];
			$newData['dateBirth'] = $data['jwt']['claim']['assertion']['dob'];
			$newData['dateIssue'] = $data['jwt']['claim']['assertion']['doi'];
			$newData['dateExpiry'] = $data['jwt']['claim']['assertion']['doe'];
			$newData['image'] = array('type'=>$data['jwt']['claim']['assertion']['image']['type'],'data'=>$data['jwt']['claim']['assertion']['image']['data']);
			
			if($data['jwt']['issuer']['vportId']==''){
				echo "IMI server call exception: response vportId is error";die();
			}else{
				if($this->tmpVportId==''){
					$this->tmpVportId = $data['jwt']['issuer']['vportId'];
				}else{
					if($this->tmpVportId != $data['jwt']['issuer']['vportId']){
						echo "IMI server call exception: response vportId is error";die();
					}
				}
			}
		}elseif ($sc == 'snsapi_info'){
			$newData['userName'] = $data['claim']['assertion']['userName'];
			$newData['mobile'] = $data['claim']['assertion']['mobile'];
			$newData['email'] = isset($data['claim']['assertion']['email']) ? $data['claim']['assertion']['email'] : '';
			//$newData['image'] = array('type'=>$data['claim']['assertion']['image']['type'],'data'=>$data['claim']['assertion']['image']['data']);
			
			if($data['issuer']['vportId']==''){
				echo "IMI server call exception: response vportId is error";die();
			}else{
				if($this->tmpVportId==''){
					$this->tmpVportId = $data['issuer']['vportId'];
				}else{
					if($this->tmpVportId != $data['issuer']['vportId']){
						echo "IMI server call exception: response vportId is error";die();
					}
				}
			}
		}
		return $newData;
	}
	public function getIdAddr(){
		$ip = '';
		if (getenv("HTTP_CLIENT_IP")){
			$ip = getenv("HTTP_CLIENT_IP");
		}else if(getenv("HTTP_X_FORWARDED_FOR")){
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}else if(getenv("REMOTE_ADDR")){
			$ip = getenv("REMOTE_ADDR");
		}else{ 
			$ip = "Unknow";
		}		
		return str_replace('.','',$ip);
	}
	
	public function getUUID(){
		return time().uniqid();
	}
	public function getSequenceId(){
		return time().rand(1000,9999);
	}
	//初始化keystore
	public function readKeyStore(){
		if( !is_file($this->imiKsPath)){
			echo "iinitConfigPath: imiKsPath is empty";die();
		}
		$keyStore = KeyStore::decrypt($this->pass,$this->imiKsPath);
		$this->keyStore = $keyStore['ecKeyPair'];
	}
	//初始化配置文件
	public function readConfigParam(){
		if( !is_file($this->imiConfigPath)){
			echo "initConfigPath: imiConfigPath is empty";die();
		}
		$text = explode("<br />",nl2br(file_get_contents($this->imiConfigPath)));
		$a = '';
		foreach ($text as $item){
			if(trim($item)!=''&&strpos(trim($item), '#')===FALSE){
				$tmp = explode('=', trim($item));
				if($tmp[0]=='web.mapping.getTopicId'){
					$this->getTopicId = $tmp[1];
				}elseif ($tmp[0]=='web.mapping.pullData'){
					$this->pullData = $tmp[1];
				}elseif ($tmp[0]=='imi.mappingOpenid'){
					$this->openid = $tmp[1];
				}elseif ($tmp[0]=='imi.vportId'){
					$this->vportId = $tmp[1];
				}elseif ($tmp[0]=='imi.name'){
					$this->name = $tmp[1];
				}elseif ($tmp[0]=='imi.ks.pass'){
					$this->pass = $tmp[1];
				}
			}
		}
		$this->baseJwt = file_get_contents($this->imiKsPath);
	}
	public function bean2JSONStrWithJWT($bean){
		$keyStore = $this->keyStore;
		$publicKey = $keyStore['publicKey'];
		$header = base64_encode(json_encode(array("alg"=>"ES256K","typ"=>"JWT")));
		$issuer = array("name"=>$this->name,"hashed"=>false,"publicKey"=>"0x".$publicKey,"vportId"=>$this->vportId);
		$bean['issuer'] = $issuer;
		$bean['iat'] = self::getMillisecond();
		$bean['type'] = "ApiCommunicationParameter";
		$bean['@context'] = 'http://www.blockcerts.org/schema/1.2/context.json';
		$payload = str_replace("\\/", "/",  json_encode($bean));
		$payload = base64_encode($payload);
		$jwt = $header.'.'.$payload;
		$signature = base64_encode(hex2bin(SignatureVerify::createSign($jwt, $keyStore['privateKey'])));
		$jwt = $jwt.'.'.$signature;
		$data = json_encode(array("jwt"=>$jwt));
		return $data;
	}
	public static function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	public function curl_post($url,$post_data){
		$headers[] = 'Content-Type: application/json; charset=utf-8';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		$output=curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}
