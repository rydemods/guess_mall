<?php
/********************************************************************* 
// 파 일 명		: member_join_facebook.php 
// 설     명		: 회원가입 페이스북 이동
// 상세설명	: 회원가입시 페이스북으로 이동
// 작 성 자		: 2015.10.28 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$access	= $_REQUEST['access'];
$rec_id		= $_REQUEST['rec_id'];		//추천인 아이디

$news_mail_yn		= $_REQUEST['news_mail_yn'];		//이메일 수신여부
$news_sms_yn		= $_REQUEST['news_sms_yn'];		//SMS 수신여부
//ECHO $access;
//exit;

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
	setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/'.RootPath , getCookieDomain());
}


// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
	$cookie = md5($cookie_name);
	if (array_key_exists($cookie, $_COOKIE))
		return base64_decode($_COOKIE[md5($cookie_name)]);
	else
		return "";
}

function goto_url_frame($url)
{
	$url = str_replace("&amp;", "&", $url);
	//echo "<script> location.replace('$url'); </script>";

	echo '<script>';
	echo 'parent.location.replace("'.$url.'");';
	echo '</script>';
	echo '<noscript>';
	echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
	echo '</noscript>';

}



set_cookie('ck_facebook_checked', false, 86400*31);
set_cookie('rec_id', $rec_id, 60*10);
set_cookie('news_mail_yn', $news_mail_yn, 60*10);
set_cookie('news_sms_yn', $news_sms_yn, 60*10);

//============================================================================
// 페이스북 로그인
//----------------------------------------------------------------------------

// 변수 설정 등
//$consumer_key = $config['cf_facebook_appid'];
	$consumer_key = "820599761372048";
	$domain = 'http://' . $_SERVER['HTTP_HOST'] . '/';

// 로그인
	if($access == "1"){
		// 파라미터
		$args = "scope=email,publish_actions"
				. "&client_id=" . $consumer_key
				. "&display=popup"
				. "&redirect_uri=" . $domain . 'front/login_facebook_access.php';
// 회원가입
	}else{

		// 파라미터
		$args = "scope=email,publish_actions"
				. "&client_id=" . $consumer_key
				. "&display=popup"
				. "&redirect_uri=" . $domain . 'front/member_join_facebook_access.php';

	}

	// 호출 uri
	$uri = "https://graph.facebook.com/oauth/authorize?" . $args;

	goto_url_frame($uri);
?>