<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
Header("Pragma: no-cache");

/*
- ����Ȯ�ΰ��
- ������ҿ�û
- ������Ұ��
*/

function getMertkey($gbn) {
	if($f=@file(DirPath.AuthkeyDir."pg")) {
		for($i=0;$i<count($f);$i++) {
			$f[$i]=trim(str_replace("\n","",$f[$i]));
			if (substr($f[$i],0,strlen($gbn))==$gbn) {
				return decrypt_authkey(substr($f[$i],strlen($gbn)));
				break;
			}
		}
	}
}

function write_success($noti){
	//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.	
	//write_log("log/php_escrow_write_success.log", $noti);
	return true;
}

function write_failure($noti){
	//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.	
	//write_log("log/php_escrow_write_failure.log", $noti);
	return true;
}

function write_hasherr($noti) {
	//������ ���� log����� �˴ϴ�. log path���� �� dbó����ƾ�� �߰��Ͽ� �ֽʽÿ�.	
	//write_log("log/php_escrow_write_hasherr.log", $noti);
	return true;
}

function write_log($file, $noti) {
	$fp = fopen($file, "a+");
	ob_start();
	print_r($noti);
	$msg = ob_get_contents();
	ob_end_clean();
	fwrite($fp, $msg);
	fclose($fp);
}

function get_param($name){
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	if (!isset($HTTP_POST_VARS[$name]) || $HTTP_POST_VARS[$name] == "") {
		if (!isset($HTTP_GET_VARS[$name]) || $HTTP_GET_VARS[$name] == "") {
			return false;
		} else {
			 return $HTTP_GET_VARS[$name];
		}
	}
	return $HTTP_POST_VARS[$name];
}

// �����޿��� ���� value
$txtype = "";				// �������(C=����Ȯ�ΰ��, R=������ҿ�û, D=������Ұ��, N=NCó����� )
$mid="";					// �������̵� 
$tid="";					// �������� �ο��� �ŷ���ȣ
$oid="";					// ��ǰ��ȣ
$ssn = "";					// �������ֹι�ȣ
$ip = "";					// ������IP
$mac = "";					// ������ mac
$hashdata = "";				// ������ ���� ������
$productid = "";			// ��ǰ����Ű
$resdate = "";				// ����Ȯ�� ��û�Ͻ�
$resp = false;				// ������� ��������

$txtype = get_param("txtype");
$mid = get_param("mid");
$tid = get_param("tid");
$oid = get_param("oid");
$ssn = get_param("ssn");	
$ip = get_param("ip");
$mac = get_param("mac");
$hashdata = get_param("hashdata");
$productid = get_param("productid");
$resdate = get_param("resdate");

//tblpordercode Ȯ���Ͽ� ������� Ȯ��
$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$oid."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	if(strlen(AdminMail)>0) {
		@mail(AdminMail,"[PG] ".$oid." �ֹ���ȣ �������� ����","$sql");
	}
}
pmysql_free_result($result);

$pgid_info=array();
$mertkey = ""; //�����޿��� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.
if($paymethod=="C") {
	$pgdata=getMertkey("card_id:::");
} else if($paymethod=="V") {
	$pgdata=getMertkey("trans_id:::");
} else if($paymethod=="O") {
	$pgdata=getMertkey("virtual_id:::");
} else if($paymethod=="Q") {
	$pgdata=getMertkey("escrow_id:::");
} else if($paymethod=="M") {
	$pgdata=getMertkey("mobile_id:::");
}
if($pgdata) {
	$pgid_info=GetEscrowType($pgdata);
}
$mertkey=$pgid_info["KEY"];

$hashdata2 = md5($mid.$oid.$tid.$txtype.$productid.$ssn.$ip.$mac.$resdate.$mertkey); // 

$value = array( "txtype"		=> $txtype, 
				"mid"    		=> $mid,
				"tid" 			=> $tid,
				"oid"     		=> $oid,
				"ssn" 			=> $ssn,					
				"ip"			=> $ip,
				"mac"			=> $mac,
				"resdate"		=> $resdate,
				"hashdata"    	=> $hashdata,
				"productid"		=> $productid,  
				"hashdata2"  	=> $hashdata2 );

if ($hashdata2 == $hashdata) {			//�ؽ��� ������ �����ϸ�
	$resp = write_success($value);
} else {								//�ؽ��� ������ �����̸�
	write_hasherr($value);
}

$tblname="";
if(strstr("P", $paymethod)) {
	$tblname="tblpcardlog";
} else if(strstr("OQ", $paymethod)) {
	$tblname="tblpvirtuallog";
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}
$return_host=$_SERVER['HTTP_HOST'];
$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/dacom.php";
$query="ordercode=".$oid."&txtype=".$txtype;

if(strstr("NCR", $txtype)) {			//�ڵ�����Ȯ��, ����Ȯ�ΰ��, ������ҿ�û
	if(strstr("QP", $paymethod)) {
		########################## status�� "S"�� ��쿡�� ����ó�� #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="S") {
				if(strstr("NC", $txtype)) $query.="&ok=Y";
				else if($txtype=="R") $query.="&ok=C";
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					if(strstr("NC", $txtype)) {	//����Ȯ��
						$sql.= "status	= 'Y' ";
					} else if($txtype=="R") {				//�������
						$sql.= "status	= 'H' ";
					}
					$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$oid." ����Ȯ��/��������뺸 ������Ʈ ����","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
} else if($txtype=="D") {				//������Ұ�� (��������� ��쿣 ȯ�ҿϷ����???)
	if(strstr("Q", $paymethod)) {
		########################## status�� "F"�� ��쿡�� ����ó�� #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if(strstr("CY", $row->ok) && $row->status=="F") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok				= 'C', ";
					$sql.= "status			= 'E', ";
					$sql.= "refund_date		= '".$resdate."', ";
					$sql.= "refund_receive_date='".$date."' ";
					$sql.= "WHERE ordercode='".$oid."' AND trans_code='".$tid."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$oid." ������� ȯ���뺸 ������Ʈ ����","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

if($resp) {								//��������� �����̸�
	echo "OK";
} else {								//��������� �����̸�
	echo "FAIL",$value;
}
