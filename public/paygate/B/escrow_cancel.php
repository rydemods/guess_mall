<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$mid=$_REQUEST["mid"];
$mertkey=$_REQUEST["mertkey"];
$ordercode=$_REQUEST["ordercode"];

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
		echo "OK|�ش� ����ũ�� �������� �̹� ���ó�� �Ǿ����ϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�.";
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
			$mod_type="STE4";
			if($row->paymethod=="Q") {
				//ȯ�� �Ǵ� �߱ް������� ����
				if($row->ok=="Y") {	//ȯ��ó��
					if(strlen($row->refund_account)==0 || strlen($row->refund_name)==0 || strlen($row->refund_bank_code)==0) {
						echo "NO|�ش� ����ũ�� �������� ȯ�Ҽ������ ������ ����ϼž� �ּ�ó���� �����մϴ�.\\n\\nȯ�Ұ��¼����Է� �� ���ó�� �Ͻñ� �ٶ��ϴ�."; exit;
					}
					$refund_account=$row->refund_account;
					$refund_account=str_replace("-","",$refund_account);
					$refund_nm=$row->refund_name;
					$bank_code=$row->refund_bank_code;
				}
			}
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
					if(strlen($row->refund_account)==0 || strlen($row->refund_name)==0 || strlen($row->refund_bank_code)==0) {
						echo "NO|�ش� ����ũ�� �������� ȯ�Ҽ������ ������ ����ϼž� �ּ�ó���� �����մϴ�.\\n\\nȯ�Ұ��¼����Է� �� ���ó�� �Ͻñ� �ٶ��ϴ�."; exit;
					}
					$refund_account=$row->refund_account;
					$refund_account=str_replace("-","",$refund_account);
					$refund_nm=$row->refund_name;
					$bank_code=$row->refund_bank_code;
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

//�Ա��� �׳� ���ó�� (�����޿����� "�߱ް�������"�� ���� ������ ��üDB�� ���ó�� �Ѵ�.)
if($mod_type=="STE5") {
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "status		= 'G' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}
$note_url="http://".$shopurl."paygate/B/dacom_process.php";
$ret_url="http://".$shopurl;

$hashdata=md5($mid.$ordercode.$mertkey);
$query="mid=".$mid."&oid=".$ordercode."&tid=".$trans_code."&ret_url=".$ret_url."&note_url=".$note_url."&hashdata=".$hashdata;
if($paymethod=="Q") {
	$query.="&bankcode=".$bank_code."&account=".$refund_account."&paytype=SC0040";
}

$temp = SendSocketPost("pg.dacom.net","/common/cancel.jsp",$query);
//$temp = SendSocketPost("pg.dacom.net","/common/cancel.jsp",$query,7080);

$respcode="";
$paytype="";
$respmsg="";
if(strpos($temp,"respcode\" value=\"")) {
	$respcode = substr($temp,strpos($temp,"respcode\" value=\"")+17,4);
}
if(strpos($temp,"paytype\" value=\"")) {
	$paytype = substr($temp,strpos($temp,"paytype\" value=\"")+16,6);
}
if(strpos($temp,"respmsg\" value=\"")) {
	$tempmsg = substr($temp,strpos($temp,"respmsg\" value=\"")+16);
	$respmsg = substr($tempmsg,0,strpos($tempmsg,"\" >"));
}

if(strlen($respcode)==0) {
	$tempmsg = substr($temp,strpos($temp,"alert('")+7);
	$respmsg = substr($tempmsg,0,strpos($tempmsg,"');"));
}

/*
$fp = fopen("log/test.txt", "a+");
fwrite($fp, $temp);
fclose($fp);
*/

#################### ����ũ�� ��� ��� ó�� ###################
if(strlen($respcode)==0) {
	$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$isupdate=false;
	if($paymethod=="P") {
		if($row->ok=="C") {
			$isupdate=true;
		}
	} else if($paymethod=="Q") {
		if($row->status=="F") {
			$isupdate=true;
		}
	}

	if($isupdate) {
		echo "OK|�ش� ����ũ�� �������� �̹� ���ó�� �Ǿ����ϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�.";
	} else {
		echo "NO|���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.";
		if(strlen($respmsg)>0) {
			echo "\\n\\n���л��� : $respmsg";
		}
	}
	exit;
} else if ($respcode=="0000" || $respcode=="RF00") {
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	if($paymethod=="Q") {
		$sql.= "status	= 'F' ";
	} else if($paymethod=="P") {
		$sql.= "status	= 'X' ";
	}
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
} else {
	echo "NO|���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $respmsg";
	exit;
}
