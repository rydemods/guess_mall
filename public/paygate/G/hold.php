<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$ordercode=$_REQUEST["ordercode"];

if (empty($sitecd)) {
	echo "NO|AllTheGate ����ID�� �����ϴ�.";exit;
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
	if($row->status!="S") {
		switch($row->status) {
			case "D":
				echo "NO|�ش� ����ũ�� �������� ���ó�� �Ǿ����ϴ�."; break;
			case "H":
				echo "OK|�ش� ����ũ�� �������� �̹� ���꺸�� �����Դϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�."; break;
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
			case "N":
				echo "NO|�ش� ����ũ�� �������� ���ó���� �����մϴ�."; break;
		}
		exit;
	}
} else {
	echo "NO|�ش� ����ũ�� �������� �������� �ʽ��ϴ�.";exit;
}
pmysql_free_result($result);

################### ��۽��� ��� ó�� #####################

//DB ������Ʈ
$sql = "UPDATE ".$tblname." SET ";
$sql.= "status	= 'H' ";
$sql.= "WHERE ordercode='".$ordercode."' ";
pmysql_query($sql,get_db_conn());
echo "OK"; exit;
