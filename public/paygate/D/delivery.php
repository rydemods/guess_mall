<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$ordercode=$_REQUEST["ordercode"];
$deli_num=$_REQUEST["deli_num"];
$deli_name=urldecode($_REQUEST["deli_name"]);
$delicom_code=$_REQUEST["delicom_code"];
$delidate = date("Ymd");

if (empty($sitecd)) {
	echo "NO|INICIS ����ID�� �����ϴ�.";exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	echo "NO|�ش� ����ũ�� �������� �������� �ʽ��ϴ�.";exit;
}
pmysql_free_result($result);

$tblname="";
if(strstr("Q", $paymethod[0]))		$tblname="tblpvirtuallog";
else if($paymethod=="P")					$tblname="tblpcardlog";
else {
	echo "NO|�߸��� ó���Դϴ�.";exit;
}

//���������� ���翩�� Ȯ��
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|�ش� ����ũ�� �������� �������� �ʽ��ϴ�.";exit;
	}
	if($row->ok=="C") {
		echo "NO|�ش� ����ũ�� �������� ���ó�� �Ǿ����ϴ�.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "OK|�ش� ����ũ�� �������� �̹� ���ó�� �Ǿ����ϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�."; exit;
			break;
		case "D":
			echo "NO|�ش� ����ũ�� �������� ���ó�� �Ǿ����ϴ�."; break;
		case "H":
			echo "NO|�ش� ����ũ�� �������� ���꺸�� �����Դϴ�."; break;
		case "X":
			echo "NO|�ش� ����ũ�� �������� ���ó�� �Ǿ����ϴ�."; break;
		case "Y":
			echo "NO|�ش� ����ũ�� �������� ����Ȯ��ó�� �Ǿ����ϴ�."; break;
		case "C":
			echo "NO|�ش� ����ũ�� �������� �������ó�� �Ǿ����ϴ�."; break;
		case "E":
			echo "NO|�ش� ����ũ�� �������� ȯ��ó�� �Ǿ����ϴ�."; break;
		case "G":
			echo "NO|�ش� ����ũ�� �������� �߱ް��°� �����Ǿ����ϴ�."; break;
	}
} else {
	echo "NO|�ش� ����ũ�� �������� �������� �ʽ��ϴ�.";exit;
}
pmysql_free_result($result);

if($paymethod=="Q") {
	$check_host=$_SERVER['HTTP_HOST'];
	$check_script="/".RootPath."paygate/D/escrow/INIescrow.php";
	if(strlen($delicom_code)==0) {
		$delicom_code="OTHEREXPRX";
	}
	$check_query="hanatid=".$row->trans_code."&mid=".$sitecd."&EscrowType=dr&transtype=S0&invno=".$deli_num."&compID=".$delicom_code."&compName=".$deli_name."&transdate1=".$delidate."&transdate2=".$delidate;
	$check_data=SendSocketPost($check_host, $check_script, $check_query);
	$check_data_exp = explode("|",$check_data);
	
	$res_cd = $check_data_exp[0];
	$res_msg = $check_data_exp[1];
}

################## ��۽��� ��� ó�� ################
if($res_cd!="0000") {
	echo "NO|����ũ�� ��������� �Ʒ��� ���� ������ �������� ���Ͽ����ϴ�.\\n\\n���л��� : $res_msg";
	exit;
} else {
	//DB ������Ʈ
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "status	= 'S' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}
