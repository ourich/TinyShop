<?php
//基本参数
header('Content-type:text/html;charset=utf-8');
header("Access-Control-Allow-Origin: *");
date_default_timezone_set("Asia/chongqing");
//小桔配置
$Ju_appkey='';
$Ju_appSecret='';
$Ju_dataSecret='';
$Ju_sigSecret='';
$Ju_url='http://epower-openapi.xiaojukeji.com/energy/v1/';
$Ju_openChannel=1;
//获取小桔的平台密钥
function queryToken(){
	global $Ju_appSecret;
	$t_json="./json/queryToken.json";
	if(!file_exists($t_json)){
		file_put_contents($t_json,'');
	}

	$data = json_decode(file_get_contents($t_json));
	if (@$data->expire_time < time()) {
		$queryData=array('appSecret'=>$Ju_appSecret);
		$info=curl_xiaoJu('queryToken',$queryData);
		print_r($info);
		if($info['code']!=0){
			exit($info['msg']);
		}
		$ticket=$info['data']['accessToken'];
		$data->expire_time = time() + ($info['data']['availableTime']-200);
		$data->accessToken = $info['data']['accessToken'];
		$fp = fopen($t_json, "w");
		fwrite($fp, json_encode($data));
		fclose($fp);
	} else {
		$ticket = $data->accessToken;
	}

	return $ticket;
}
function curl_xiaoJu($url, $data = array()){
	global $Ju_url,$Ju_appkey,$Ju_dataSecret,$Ju_sigSecret;
	include_once "./AES.php";
	$data=AES::encrypt(json_encode($data),$Ju_dataSecret);
	$timeStamp=date('YmdHis');
	$addData = array(
			'appKey'=>$Ju_appkey,
			'data'=>$data,
			'timeStamp'=>$timeStamp
	);
	ksort($addData);
	$signStr=$Ju_appkey.$data.$timeStamp;
	$sign=strtoupper(hash_hmac('md5', $signStr, $Ju_sigSecret));
	$data=array_merge($addData,['sig'=>$sign]);
	$SSL = substr($Ju_url.$url, 0, 8) == "https://" ? true : false;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Ju_url.$url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 28);
	if ($SSL) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	if($url!='queryToken') {
		$headers[]  =  "Content-Type:application/json; charset=utf-8";
		$headers[]  =  "Authorization: Bearer ". queryToken();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}else{
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
	}
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	$ret = curl_exec($ch);
    $request_header = curl_getinfo( $ch, CURLINFO_HEADER_OUT);
    print_r($request_header);
	curl_close($ch);
//    print_r($ret);exit;
	$ret=json_decode($ret,true);
	if($ret['code']!=0){
		return array('code'=>$ret['code'],'msg'=>$ret['msg']);
	}

	$dataInfo=json_decode(AES::decrypt(json_encode($ret['data']),$Ju_dataSecret),true);
	return array('code'=>$ret['code'],'data'=>$dataInfo);
}