<?php
/********************************************************************* 
// 파 일 명		: member_join_facebook_access.php 
// 설     명		: 회원가입 페이스북 정보로 회원가입
// 상세설명	: 회원가입시 페이스북의 정보확인후 회원가입 및 수정.
// 작 성 자		: 2015.10.28 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
	session_start();

#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}

	// library 로드, 변수 설정 등
	require_once($Dir."plugin/sns/facebook/src/facebookoauth.php");
	//$consumer_key = $config['cf_facebook_appid'];
	//$consumer_secret = $config['cf_facebook_secret'];
	$consumer_key = "820599761372048";
	$consumer_secret = "f0b7f0eb3f59a1516838f1115b48cf5b";

	// FacebookOAuth object 생성
	$connection = new FacebookOAuth($consumer_key, $consumer_secret);

	// 토큰 수령
	$access_token = $connection->getAccessToken($_REQUEST['code']);

	$token = $access_token['oauth_token'];
	//$token = $access_token['access_token'];

	// Access token 을 포함한 TwitterOAuth object 생성
	$connection = new FacebookOAuth($consumer_key, $consumer_secret, $token);

	// get user profile
	 $parameters['fields'] = "id,name,email,birthday,link";

	$user = $connection->get('me', $parameters);
	//print_r($user);

	 if($user -> email && $user -> name){		 
		$facebook_id			= $user -> id;
		$facebook_name		= $user -> name;
		$facebook_email		= $user -> email;
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		echo '<script> ';
		echo "opener.document.form11.facebook_id.value='{$facebook_id}';";
		echo "opener.document.form11.facebook_name.value='{$facebook_name}';";
		echo "opener.document.form11.facebook_email.value='{$facebook_email}';";
		echo "opener.document.form11.facebook_token.value='{$token}';";
		echo "opener.document.form11.submit();";
		echo "window.close();";
		echo '</script>';

		exit;

	}else{
		//alert("페이스북 사용권한을 확인 해주셔야 이용가능합니다.","/");
		//echo (iconv("UTF-8", "euc-kr", "페이스북 사용권한을 확인 해주셔야 이용가능합니다."));
		$onload="페이스북 사용권한을 확인 해주셔야 이용가능합니다.";
		if(ord($onload)) {
			alert_close($onload);
		}
	}


?>