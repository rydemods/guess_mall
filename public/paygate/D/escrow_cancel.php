<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$ordercode=$_REQUEST["ordercode"];
$curgetid=$_REQUEST["curgetid"];

if (empty($sitecd)) {
	echo "NO|KCP ����ID�� �����ϴ�.";exit;
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

$mod_type="";
$refund_account="";
$refund_nm="";
$bank_code="";

//���������� ���翩�� Ȯ��
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|�ش� ����ũ�� �������� �������� �ʽ��ϴ�.";exit;
	}
	if($row->ok=="C") {
		echo "OK|�ش� ����ũ�� �������� �̹� ���ó�� �Ǿ����ϴ�.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "NO|�ش� ����ũ�� �������� ��ǰ ������Դϴ�.\\n\\n���꺸�� �� ���ó���� �����մϴ�."; exit;
			break;
		case "D":
		case "X":
		case "C":
			echo "OK|�ش� ����ũ�� �������� �̹� ���ó�� �Ǿ����ϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�."; exit;
			break;
		case "H":
			//���꺸���Ȱǿ����ؼ����ó�� ���� ����
			$mod_type="STE2";
			break;
		case "Y":
			echo "NO|�ش� ����ũ�� �������� ����Ȯ�� ó���� �Ǿ� ��Ұ� �Ұ����մϴ�."; exit;
			break;
		case "E":
			echo "NO|�ش� ����ũ�� �������� ȯ��ó�� �Ǿ����ϴ�."; exit;
			break;
		case "G":
			echo "NO|�ش� ����ũ�� �������� �߱ް��°� �����Ǿ����ϴ�."; exit;
			break;
		case "N":
			if($row->paymethod=="Q") {
				//ȯ�� �Ǵ� �߱ް������� ����
				if($row->ok=="Y") {	//ȯ��ó��
					$mod_type="STE2";
				} else {			//�߱ް�������
					$mod_type="STE5";
				}
			} else if($row->paymethod=="P") {
				//������ ����
				$mod_type="STE2";
			}
			break;
		default:
			exit;
			break;
	}
} else {
	echo "NO|�ش� ����ũ�� �������� �������� �ʽ��ϴ�.";exit;
}
pmysql_free_result($result);

if($paymethod=="Q" && $mod_type!="STE5" && $row->status!="C") {
	$check_host=$_SERVER['HTTP_HOST'];
	$check_script="/".RootPath."paygate/D/escrow/INIescrow.php";
	
	$check_query="hanatid=".$row->trans_code."&mid=".$sitecd."&EscrowType=rr&adminID=".$curgetid."&adminName=������&returntype=0";
	$check_data=SendSocketPost($check_host, $check_script, $check_query);
	$check_data_exp = explode("|",$check_data);
	
	$res_cd = $check_data_exp[0];
	$res_msg = $check_data_exp[1];
} else if($mod_type=="STE5") {
	$res_cd = "0000";
	$res_msg = "�Ա��� ���";
}

################## ��۽��� ��� ó�� ################
if($res_cd!="0000") {
	echo "NO|����ũ�� ��� ó���� �Ʒ��� ���� ������ �������� ���Ͽ����ϴ�.\\n\\n���л��� : $res_msg";
	exit;
} else {
	//DB ������Ʈ
	$sql = "UPDATE ".$tblname." SET ";
	if($mod_type=="STE2") {	//����� ������
		$sql.= "ok			= 'C', ";
		$sql.= "status		= 'C' ";
	} else if($mod_type=="STE4") {	//���꺸���� ������ ���
		$sql.= "ok			= 'C', ";
		if($paymethod=="Q") {
			$sql.= "status	= 'F' ";
		} else if($paymethod=="P") {
			$sql.= "status	= 'X' ";
		}
	} else if($mod_type=="STE5") {	//�߱ް��� ����
		$sql.= "ok			= 'C', ";
		$sql.= "status		= 'G' ";
	}
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}
