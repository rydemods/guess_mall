<?php 
session_start();
/**
* 
* 아이디 및 비밀번호 찾기 체크 페이지
* 
*/

Header("Content-type: text/html; charset=utf-8");



$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

exit;

$u_name	= $_REQUEST['name'];
$u_id			= $_REQUEST['id'];
$u_mobile	= str_replace("-","",$_REQUEST['mobile']);
$u_change_password	= $_REQUEST['change_password'];
		

	

$qry_where	= "";

if ($u_name) $qry_where .= "AND name = '{$u_name}' ";
if ($u_id) $qry_where .= "AND id = '{$u_id}' ";
if ($u_mobile) $qry_where .= "AND replace(mobile,'-','') = '{$u_mobile}' ";

list($mem_id, $mem_name, $mem_mobile, $mem_date)=pmysql_fetch("select id, name, mobile, date from tblmember where 1=1 {$qry_where}");
if(!$mem_id){
	$message	= "일치하는 회원정보가 없습니다.";
} else {
	
	$message	= "비밀번호 변경이 완료되었습니다. ";							

	//$passwd=substr(rand(0,9999999),0,8);

	$shadata = "*".strtoupper(SHA1(unhex(SHA1($u_change_password))));

	//$shadata	= "*".strtoupper(SHA1(unhex(SHA1($u_ch_pwd))));					
	$sql = "UPDATE tblmember SET passwd='".$shadata."' ";
	$sql.= "WHERE 1=1 {$qry_where}";

	pmysql_query($sql,get_db_conn());

	
	$lms_sql = " select id,authkey,return_tel from tblsmsinfo limit 1 ";
	$lms_result = pmysql_query($lms_sql);
	$lms_info = pmysql_fetch_object($lms_result);

//	$sms_msg = $u_name." 님 핫티 비밀번호 변경이 완료 되었습니다.";
//	$return_msg = SendSMS($lms_info->id, $lms_info->authkey, $mem_mobile, '', $lms_info->return_tel, 0, $sms_msg, '');

	
}
			
$resultData = array("msg"=>urlencode($message), "code"=>$code);
echo urldecode(json_encode($resultData));
?>
