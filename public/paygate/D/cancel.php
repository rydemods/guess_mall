<?php
/*
�ſ�ī��/�ڵ��� ���ó��
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$sitecd=$_POST["sitecd"];
$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

if (empty($sitecd)) {
	alert_go('INICIS ����ID�� �����ϴ�.',-1);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	alert_go(get_message("�ش� ���ΰ��� �������� �ʽ��ϴ�."),-1);
}
pmysql_free_result($result);

#### PG ����Ÿ ���� ####
$_ShopInfo->getPgdata();
########################
switch($paymethod) {
	case "C":
		$pay_method="onlycard";
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "P":
		$pay_method="onlycard";
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "O":
		$pay_method="onlyvbank";
		$pgid_info=GetEscrowType($_data->virtual_id);
		break;
	case "Q":
		$pay_method="onlyvbank";
		$pgid_info=GetEscrowType($_data->escrow_id);
		break;
	case "M":
		$pay_method="onlyhpp";
		$pgid_info=GetEscrowType($_data->mobile_id);
		break;
	case "V":
		$pay_method="onlydbank";
		$pgid_info=GetEscrowType($_data->trans_id);
		break;
}

$sitekey = $pgid_info["KEY"];

if (empty($sitekey)) {
	alert_go('�̴Ͻý� ����KEY�� �����ϴ�.',-1);
}

$tblname="";
if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
else if($paymethod=="M")					$tblname="tblpmobilelog";
else {
	alert_go('�߸��� ó���Դϴ�.',-1);
}

//���������� ���翩�� Ȯ��
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if($row->ok=="C") {	//�̹� ���ó���� ��
		echo "<script>alert('".get_message("�ش� �������� �̹� ���ó���Ǿ����ϴ�. ���θ��� ��ݿ��˴ϴ�.")."')</script>\n";
		if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
			echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
			echo "<input type=hidden name=rescode value=\"C\">\n";
			$text = explode("&",$return_data);
			for ($i=0;$i<sizeOf($text);$i++) {
				$textvalue = explode("=",$text[$i]);
				echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
			}
			echo "</form>";
			echo "<script>document.form1.submit();</script>";
			exit;
		} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
			$return_data.="&rescode=C";
			//������� ó��
			exit;
		}
	}
} else {
	alert_go(get_message("�ش� ���ΰ��� �������� �ʽ��ϴ�."),-1);
}
pmysql_free_result($result);

if (strlen($row->trans_code)==0) {
	alert_go('�̴Ͻý� ������ȣ�� �������� �ʽ��ϴ�.',-1);
}

$mid = $sitecd;
$mkey = $sitekey;
$tid = $row->trans_code;

/**************************
 * 1. ���̺귯�� ��Ŭ��� *
 **************************/
require("INIpay41Lib.php");


/***************************************
 * 2. INIpay41 Ŭ������ �ν��Ͻ� ���� *
 ***************************************/
$inipay = new INIpay41;


/*********************
 * 3. ��� ���� ���� *
 *********************/
$inipay->m_inipayHome = $_SERVER['DOCUMENT_ROOT']."/".RootPath."paygate/D"; // �̴����� Ȩ���͸�
$inipay->m_type = "cancel"; // ����
$inipay->m_subPgIp = "203.238.3.10"; // ����
$inipay->m_keyPw = $mkey; // Ű�н�����(�������̵� ���� ����)
$inipay->m_debug = "true"; // �α׸��("true"�� �����ϸ� �󼼷αװ� ������.)
$inipay->m_mid = $mid; // �������̵�
$inipay->m_tid = $tid; // ����� �ŷ��� �ŷ����̵�
$inipay->m_cancelMsg = $msg; // ��һ���
$inipay->m_uip = $_SERVER['REMOTE_ADDR']; // ����


/****************
 * 4. ��� ��û *
 ****************/
$inipay->startAction();


/****************************************************************
 * 5. ��� ���                                           	*
 *                                                        	*
 * ����ڵ� : $inipay->m_resultCode ("00"�̸� ��� ����)  	*
 * ������� : $inipay->m_resultMsg (��Ұ���� ���� ����) 	*
 * ��ҳ�¥ : $inipay->m_pgCancelDate (YYYYMMDD)          	*
 * ��ҽð� : $inipay->m_pgCancelTime (HHMMSS)            	*
 * ���ݿ����� ��� ���ι�ȣ : $inipay->m_rcash_cancel_noappl    *
 * (���ݿ����� �߱� ��ҽÿ��� ���ϵ�)                          * 
 ****************************************************************/
############### ��Ұ��ó�� #############
if($inipay->m_resultCode!="00") {
	alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : ".$inipay->m_resultMsg." (".$inipay->m_resultCode.")"),-1);
} else {
	//������Ʈ
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "canceldate	= '".date("YmdHis")."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()) {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$tblname." ��� update ����!",$sql." - ".pmysql_error());
		}
		alert_go(get_message("��Ҵ� ���� ó���Ǿ����� ����DB�� �ݿ��� �ȵǾ����ϴ�.\\n\\n�����ڿ��� �����Ͻñ� �ٶ��ϴ�."),-1);
	}
	if($inipay->m_resultCode=="00") {
		echo "<script>alert('".get_message("������Ұ� ���������� ó���Ǿ����ϴ�.\\n\\nINICIS �������������� ��ҿ��θ� �� Ȯ���Ͻñ� �ٶ��ϴ�.")."');</script>\n";
	} else {
		echo "<script>alert('".get_message("�̹� ��ҵ� �ŷ� ��ҿ�û���Դϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�.")."');</script>\n";
	}

	if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
		echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
		echo "<input type=hidden name=rescode value=\"C\">\n";
		$text = explode("&",$return_data);
		for ($i=0;$i<sizeOf($text);$i++) {
			$textvalue = explode("=",$text[$i]);
			echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
		}
		echo "</form>";
		echo "<script>document.form1.submit();</script>";
		exit;
	} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
		$return_data.="&rescode=C";
		//������� ó��
		exit;
	}
}
