<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$mid=$_REQUEST["mid"];
$mertkey=$_REQUEST["mertkey"];
$ordercode=$_REQUEST["ordercode"];
$deli_num=$_REQUEST["deli_num"];
$delicom_code=urldecode($_REQUEST["delicom_code"]);

if (empty($mid)) {
	echo "NO|������ ����ID�� �����ϴ�.";exit;
}
if (empty($mertkey)) {
	echo "NO|������ ���� mertkey�� �����ϴ�.";exit;
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

################## ó����ƾ �߰� ##################
$dlvdate=date("YmdHi");
$dlvcompcode=$delicom_code;
$dlvno=$deli_num;
$hashdata=md5($mid.$ordercode.$dlvdate.$dlvcompcode.$dlvno.$mertkey);

$query="mid=".$mid."&oid=".$ordercode."&dlvtype=03&dlvdate=".$dlvdate."&dlvcompcode=".$dlvcompcode."&dlvno=".$dlvno."&dlvworker=&dlvworkertel=&hashdata=".$hashdata."&productid=ID0000";

$temp = SendSocketPost("pgweb.dacom.net","/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp",$query);
//$temp = SendSocketGet("pgweb.dacom.net","/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp",$query,7085);

if(trim($temp)=="OK") {
	//DB ������Ʈ
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "status	= 'S' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
} else {
	echo "NO|����ũ�� ��������� �������� ���Ͽ����ϴ�.";
	exit;
}
