<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/adminlib.php");
//include_once($Dir."lib/shopdata.php");
include("access.php");


//exdebug($_REQUEST);
//exdebug($_POST);
//exit;

$mode					= $_REQUEST['mode'];
$mem_type			= $_REQUEST['mem_type'];
$erp_member_id		= $_REQUEST['erp_member_id'];
$mem_join_type		= $_REQUEST['mem_join_type'];
$id						= $_REQUEST['id'];
$group_code			= $_REQUEST['group_code'];
$name					= $_REQUEST['name'];
$birth						= trim($_POST["birth1"]);														// 생년월일
$lunar					= $_REQUEST['lunar'];
$gender					= $_REQUEST["gender"];													// 성별
$home_zonecode	= trim($_REQUEST["home_zonecode"]);
$home_post1			= trim($_REQUEST["home_post1"]);
$home_post2			= trim($_REQUEST["home_post2"]);
$home_addr1			= trim($_REQUEST["home_addr1"]);
$home_addr2			= trim($_REQUEST["home_addr2"]);
$home_tel				= implode("-",$_REQUEST["home_tel"]);
$mobile					= implode("-",$_REQUEST["mobile"]);
//$email					= trim($_REQUEST["email1"])."@".trim($_REQUEST["email2"]);
$email					= trim($_REQUEST["email"]);
$height					= trim($_REQUEST['height']);
$weigh					= trim($_REQUEST['weigh']);
$job_code				= $_REQUEST['job_code'];
$job						= $erp_job_cd_arr[$job_code];
$news_mail_yn		= $_REQUEST["news_mail_yn"];
$news_sms_yn		= $_REQUEST["news_sms_yn"];
$news_kko_yn		= $_REQUEST["news_kko_yn"];
$emp_id					= $_REQUEST["emp_id"];
//$office_name			= $_REQUEST["office_name"];
$office_code			= $_REQUEST["office_code"];  // 20170825 수정
$staff_reserve_ori	= trim($_REQUEST['staff_reserve_ori']);		// 임직원적립금(이전)
$staff_reserve			= trim($_REQUEST['staff_reserve']);		// 임직원적립금
//$cooper_reserve_ori			= trim($_REQUEST['cooper_reserve_ori']);		// 제휴사적립금(이전) 20171001
//$cooper_reserve			= trim($_REQUEST['cooper_reserve']);		// 제휴사적립금

if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
	$news_yn="Y";
} elseif($news_mail_yn=="Y") {
	$news_yn="M";
} elseif($news_sms_yn=="Y") {
	$news_yn="S";
} else {
	$news_yn="N";
}

if($news_kakao_yn!="Y") $news_kakao_yn	= "N";		

$home_addr			=$home_addr1."↑=↑".$home_addr2;
if ($job_code=='') $job	= "";

$staff_yn		="N";
$cooper_yn	="N";
if($mem_join_type == '2') {
	$staff_yn				="Y";
	$office_name			= "";
} else if($mem_join_type == '3') {
	$cooper_yn			="Y";
	$emp_id					= "";
} else {
	$emp_id					= "";
	$office_name			= "";
}

if($mode=="update"){

	//멤버 변경 로고
	$sel_qry="select * from tblmember a left join tblmembergroup b on(a.group_code=b.group_code) where a.id='{$id}'";
	$sel_result=pmysql_query($sel_qry);
	$sel_data=pmysql_fetch_object($sel_result);

	if($sel_data->group_code!=$group_code){

		$sum_sql = "SELECT sum(price) as sumprice FROM tblorderinfo ";
		$sum_sql.= "WHERE id = '{$id}' AND deli_gbn = 'Y'";
		$sum_result = pmysql_query($sum_sql,get_db_conn());
		$sum_data=pmysql_fetch_object($sum_result);
		$sumprice="0";
		$sumprice=$sum_data->sumprice+$sel_data->sumprice;

		list($after_group)=pmysql_fetch_array(pmysql_query("select group_name from tblmembergroup where group_code='{$group_code}'"));

		$qry="insert into tblmemberchange (
		mem_id,
		before_group,
		after_group,
		accrue_price,
		change_date
		) values (
		'".$id."',
		'".$sel_data->group_name."',
		'".$after_group."',
		'".$sumprice."',
		'".date("Y-m-d")."'
		)";
		//exdebug($qry);
		pmysql_query($qry,get_db_conn());

		// ERP로 회원등급정보 전송
		sendErpMemberGradeChange($id, $group_code);
	}	

	$sql = "UPDATE tblmember SET ";
	$sql.= "name		= '{$name}', ";
	$sql.= "group_code	= '{$group_code}', ";
	$sql.= "email		= '{$email}', ";
	$sql.= "mobile		= '{$mobile}', ";
	$sql.= "home_post	= '{$home_zonecode}', ";
	$sql.= "home_addr	= '{$home_addr}', ";
	$sql.= "home_tel	= '{$home_tel}', ";
	$sql.= "height			= '{$height}', ";
	$sql.= "weigh			= '{$weigh}', ";
	$sql.= "lunar			= '{$lunar}', ";
	$sql.= "job				= '{$job}', ";
	$sql.= "job_code		= '{$job_code}', ";
	$sql.= "birth		= '{$birth}', ";
	$sql.= "gender		= '{$gender}', ";
	$sql.= "news_yn		= '{$news_yn}', ";
	$sql.= "kko_yn		= '{$news_kakao_yn}', ";
	$sql.= "staff_yn ='{$staff_yn}', ";
	$sql.= "cooper_yn ='{$cooper_yn}', ";
	$sql.= "erp_emp_id ='{$emp_id}' ";
//	$sql.= "office_name ='{$office_name}' ";
	//20170825 수정
	if($cooper_yn == "Y"){
	list($company_group)=pmysql_fetch_array(pmysql_query("select group_no from tblcompanygroup where group_code='{$office_code}'"));
	$sql.= ",company_code ='{$office_code}' ";
	$sql.= ",company_group ='{$company_group}' ";
	}
	$sql.= "WHERE id='{$id}' ";
	//exdebug($sql);
	//exit;

	pmysql_query($sql,get_db_conn());

	
	// ERP로 회원정보 전송..2016-12-19
	sendErpMemberInfo($id, "modify");

	//임직원적립금이 변경되었는지 체크한다. (2016.05.10 - 김재수)
	$ch_staff_reserve	= $staff_reserve - $staff_reserve_ori;
	if ($ch_staff_reserve != 0) { // 변경되었다면 지급/차감 한다.
		$add_ment	= "지급";
		if ($ch_staff_reserve < 0) $add_ment	= "차감";
		insert_staff_point($id, $ch_staff_reserve, "관리자 임직원포인트 ".$add_ment, '@admin',$_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''));
	}
/*
	//제휴사적립금이 변경되었는지 체크한다. 20171001
	$ch_cooper_reserve	= $cooper_reserve - $cooper_reserve_ori;
	if ($ch_cooper_reserve != 0) { // 변경되었다면 지급/차감 한다.
		$add_ment	= "지급";
		if ($ch_cooper_reserve < 0) $add_ment	= "차감";
		insert_cooper_point($id, $ch_cooper_reserve, "관리자 제휴사포인트 ".$add_ment, '@admin',$_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''));
	}
*/
	alert_go("{$id} 회원님의 개인정보 수정이 완료되었습니다.\\n\\n감사합니다.","crm_view.php?menu=mem_list&id={$id}");

}





?>