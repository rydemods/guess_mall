<?php
/********************************************************************* 
// 파 일 명		: iddup.proc.php 
// 설     명		: 아이디, 닉네임, 이메일 체크
// 상세설명	: 아이디, 닉네임, 이메일 유무를 체크함
// 작 성 자		: hspark
// 수 정 자		: 2015.10.29 - 김재수
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

$access_type	= $_GET['access_type'];
/*
if ($access_type == 'mobile') {
	$mem_id= $_GET['mem_id'];
} else {
	$mem_id=$_ShopInfo->getMemid();
}
*/

$cpCode= $_GET['cpCode'];


if($_GET[mode] == 'cpCode'){
	$cpCode=$_GET["cpCode"];
	$code="0";
	$sql = "SELECT group_code FROM tblcompanygroup WHERE group_code='{$cpCode}'";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$message="올바른 제휴 코드 입니다..";
		$code=1;
	} else {
		$message="올바른 제휴 코드가 아닙니다.";
	}
	pmysql_free_result($result);
}

$message	= $_GET[mode]=='emp_chk'?$message:$message;
$resultData = array("msg"=>$message, "code"=>$code);
echo urldecode(json_encode($resultData));
?>