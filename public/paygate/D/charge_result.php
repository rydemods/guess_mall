<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$gopaymethod = $_POST["gopaymethod"];
$goodname = $_POST["goodname"];
$price = $_POST["price"];
$buyername = $_POST["buyername"];
$buyeremail = $_POST["buyeremail"];
$buyertel = $_POST["buyertel"];
$parentemail = $_POST["parentemail"];
$mid = $_POST["mid"];
$currency = $_POST["currency"];
$oid = $_POST["oid"];
$nointerest = $_POST["nointerest"];
$quotabase = $_POST["quotabase"];
$acceptmethod = $_POST["acceptmethod"];
$ini_logoimage_url = $_POST["ini_logoimage_url"];
$ini_menuarea_url = $_POST["ini_menuarea_url"];
$recvname = $_POST["recvname"];
$recvtel = $_POST["recvtel"];
$recvpostnum = $_POST["recvpostnum"];
$recvaddr = $_POST["recvaddr"];
$quotainterest = $_POST["quotainterest"];
$paymethod = $_POST["paymethod"];
$cardcode = $_POST["cardcode"];
$cardquota = $_POST["cardquota"];
$rbankcode = $_POST["rbankcode"];
$reqsign = $_POST["reqsign"];
$encrypted = $_POST["encrypted"];
$sessionkey = $_POST["sessionkey"];
$uid = $_POST["uid"];
$sid = $_POST["sid"];
$version = $_POST["version"];
$clickcontrol = $_POST["clickcontrol"];

$pricecheck = "";
if(strlen($oid)>0) {
	$sql = "SELECT price FROM tblorderinfotemp WHERE ordercode = '".$oid."' ";
	$result=pmysql_query($sql,get_db_conn());
	$oidrow=@pmysql_fetch_object($result);
	pmysql_free_result($result);

	$OriginalPrice = (int)$oidrow->price;
	$PostPrice = $price;
	if($OriginalPrice != $PostPrice) {
		$pricecheck = "P";
	}
} else {
	$pricecheck = "O";
}
#### PG ����Ÿ ���� ####
$_ShopInfo->getPgdata();
########################
switch($gopaymethod) {
	case "onlycard":
		$pgid_info=GetEscrowType($_data->card_id);
		break;
	case "onlyvbank":
		$pgid_info=GetEscrowType($_data->virtual_id);
		break;
	case "onlyhpp":
		$pgid_info=GetEscrowType($_data->mobile_id);
		break;
	case "onlydbank":
		$pgid_info=GetEscrowType($_data->trans_id);
		break;
	default :
		break;
}

