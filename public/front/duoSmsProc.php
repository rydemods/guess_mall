<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
header("Content-Type: application/json;charset=EUC-KR");
$eprise_code = '';
//echo $eprise_code;
$mode = $_POST['mode']; 
$emp_id = $_POST['emp_id'];
$empoly_id = $_POST['empoly_id'];
$employ_mem_name = $_POST['employ_mem_name'];
$employ_name = $_POST['employ_name'];
$employ_orgnzt_domain = $_SERVER["HTTP_HOST"];
$employ_ip = $_SERVER["REMOTE_ADDR"];
$employ_pw = $_POST['employ_pw'];
$employ_email = $_POST['employ_email'];
$employ_tel = $_POST['employ_tel'];
$employ_hp = $_POST['employ_hp'];
$employ_licensee = $_POST['employ_licensee'];
$employ_fax = $_POST['employ_fax'];
$employ_zipcode = $_POST['employ_zipcode1'].'-'.$_POST['employ_zipcode1'];
$employ_business = $_POST['employ_business'];
$employ_event = $_POST['employ_event'];
$employ_owner_name = $_POST['employ_owner_name'];
$employ_addr1 = $_POST['employ_addr1'];
$employ_addr2 = $_POST['employ_addr2'];

$pay_count = $_POST['pay_count'];     
$pay_method = $_POST['pay_method'];
$user_name = $_POST['user_name'];
$itemprice = $_POST['itemprice'];
$itemname = $_POST['itemname'];
$shop_id = $_POST['shop_id']; 
$user_no = $_POST['user_no']; 
$tno = 0;
$auth_code = 0;
$result_code = '';
$pay_logs_code = '002003';

$sendId = $_POST['sendId'];
$contents = $_POST['contents'];
$m_send_phone = $_POST['m_send_phone'];
$totea = $_POST['totea'];

if ($mode == 'emp_add') { // sms시스템 가입
	//echo json_encode(duo_smsEmpolyAdd());
	$cmd_mode = '_empolyAdd';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();

	$data = array(
		"key" => $key,
		"cmd_mode" => $cmd_mode, 
		"hashdata" => md5($cmd_mode.$key),
		"employ_id" => $empoly_id,
		"employ_mem_name" => $employ_mem_name,
		"employ_name" => $employ_name,
		"employ_orgnzt_domain" => $employ_orgnzt_domain,
		"employ_ip" => $employ_ip,
		"employ_pw" => $employ_pw,
		"employ_email" => $employ_email,
		"employ_tel" => $employ_tel,
		"employ_hp" => $employ_hp,
		"employ_licensee" => $employ_licensee,
		"employ_fax" => $employ_fax,
		"employ_zipcode" => $employ_zipcode,
		"employ_business" => $employ_business,
		"employ_event" => $employ_event,
		"employ_owner_name" => $employ_owner_name,
		"employ_addr1" => $employ_addr1,
		"employ_addr2" => $employ_addr2
	);
	$resultData = duo_smsEmpolyAdd($data);
	if ($resultData['result'] == "true") {
		$query = "UPDATE tblsmsinfo SET id='".$empoly_id."', authkey='".$resultData['esntl_key']."'";
		pmysql_query($query); 
	}
	echo json_encode($resultData);
	exit;
} else if ($mode == 'emp_id_chk'){ // 가입시 아이디 체크
	echo json_encode(duo_smsEmpolyIdCheck($emp_id));
	exit;
} else if ($mode == 'sms_send') { // sms 발송 
	$query = "SELECT member_name, handphone FROM member WHERE member_code in (".$sendId.")";
	$res = pmysql_query($query);
	$send_object_count = 0;
	$handphone = "";
	$member_name = "";
	while($row = pmysql_fetch_object($res)){
		if ($send_object_count > 0) {
			$handphone .= "||";
			$member_name .= "||";
		}
		$handphone .= str_replace("-","",$row->handphone);
		$member_name .= $row->member_name;
		$send_object_count++;
	}
	$cmd_mode = 'sms_send';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();
	$data = array(
		"key" => $key,
		"esntl_key" => $esntl_key,
		"cmd_mode" => $cmd_mode, 
		"hashdata" => md5($cmd_mode.$key),
		"dest_phone" => $handphone, 
		"dest_name" => mb_convert_encoding($member_name,"utf-8","euc-kr"), 
		"send_phone" => $m_send_phone, 
		"msg_body"  => $contents,
		"send_object_count" => $send_object_count
	);
	
	$resultData = duo_sms_send($data);
	echo json_encode($resultData);
	exit;
} else if ($mode == 'pay_add') { // 결제
	$cmd_mode = 'setPayInfo';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();

	$data = array(
		"key" => $key,
		"esntl_key" => $esntl_key,
		"cmd_mode" => $cmd_mode, 
		"hashdata" => md5($cmd_mode.$key),
		"pay_count" => $pay_count,
		"pay_method" => $pay_method,
		"user_name" => $user_name,
		"itemprice" => $itemprice,
		"itemname" => $itemname,
		"shop_id" => $shop_id,
		"result_code" => $result_code,
		"pay_logs_code" => $pay_logs_code,
		"user_no" => $user_no,
		"tno" => $tno,
		"auth_code" => $auth_code
	);
	$resultData = duo_setPayInfo($data);
	echo json_encode($resultData);
	exit;
}
exit;
?>	