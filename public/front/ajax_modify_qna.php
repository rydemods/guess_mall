<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");

$num            = $_POST['num']; //게시판 번호
$up_subject     = $_POST['up_subject']; // 제목
$up_content     = $_POST['up_content']; // 내용
$up_sms_send    = $_POST['up_sms_send']; // 1 - 비밀글 / 0 - 일반글
$up_hp          = $_POST['up_hp']; // 휴대폰 번호
$up_email_send  = $_POST['up_email_send']; //이메일 전송 체크
$up_email       = $_POST['up_email']; //이름
$up_is_secret   = $_POST['up_is_secret']; //비밀 체크
$up_passwd      = $_POST['up_passwd']; //비밀번호
$up_sms_send     = $_POST['up_sms_send']; //휴대폰 번호 전송 체크

$sql = "UPDATE tblboard SET ";
$sql.= "title = '".$up_subject."', ";
$sql.= "content = '".pmysql_escape_string( $up_content )."', ";
$sql.= "passwd = '".$up_passwd."', ";
$sql.= "is_secret = '".$up_is_secret."', ";
$sql.= "sms_send = '".$up_sms_send."', ";
$sql.= "hp = '".$up_hp."', ";
$sql.= "email_send = '".$up_email_send."', ";
$sql.= "email = '".$up_email."' ";
$sql.= "WHERE num = '".$num."' ";

echo $sql;

$result = pmysql_query( $sql );

if( empty($result) ){
	echo 'false';
} else {
	echo 'true';
}

?>

