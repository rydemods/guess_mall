<?php
if($_SERVER['REMOTE_ADDR']!="220.85.12.74") exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
Header("Pragma: no-cache");

 /***************************************************************************************************************
 * �ô�����Ʈ�� ���� ��/��� ����Ÿ�� �޾Ƽ� �������� ó�� �� ��
 * �ô�����Ʈ�� �ٽ� ���䰪�� �����Ѵ�.
 * ��ü�� �°� �����Ͽ� �۾��ϸ� �ȴ�.
***************************************************************************************************************/

/*********************************** �ô�����Ʈ�� ���� �Ѱ� �޴� ���� ���� *************************************/
$trcode     = trim( $_POST["trcode"] );					    //�ŷ��ڵ�
$service_id = trim( $_POST["service_id"] );					//�������̵�
$orderdt    = trim( $_POST["orderdt"] );				    //��������
$virno      = trim( $_POST["virno"] );				        //������¹�ȣ
$deal_won   = trim( $_POST["deal_won"] );					//�Աݾ�
$ordno		= trim( $_POST["ordno"] );                      //�ֹ���ȣ
$inputnm	= trim( $_POST["inputnm"] );					//�Ա��ڸ�
/*********************************** �ô�����Ʈ�� ���� �Ѱ� �޴� ���� �� *************************************/

/***************************************************************************************************************
 * �������� �ش� �ŷ��� ���� ó�� db ó�� ��....
 *
 * trcode = "1" �� �Ϲݰ������ �Ա��뺸����
 * trcode = "2" �� �Ϲݰ������ ����뺸����
 * trcode = "3" �� ����ũ�ΰ������ �Ա��뺸����
 * trcode = "4" �� ����ũ�ΰ������ ����뺸����
 *
 * �� ����ũ�ΰ�������� ��� �Ա��ڸ� ���� �뺸������ ���� �ʽ��ϴ�.
***************************************************************************************************************/
$rSuccYn="n";
$date=date("YmdHis");

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordno."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	if(strlen(AdminMail)>0) {
		@mail(AdminMail,"[PG] ".$ordno." �ֹ���ȣ �������� ����","$sql");
	}
}
pmysql_free_result($result);

$tblname="";
if(strstr("P", $paymethod)) {
	$tblname="tblpcardlog";
} else if(strstr("OQ", $paymethod)) {
	$tblname="tblpvirtuallog";
}

if(strlen($_SERVER['HTTP_HOST'])>0) {
	$envhttphost = $_SERVER['HTTP_HOST'];
} else {
	$envhttphost = getUriDomain($_SERVER['REQUEST_URI']);
}

if(strlen(RootPath)>0) {
	$hostscript=$envhttphost.$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$envhttphost."/";
}
$return_host=$envhttphost;
$return_script=str_replace($envhttphost,"",$shopurl).FrontDir."payresult/allthegate.php";
$query="ordercode=".$ordno."&trcode=".$trcode;

if(($trcode==1 || $trcode==2 || $trcode==3 || $trcode==4) && ($paymethod=="Q" || $paymethod=="O")) {
	####################### ok�� "M|Y", status�� "N"�� ��쿡�� ����ó�� ########################
	$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
	$sql.= "WHERE ordercode='".$ordno."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if(($trcode==2 || $trcode==4) && ($paymethod=="O" || $paymethod=="Q")) {
			$query.="&price=".$deal_won."&ok=C";
			if(strstr("Y", $row->ok)) {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok			= 'M', ";
					$sql.= "bank_price	= NULL, ";
					$sql.= "remitter	= '', ";
					$sql.= "bank_code	= '', ";
					$sql.= "bank_date	= '', ";
					$sql.= "receive_date= '' ";
					$sql.= "WHERE ordercode='".$ordno."'";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rSuccYn  = "y";// ���� : y ���� : n
					} else {
						$rSuccYn  = "n";// ���� : y ���� : n
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$ordno." ���Ա� ó�� ����","$sql");
						}
					}
				} else {
					$rSuccYn  = "n";// ���� : y ���� : n
				}
			} else {
				$rSuccYn  = "y";// ���� : y ���� : n
			}
		} else {
			$query.="&price=".$deal_won."&ok=Y";
			if($row->ok=="M" && $row->status=="N") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok			= 'Y', ";
					$sql.= "bank_price	= '".$deal_won."', ";
					$sql.= "remitter	= '".$inputnm."', ";
					$sql.= "bank_code	= '', ";
					$sql.= "bank_date	= '".$orderdt."', ";
					$sql.= "receive_date= '".$date."' ";
					$sql.= "WHERE ordercode='".$ordno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rSuccYn  = "y";// ���� : y ���� : n
					} else {
						$rSuccYn  = "n";// ���� : y ���� : n
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$ordno." ������� �Ա��뺸 ������Ʈ ����","$sql");
						}
					}
				} else {
					$rSuccYn  = "n";// ���� : y ���� : n
				}
			} else {
				$rSuccYn  = "y";// ���� : y ���� : n
			}
		}
	} else {
		$rSuccYn  = "y";// ���� : y ���� : n
	}
}
/******************************************ó�� ��� ����******************************************************/
$rResMsg  = "";

//����ó�� ��� �ŷ��ڵ�|�������̵�|�ֹ��Ͻ�|������¹�ȣ|ó�����|
$rResMsg .= $trcode."|";
$rResMsg .= $service_id."|";
$rResMsg .= $orderdt."|";
$rResMsg .= $virno."|";
$rResMsg .= $rSuccYn."|";

msg($rResMsg);
exit;
echo $rResMsg;
/******************************************ó�� ��� ����******************************************************/
