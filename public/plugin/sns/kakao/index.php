<?php 
	$Dir="../../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."conf/config.sns.php");


	$CLIENT_ID     = $snsKtConfig["restKey"]; 
	$REDIRECT_URI  = "http://test2-sejung.ajashop.co.kr/plugin/sns/sns_proc.php"; 
	#$TOKEN_API_URL = "https://kauth.kakao.com/oauth/token";
	$TOKEN_API_URL = "https://kauth.kakao.com/oauth/authorize?client_id=22907c3c1f621435fa3f64992afe80ce&redirect_uri=http://test2-sejung.ajashop.co.kr/plugin/sns/sns_proc.php&response_type=code";

	$code   = 'a';


	#$params = sprintf( 'grant_type=authorization_code&client_id=%s&redirect_uri=%s&code=%s', $CLIENT_ID, $REDIRECT_URI, $code);
	$params = sprintf( 'client_id=%s&redirect_uri=%s&response_type=code', $CLIENT_ID, $REDIRECT_URI);
	$opts = array(
		CURLOPT_URL => $TOKEN_API_URL,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSLVERSION => 1,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $params,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => false
	);

	debug($opts);

	$curlSession = curl_init();
	curl_setopt_array($curlSession, $opts);
	$accessTokenJson = curl_exec($curlSession);
	curl_close($curlSession);

	echo $accessTokenJson;
?>