if(strlen($pgid_info["KEY"])==0) {
	$pricecheck = "A";
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

/* INIsecurepay.php
 *
 * �̴����� �÷������� ���� ��û�� ������ ó���Ѵ�.
 * ���� ��û�� ó���Ѵ�.
 * �ڵ忡 ���� �ڼ��� ������ �Ŵ����� �����Ͻʽÿ�.
 * <����> �������� ������ �ݵ�� üũ�ϵ����Ͽ� �����ŷ��� �����Ͽ� �ֽʽÿ�.
 *  
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
 */

	/**************************
	 * 1. ���̺귯�� ��Ŭ��� *
	 **************************/
	require("INIpay41Lib.php");
	
	
	/***************************************
	 * 2. INIpay41 Ŭ������ �ν��Ͻ� ���� *
	 ***************************************/
	$inipay = new INIpay41;


	/*********************
	 * 3. ���� ���� ���� *
	 *********************/
	$inipay->m_inipayHome = $_SERVER['DOCUMENT_ROOT']."/".RootPath."paygate/D"; 	// �̴����� Ȩ���͸�
	$inipay->m_type = "securepay"; 					// ���� (���� ���� �Ұ�)
	$inipay->m_pgId = "INIpay".$pgid; 				// ���� (���� ���� �Ұ�)
	$inipay->m_subPgIp = "203.238.3.10"; 			// ���� (���� ���� �Ұ�)
	$inipay->m_keyPw = $pgid_info["KEY"]; 			// Ű�н�����(�������̵� ���� ����)
	$inipay->m_debug = "true"; 						// �α׸��("true"�� �����ϸ� �󼼷αװ� ������.)
	$inipay->m_mid = $mid; 							// �������̵�
	$inipay->m_uid = $uid; 							// INIpay User ID (���� ���� �Ұ�)
	$inipay->m_uip = $_SERVER['REMOTE_ADDR']; 		// ���� (���� ���� �Ұ�)
	$inipay->m_goodName = $goodname;				// ��ǰ�� 
	$inipay->m_currency = $currency;				// ȭ�����
	$inipay->m_price = $price;						// �����ݾ�
	$inipay->m_buyerName = $buyername;				// ������ ��
	$inipay->m_buyerTel = $buyertel;				// ������ ����ó(�޴��� ��ȣ �Ǵ� ������ȭ��ȣ)
	$inipay->m_buyerEmail = $buyeremail;			// ������ �̸��� �ּ�
	$inipay->m_payMethod = $paymethod;				// ���ҹ�� (���� ���� �Ұ�)
	$inipay->m_encrypted = $encrypted;				// ��ȣ��
	$inipay->m_sessionKey = $sessionkey;			// ��ȣ��
	$inipay->m_url = "http://".$shopurl; 			// ���� ���񽺵Ǵ� ���� SITE URL�� �����Ұ�
	$inipay->m_cardcode = $cardcode; 				// ī���ڵ� ����
	$inipay->m_ParentEmail = $parentemail; 			// ��ȣ�� �̸��� �ּ�(�ڵ��� , ��ȭ�����ÿ� 14�� �̸��� ���� �����ϸ�  �θ� �̸��Ϸ� ���� �����뺸 �ǹ�, �ٸ����� ���� ���ÿ� ���� ����)
	$inipay->m_recvName = $recvname;				// ������ ��
	$inipay->m_recvTel = $recvtel;					// ������ ����ó
	$inipay->m_recvAddr = $recvaddr;				// ������ �ּ�
	$inipay->m_recvPostNum = $recvpostnum;			// ������ �����ȣ
	$inipay->m_recvMsg = $recvmsg;					// ���� �޼���
	
	
	/****************
	 * 4. ���� ��û *
	 ****************/
	$inipay->startAction();
	$good_mny = $inipay->m_resultprice; //����

	if(strlen($pricecheck)>0)
	{
		$inipay->m_type = "cancel"; // ����

		if($pricecheck=="P") {
			$inipay->m_msg = "Price ����ġ"; // ��һ���
		} else if($pricecheck=="O") {
			$inipay->m_msg = "OrderCode �� ������"; // ��һ���
		} else if($pricecheck=="A") {
			$inipay->m_msg = "PGKEY �� ������"; // ��һ���
		}
		$inipay->startAction();
		if($inipay->m_resultCode == "00")
		{
			$inipay->m_resultCode = "01";
			$inipay->m_resultMsg = $inipay->m_msg;
		}
	}
	
	$ordr_idxx = $oid;

	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
	$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$ordr_idxx;

	$isreload=false;
	$tblname="";
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordr_idxx."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod_self=$row->paymethod;
		if(strstr("CP", $paymethod_self)) $tblname="tblpcardlog";
		else if(strstr("OQ", $paymethod_self)) $tblname="tblpvirtuallog";
		else if($paymethod_self=="M") $tblname="tblpmobilelog";
		else if($paymethod_self=="V") $tblname="tblptranslog";
	}
	pmysql_free_result($result);

	if(strlen($tblname)>0) {
		$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordr_idxx."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$isreload=true;
			$pay_data=$row->pay_data;
			$good_mny = $row->price;
			$MSG1 = $row->msg;
			if ($row->ok=="Y") {
				$PAY_GLAG="0000";
				$DELI_GBN="N";
			} else if ($row->ok=="N") {
				$PAY_FLAG="9999";
				$DELI_GBN="C";
			}
			if(strstr("CP", $paymethod_self)) $PAY_AUTH_NO = "00000000";
		}
		pmysql_free_result($result);
	}

	if($isreload!=true) {
		$date=$inipay->m_pgAuthDate.$inipay->m_pgAuthTime;
		if ($inipay->m_resultCode == "00") {	//�������
			$PAY_FLAG="0000";
			$DELI_GBN="N";
			$MSG1=$inipay->m_resultMsg;
			$pay_data=$inipay->m_resultMsg;
			$ok="Y";
			if ($inipay->m_payMethod == "VCard" || $inipay->m_payMethod == "Card") {	//�ſ�ī��
				$tblname="tblpcardlog";
				$paymethod_self="C";
				$PAY_AUTH_NO=$inipay->m_authCode;
				$MSG1="������� - ���ι�ȣ : ".$PAY_AUTH_NO;
				$pay_data="���ι�ȣ : ".$inipay->m_authCode."";
			} else if ($inipay->m_payMethod == "DirectBank") {	//������ü
				$tblname="tblptranslog";
				$paymethod_self="V";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
				if(strlen($inipay->m_directbankcode)==2) {
					$banksql = "SELECT bank_name FROM tblpbankcode WHERE code='".$inipay->m_directbankcode."' ";
					$bankresult=pmysql_query($banksql,get_db_conn());
					$bankrow=@pmysql_fetch_object($bankresult);
					$bank_name = $bankrow->bank_name;
				}
			} else if ($inipay->m_payMethod == "VBank") { //�������
				$ok="M";
				$tblname="tblpvirtuallog";
				$paymethod_self="O";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
				if(strlen($inipay->m_vcdbank)==2) {
					$banksql = "SELECT bank_name FROM tblpbankcode WHERE code='".$inipay->m_vcdbank."' ";
					$bankresult=pmysql_query($banksql,get_db_conn());
					$bankrow=@pmysql_fetch_object($bankresult);
					$bank_name = $bankrow->bank_name;
				}
				$pay_data=$bank_name."(".$inipay->m_vcdbank.") ".$inipay->m_vacct." (������:".$inipay->m_nmvacct.")";
			} else if ($inipay->m_payMethod == "HPP") { //�޴���
				$tblname="tblpmobilelog";
				$paymethod_self="M";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";
			}
			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod_self."') ";
			pmysql_query($sql,get_db_conn());

			$sql = "INSERT ".$tblname." SET ";
			$sql.= "ordercode		= '".$ordr_idxx."', ";
			$sql.= "trans_code		= '".$inipay->m_tid."', ";
			$sql.= "pay_data		= '".$pay_data."', ";
			$sql.= "pgtype			= 'D', ";
			$sql.= "ok				= '".$ok."', ";
			$sql.= "okdate			= '".$date."', ";
			$sql.= "price			= '".$good_mny."', ";
			if ($inipay->m_payMethod == "VCard" || $inipay->m_payMethod == "Card") {		//�ſ�ī��
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "edidate			= '".$date."', ";
				$sql.= "cardname		= '".$inipay->m_cardCode."', ";
				$sql.= "noinf			= '".($inipay->m_quotaInterest=="1"?"Y":"")."', ";
				$sql.= "quota			= '".$inipay->m_cardQuota."', ";
			} else if($inipay->m_payMethod == "DirectBank") {	//������ü
				$sql.= "bank_name		= '".$bank_name."', ";
			} else if($inipay->m_payMethod == "VBank") {	//�������
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "sender_name		= '".$inipay->m_nminput."', ";
				$sql.= "account			= '".$inipay->m_vacct."', ";
			} else if ($inipay->m_payMethod == "HPP") { //�޴���

			}
			$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
			$sql.= "goodname		= '".$goodname."', ";
			$sql.= "msg				= '".$MSG1."' ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);
			
		} else {	//���ν���
			$PAY_FLAG="9999";
			$DELI_GBN="C";
			$MSG1=$inipay->m_resultMsg;
			$PAY_AUTH_NO="";
			$pay_data=$inipay->m_resultMsg;
			if ($gopaymethod == "onlycard") {	//�ſ�ī��
				$tblname="tblpcardlog";
				$paymethod_self="C";
			} else if ($gopaymethod == "onlydbank") {	//������ü
				$tblname="tblptranslog";
				$paymethod_self="V";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($gopaymethod == "onlybank") { //�������
				$tblname="tblpvirtuallog";
				$paymethod_self="O";
				$card_name="";
				$noinf="";
				$quota="";
			} else if ($gopaymethod == "onlyhpp") { //�޴���
				$tblname="tblpmobilelog";
				$paymethod_self="M";
				$card_name="";
				$noinf="";
				$quota="";
			}

			$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod_self."') ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);

			$sql = "INSERT ".$tblname." SET ";
			$sql.= "ordercode		= '".$ordr_idxx."', ";
			$sql.= "trans_code		= '".$inipay->m_tid."', ";
			$sql.= "pay_data		= 'ERROR', ";
			$sql.= "pgtype			= 'D', ";
			$sql.= "ok				= 'N', ";
			$sql.= "okdate			= '".$date."', ";
			$sql.= "price			= '".$good_mny."', ";
			if ($gopaymethod == "onlycard") {		//�ſ�ī��
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "edidate			= '".$date."', ";
				$sql.= "cardname		= '".$inipay->m_cardCode."', ";
				$sql.= "noinf			= '".($inipay->m_quotaInterest=="1"?"Y":"")."', ";
				$sql.= "quota			= '".$inipay->m_cardQuota."', ";
			} else if($gopaymethod == "onlydbank") {	//������ü
				if(strlen($inipay->m_directbankcode)==2) {
					$banksql = "SELECT bank_name FROM tblpbankcode WHERE code='".$inipay->m_directbankcode."' ";
					$bankresult=pmysql_query($banksql,get_db_conn());
					$bankrow=@pmysql_fetch_object($bankresult);
					$bank_name = $bankrow->bank_name;
				}
				$sql.= "bank_name		= '".$bank_name."', ";
			} else if($gopaymethod == "onlybank") {	//�������
				$sql.= "status			= 'N', ";
				$sql.= "paymethod		= '".$paymethod_self."', ";
				$sql.= "sender_name		= '".$inipay->m_nminput."', ";
				$sql.= "account			= '".$inipay->m_vacct."', ";
			} else if ($gopaymethod == "onlyhpp") { //�޴���

			}
			$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
			$sql.= "goodname		= '".$goodname."', ";
			$sql.= "msg				= '".$MSG1."' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
	$return_data2=str_replace("'","",$return_data);
	$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
	pmysql_query($sql,get_db_conn());

	$temp = SendSocketPost($return_host,$return_script,$return_data);
	if($temp!="ok") {
		//error (���� �߼�)
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$ordr_idxx." �������� ������Ʈ ����",$return_host."<br>".$return_script."<br>".$return_data);
		}
	} else {
		pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
	}

	echo "<script>";
	echo "opener.location.href=\"http://".$return_resurl."\";\n";
	echo "window.close();";
	echo "</script>";
	exit;
