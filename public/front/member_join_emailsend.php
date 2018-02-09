<?php
/********************************************************************* 
// 파 일 명		: member_join_emailsend.php
// 설     명		: 학교 이메일 인증을 통한 가입 메일 발송
// 상세설명	: 학교 이메일 인증을 통한 가입 메일 발송
// 작 성 자		: 2015.10.29 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보를 설정한다.
#---------------------------------------------------------------
	Header("Content-type: text/html; charset=utf-8");

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$message= "";
	$code= "";

	if ($_GET["email"]) { // 이메일 전체주소가 들어왔을 경우

		$email=$_GET["email"];	

		if(!ereg("(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$)", $email)) {
			$message="잘못된 이메일 형식입니다.";
		} else {
			
			if(!preg_match("/\.(ac\.kr)$/i", $mb_email)){
				$message="메일주소는 ac.kr 가 붙은 학교 공인 이메일주소로만 가능합니다. ";
			}

			if (!$message) {
				$sql = "SELECT email FROM tblmember WHERE email='{$email}' or mb_facebook_email = '{$email}' ";
				$result = pmysql_query($sql,get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					$message="{$email} 메일은 이미 존재하는 메일주소 입니다.\\n\\n다른 메일주소를 입력해 주십시오.";
				} else {
					SendJoinCertMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $id);
					$message="정상적으로 전송 되었습니다.\\n\\n잠시후 메일 내 링크를 통해 사이트 접속 후\\n\\n회원가입을 눌러주세요.";
					$code	= 1;
				}
				pmysql_free_result($result);
			}
		}
	} else { // 이메일이 나누어져 들어올 경우
		$email2_arr	= explode("|", $_GET["email2"]);
		$_email_code	= $email2_arr[0];
		$_email_addr	= $email2_arr[1];

		$email=$_GET["email1"]."@".$_email_addr;

		if(!ereg("(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$)", $email)) {
			$message="잘못된 이메일 형식입니다.";
		} else {
			
			/*if(!preg_match("/\.(ac\.kr)$/i", $mb_email)){
				$message="메일주소는 ac.kr 가 붙은 학교 공인 이메일주소로만 가능합니다. ";
			}*/

			if (!$message) {
				$sql = "SELECT email FROM tblmember WHERE email='{$email}' or mb_facebook_email = '{$email}' ";
				$result = pmysql_query($sql,get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					$message="{$email} 메일은 이미 존재하는 메일주소 입니다.\\n\\n다른 메일주소를 입력해 주십시오.";
				} else {
					SendJoinCertMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $id, $_email_code);
					$message="정상적으로 전송 되었습니다.\\n\\n잠시후 메일 내 링크를 통해 사이트 접속 후\\n\\n회원가입을 눌러주세요.";
					$code	= 1;
				}
				pmysql_free_result($result);
			}
		}
	}

	$resultData = array("msg"=>urlencode($message),"code"=>urlencode($code));
	echo urldecode(json_encode($resultData));
?>