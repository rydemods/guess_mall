<?php
/****************************** ���� ���� �߰� *****************************/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
/**************************** ���� ���� �߰� �� ****************************/
/********************************************************************************
*
* ������Ʈ : AGSMobile V1.0
* (�� �� ������Ʈ�� ������ �� �ȵ���̵忡�� �̿��Ͻ� �� ������ �Ϲ� �������������� ������ �Ұ��մϴ�.)
*
* ���ϸ� : AGS_pay_ing.php
* ������������ : 2010/10/6
*
* �ô�����Ʈ ����â���� ���ϵ� �����͸� �޾Ƽ� ���ϰ�����û�� �մϴ�.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
*
*  �� ���ǻ��� ��
*  1.  "|"(������) ���� ����ó�� �� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
*   ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
********************************************************************************/
	
	
	/****************************************************************************
	*
	* [1] ���̺귯��(AGSLib.php)�� ��Ŭ��� �մϴ�.
	*
	****************************************************************************/
	require ("./lib/AGSLib.php");


	/****************************************************************************
	*
	* [2]. agspay4.0 Ŭ������ �ν��Ͻ��� �����մϴ�.
	*
	****************************************************************************/
	$agspay = new agspay40;



	/****************************************************************************
	*
	* [3] AGS_pay.html �� ���� �Ѱܹ��� ����Ÿ
	*
	****************************************************************************/

	/*������*/
	//$agspay->SetValue("AgsPayHome","/data2/local_docs/agspay40/php");			//�ô�����Ʈ ������ġ ���丮 (������ �°� ����)
	$agspay->SetValue("AgsPayHome",$_SERVER[DOCUMENT_ROOT]."/m/agspay");			//�ô�����Ʈ ������ġ ���丮 (������ �°� ����)
	$agspay->SetValue("StoreId",trim($_POST["StoreId"]));		//�������̵�
	$agspay->SetValue("log","true");							//true : �αױ��, false : �αױ�Ͼ���.
	$agspay->SetValue("logLevel","INFO");						//�α׷��� : DEBUG, INFO, WARN, ERROR, FATAL (�ش� �����̻��� �α׸� ��ϵ�)
	$agspay->SetValue("UseNetCancel","true");					//true : ����� ���. false: ����� �̻��
	$agspay->SetValue("Type", "Pay");							//������(�����Ұ�)
	$agspay->SetValue("RecvLen", 7);							//���� ������(����) üũ ������ 6 �Ǵ� 7 ����. 
	
	$agspay->SetValue("AuthTy",trim($_POST["AuthTy"]));			//��������
	$agspay->SetValue("SubTy",trim($_POST["SubTy"]));			//�����������
	$agspay->SetValue("OrdNo",trim($_POST["OrdNo"]));			//�ֹ���ȣ
	$agspay->SetValue("Amt",trim($_POST["Amt"]));				//�ݾ�
	$agspay->SetValue("UserEmail",trim($_POST["UserEmail"]));	//�ֹ����̸���
	$agspay->SetValue("ProdNm",trim($_POST["ProdNm"]));			//��ǰ��

	/*�ſ�ī��&������»��*/
	$agspay->SetValue("MallUrl",trim($_POST["MallUrl"]));		//MallUrl(�������Ա�) - ���� ������ ��������߰�
	$agspay->SetValue("UserId",trim($_POST["UserId"]));			//ȸ�����̵�


	/*�ſ�ī����*/
	$agspay->SetValue("OrdNm",trim($_POST["OrdNm"]));			//�ֹ��ڸ�
	$agspay->SetValue("OrdPhone",trim($_POST["OrdPhone"]));		//�ֹ��ڿ���ó
	$agspay->SetValue("OrdAddr",trim($_POST["OrdAddr"]));		//�ֹ����ּ� ��������߰�
	$agspay->SetValue("RcpNm",trim($_POST["RcpNm"]));			//�����ڸ�
	$agspay->SetValue("RcpPhone",trim($_POST["RcpPhone"]));		//�����ڿ���ó
	$agspay->SetValue("DlvAddr",trim($_POST["DlvAddr"]));		//������ּ�
	$agspay->SetValue("Remark",trim($_POST["Remark"]));			//���
	$agspay->SetValue("DeviId",trim($_POST["DeviId"]));			//�ܸ�����̵�
	$agspay->SetValue("AuthYn",trim($_POST["AuthYn"]));			//��������
	$agspay->SetValue("Instmt",trim($_POST["Instmt"]));			//�Һΰ�����
	$agspay->SetValue("UserIp",$_SERVER["REMOTE_ADDR"]);		//ȸ�� IP

	/*�ſ�ī��(ISP)*/
	$agspay->SetValue("partial_mm",trim($_POST["partial_mm"]));		//�Ϲ��ҺαⰣ
	$agspay->SetValue("noIntMonth",trim($_POST["noIntMonth"]));		//�������ҺαⰣ
	$agspay->SetValue("KVP_CURRENCY",trim($_POST["KVP_CURRENCY"]));	//KVP_��ȭ�ڵ�
	$agspay->SetValue("KVP_CARDCODE",trim($_POST["KVP_CARDCODE"]));	//KVP_ī����ڵ�
	$agspay->SetValue("KVP_SESSIONKEY",$_POST["KVP_SESSIONKEY"]);	//KVP_SESSIONKEY
	$agspay->SetValue("KVP_ENCDATA",$_POST["KVP_ENCDATA"]);			//KVP_ENCDATA
	$agspay->SetValue("KVP_CONAME",trim($_POST["KVP_CONAME"]));		//KVP_ī���
	$agspay->SetValue("KVP_NOINT",trim($_POST["KVP_NOINT"]));		//KVP_������=1 �Ϲ�=0
	$agspay->SetValue("KVP_QUOTA",trim($_POST["KVP_QUOTA"]));		//KVP_�Һΰ���

	/*�ſ�ī��(�Ƚ�)*/
	$agspay->SetValue("CardNo",trim($_POST["CardNo"]));			//ī���ȣ
	$agspay->SetValue("MPI_CAVV",$_POST["MPI_CAVV"]);			//MPI_CAVV
	$agspay->SetValue("MPI_ECI",$_POST["MPI_ECI"]);				//MPI_ECI
	$agspay->SetValue("MPI_MD64",$_POST["MPI_MD64"]);			//MPI_MD64

	/*�ſ�ī��(�Ϲ�)*/
	$agspay->SetValue("ExpMon",trim($_POST["ExpMon"]));				//��ȿ�Ⱓ(��)
	$agspay->SetValue("ExpYear",trim($_POST["ExpYear"]));			//��ȿ�Ⱓ(��)
	$agspay->SetValue("Passwd",trim($_POST["Passwd"]));				//��й�ȣ
	$agspay->SetValue("SocId",trim($_POST["SocId"]));				//�ֹε�Ϲ�ȣ/����ڵ�Ϲ�ȣ

	/*�ڵ������*/
	$agspay->SetValue("HP_SERVERINFO",trim($_POST["HP_SERVERINFO"]));	//SERVER_INFO(�ڵ�������)
	$agspay->SetValue("HP_HANDPHONE",trim($_POST["HP_HANDPHONE"]));		//HANDPHONE(�ڵ�������)
	$agspay->SetValue("HP_COMPANY",trim($_POST["HP_COMPANY"]));			//COMPANY(�ڵ�������)
	$agspay->SetValue("HP_ID",trim($_POST["HP_ID"]));					//HP_ID(�ڵ�������)
	$agspay->SetValue("HP_SUBID",trim($_POST["HP_SUBID"]));				//HP_SUBID(�ڵ�������)
	$agspay->SetValue("HP_UNITType",trim($_POST["HP_UNITType"]));		//HP_UNITType(�ڵ�������)
	$agspay->SetValue("HP_IDEN",trim($_POST["HP_IDEN"]));				//HP_IDEN(�ڵ�������)
	$agspay->SetValue("HP_IPADDR",trim($_POST["HP_IPADDR"]));			//HP_IPADDR(�ڵ�������)

	/*������»��*/
	$agspay->SetValue("VIRTUAL_CENTERCD",trim($_POST["VIRTUAL_CENTERCD"]));	//�����ڵ�(�������)
	$agspay->SetValue("VIRTUAL_DEPODT",trim($_POST["VIRTUAL_DEPODT"]));		//�Աݿ�����(�������)
	$agspay->SetValue("ZuminCode",trim($_POST["ZuminCode"]));				//�ֹι�ȣ(�������)
	$agspay->SetValue("MallPage",trim($_POST["MallPage"]));					//���� ��/��� �뺸 ������(�������)
	$agspay->SetValue("VIRTUAL_NO",trim($_POST["VIRTUAL_NO"]));				//������¹�ȣ(�������)

	/*����ũ�λ��*/
	$agspay->SetValue("ES_SENDNO",trim($_POST["ES_SENDNO"]));				//����ũ��������ȣ

	/*�߰�����ʵ�*/
	$agspay->SetValue("Column1", trim($_POST["Column1"]));						//�߰�����ʵ�1   
	$agspay->SetValue("Column2", trim($_POST["Column2"]));						//�߰�����ʵ�2
	$agspay->SetValue("Column3", trim($_POST["Column3"]));						//�߰�����ʵ�3
	
	/****************************************************************************
	*
	* [4] �ô�����Ʈ ���������� ������ ��û�մϴ�.
	*
	****************************************************************************/
	$agspay->startPay();

	
	/****************************************************************************
	*
	* [5] ��������� ���� ����DB ���� �� ��Ÿ �ʿ��� ó���۾��� �����ϴ� �κ��Դϴ�.
	*
	*	�Ʒ��� ��������� ���Ͽ� �� �������ܺ� ����������� ����Ͻ� �� �ֽ��ϴ�.
	*	
	*	-- ������ --
	*	��üID : $agspay->GetResult("rStoreId")
	*	�ֹ���ȣ : $agspay->GetResult("rOrdNo")
	*	��ǰ�� : $agspay->GetResult("rProdNm")
	*	�ŷ��ݾ� : $agspay->GetResult("rAmt")
	*	�������� : $agspay->GetResult("rSuccYn") (����:y ����:n)
	*	����޽��� : $agspay->GetResult("rResMsg")
	*
	*	1. �ſ�ī��
	*	
	*	�����ڵ� : $agspay->GetResult("rBusiCd")
	*	�ŷ���ȣ : $agspay->GetResult("rDealNo")
	*	���ι�ȣ : $agspay->GetResult("rApprNo")
	*	�Һΰ��� : $agspay->GetResult("rInstmt")
	*	���νð� : $agspay->GetResult("rApprTm")
	*	ī����ڵ� : $agspay->GetResult("rCardCd")
	*
	*
	*	2.�������
	*	��������� ���������� ������¹߱��� �������� �ǹ��ϸ� �Աݴ����·� ���� ���� �Ա��� �Ϸ��� ���� �ƴմϴ�.
	*	���� ������� �����Ϸ�� �����Ϸ�� ó���Ͽ� ��ǰ�� ����Ͻø� �ȵ˴ϴ�.
	*	������ ���� �߱޹��� ���·� �Ա��� �Ϸ�Ǹ� MallPage(���� �Ա��뺸 ������(�������))�� �Աݰ���� ���۵Ǹ�
	*	�̶� ��μ� ������ �Ϸ�ǰ� �ǹǷ� �����Ϸῡ ���� ó��(��ۿ�û ��)��  MallPage�� �۾����ּž� �մϴ�.
	*	�������� : $agspay->GetResult("rAuthTy") (������� �Ϲ� : vir_n ��Ŭ�� : vir_u ����ũ�� : vir_s)
	*	�������� : $agspay->GetResult("rApprTm")
	*	������¹�ȣ : $agspay->GetResult("rVirNo")
	*
	*	3.�ڵ�������
	*	�ڵ��������� : $agspay->GetResult("rHP_DATE")
	*	�ڵ������� TID : $agspay->GetResult("rHP_TID")
	*
	****************************************************************************/
		
	
	/****************************** ���� ���� �߰� *****************************/
	
		$banks = array(
		'39' => '�泲����',
		'34' => '��������',
		'04' => '��������',
		'11' => '�����߾�ȸ',
		'31' => '�뱸����',
		'32' => '�λ�����',
		'02' => '�������',
		'45' => '�������ݰ�',
		'07' => '�����߾�ȸ',
		'48' => '�ſ���������',
		'26' => '(��)��������',
		'05' => '��ȯ����',
		'20' => '�츮����',
		'71' => '��ü��',
		'37' => '��������',
		'23' => '��������',
		'35' => '��������',
		'21' => '(��)��������',
		'03' => '�߼ұ������',
		'81' => '�ϳ�����',
		'88' => '��������',
		'27' => '�ѹ�����',
	);

	$cards = array(
		'0100' => '��',
		'0310' => '�ϳ�����',
		'0200' => 'KB',
		'0201' => '����visa',
		'0206' => '��Ƽvisa',
		'0205' => '�츮visa',
		'0304' => '����visa',
		'0300' => '��ȯ',
		'0309' => '���ú���',
		'1000' => '�ؿ�visa',
		'0500' => '����',
		'1100' => '�ؿ�master',
		'0700' => '�ؿ�JCB',
		'0303' => '����visa',
		'0302' => '����visa',
		'0301' => '����visa',
		'0207' => '�ż����ѹ�',
		'0203' => '�ѹ�visa',
		'0202' => '����visa',
		'0400' => '�Ｚ',
		'0800' => '����',
		'0801' => '�ؿ�Diners',
		'0900' => '�Ե�',
		'0901' => '�ؿ�AMEX',
	);
	
	
	$date=date("YmdHis");
	if(strlen(RootPath)>0) {
		$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$pathnum=@strpos($hostscript,RootPath);
		$shopurl=substr($hostscript,0,$pathnum).RootPath;
	} else {
		$shopurl=$_SERVER['HTTP_HOST']."/";
	}

	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
	$return_resurl=$shopurl."m/payresult.php?ordercode=".$agspay->GetResult("OrdNo");

	$isreload=false;
	$tblname="";
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$agspay->GetResult("OrdNo")."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		if(strstr("CP", $paymethod)) $tblname="tblpcardlog";
		else if(strstr("OQ", $paymethod)) $tblname="tblpvirtuallog";
		else if($paymethod=="M") $tblname="tblpmobilelog";
		else if($paymethod=="V") $tblname="tblptranslog";
	}
	pmysql_free_result($result);

	if(strlen($tblname)>0) {
		$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$agspay->GetResult("OrdNo")."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$isreload=true;
			$pay_data=$row->pay_data;
			$good_mny = $row->price;
			if ($row->ok=="Y") {
				$PAY_GLAG="0000";
				$DELI_GBN="N";
			} else if ($row->ok=="N") {
				$PAY_FLAG="9999";
				$DELI_GBN="C";
			}
			if(strstr("CP", $paymethod)) $PAY_AUTH_NO = "00000000";
		}
		pmysql_free_result($result);
	}
	/****************************** ���� ���� �߰� �� *****************************/	


	if($agspay->GetResult("rSuccYn") == "y")
	{ 
		if($agspay->GetResult("AuthTy") == "virtual"){
			$paymethod="O";
			//������°����� ��� �Ա��� �Ϸ���� ���� �Աݴ�����(������� �߱޼���)�̹Ƿ� ��ǰ�� ����Ͻø� �ȵ˴ϴ�. 
			$PAY_FLAG="0000"; // ��� �ڵ�(0000:�������, 9999:���ν���)
			$DELI_GBN="N"; // ��ۿ���(N:��ó��, C:�ֹ����)
			$MSG1=$agspay->GetResult("rResMsg");

			if($agspay->GetResult("VIRTUAL_CENTERCD") == "20"){
				$pay_data="�츮����(20) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "88"){
				$pay_data="��������(88) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "34"){
				$pay_data="��������(34) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "04"){
				$pay_data="��������(04) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "11"){
				$pay_data="�����߾�ȸ(11) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "31"){
				$pay_data="�뱸����(31) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "32"){
				$pay_data="�λ�����(32) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "02"){
				$pay_data="�������(02) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "45"){
				$pay_data="�������ݰ�(45) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "07"){
				$pay_data="�����߾�ȸ(07) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "48"){
				$pay_data="�ſ���������(48) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "26"){
				$pay_data="(��)��������(26) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "05"){
				$pay_data="��ȯ����(05) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "71"){
				$pay_data="��ü��(71) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "37"){
				$pay_data="��������(37) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "23"){
				$pay_data="��������(23) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "35"){
				$pay_data="��������(35) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "21"){
				$pay_data="(��)��������(21) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "03"){
				$pay_data="�߼ұ������(03) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "81"){
				$pay_data="�ϳ�����(81) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}else if($agspay->GetResult("VIRTUAL_CENTERCD") == "27"){
				$pay_data="�ѹ�����(27) ".$agspay->GetResult("rVirNo")." (������:������ȿ��)";
			}


			$sql = "INSERT INTO tblpordercode VALUES ('".$agspay->GetResult("rOrdNo")."','".$paymethod."') ";
			pmysql_query($sql,get_db_conn());

			$sql = "INSERT INTO tblpvirtuallog (
			ordercode		,
			trans_code		,
			pay_data		,
			pgtype			,
			ok				,
			okdate			,
			price			,
			status			,
			paymethod		,
			sender_name		,
			account			,
			ip				,
			goodname		,
			msg				) VALUES (
			'".$agspay->GetResult("rOrdNo")."', 
			'".$ES_SENDNO."', 
			'".$pay_data."', 
			'C', 
			'M', 
			'".$agspay->GetResult("rApprTm")."', 
			'".$agspay->GetResult("rAmt")."', 
			'N', 
			'".$paymethod."', 
			'".$agspay->GetResult("OrdNm")."', 
			'".$agspay->GetResult("rVirNo")."', 
			'".$_SERVER['REMOTE_ADDR']."', 
			'".$agspay->GetResult("rProdNm")."', 
			'".$MSG1."')";
			pmysql_query($sql,get_db_conn());

		}else{
			$paymethod="C";
			$PAY_FLAG="0000"; // ��� �ڵ�(0000:�������, 9999:���ν���)
			$DELI_GBN="N"; // ��ۿ���(N:��ó��, C:�ֹ����)
			$PAY_AUTH_NO=$agspay->GetResult("rApprNo");
			$MSG1="������� - ���ι�ȣ : ".$agspay->GetResult("rApprNo");
			$pay_data="���ι�ȣ : ".$agspay->GetResult("rApprNo");
			$card_nm = $cards[$agspay->GetResult('rCardCd')];

			$sql = "INSERT INTO tblpordercode VALUES ('".$agspay->GetResult("rOrdNo")."','".$paymethod."') ";
			pmysql_query($sql,get_db_conn());

			$sql = "INSERT INTO tblpcardlog (
			ordercode		,
			trans_code		,
			pay_data		,
			pgtype			,
			ok				,
			okdate			,
			price			,
			status			,
			paymethod		,
			edidate			,
			cardname		,
			noinf			,
			quota			,
			ip				,
			goodname		,
			msg				) VALUES (
			'".$agspay->GetResult("rOrdNo")."', 
			'".$agspay->GetResult("rDealNo")."', 
			'".$pay_data."', 
			'C', 
			'Y', 
			'".$agspay->GetResult("rApprTm")."', 
			'".$agspay->GetResult("rAmt")."', 
			'N', 
			'".$paymethod."', 
			'".$date."', 
			'".$card_nm."', 
			'".($KVP_NOINT=="1"?"Y":"N")."', 
			'".$agspay->GetResult("rInstmt")."', 
			'".$_SERVER['REMOTE_ADDR']."', 
			'".$agspay->GetResult("rProdNm")."', 
			'".$MSG1."')";
			pmysql_query($sql,get_db_conn());

			// ���������� ���� ����ó���κ�
			//echo ("������ ����ó���Ǿ����ϴ�. [" . $agspay->GetResult("rSuccYn")."]". $agspay->GetResult("rResMsg").". " );
		}
	}
	else
	{
		$paymethod="C";
		// �������п� ���� ����ó���κ�
		//echo ("������ ����ó���Ǿ����ϴ�. [" . $agspay->GetResult("rSuccYn")."]". $agspay->GetResult("rResMsg").". " );
		$PAY_FLAG="9999";
		$DELI_GBN="C";
		$PAY_AUTH_NO="";
		$MSG1=$agspay->GetResult("rResMsg");
		$pay_data=$agspay->GetResult("rResMsg");
		$card_nm = $cards[$agspay->GetResult('rCardCd')];

		$sql = "INSERT INTO tblpordercode VALUES ('".$agspay->GetResult("rOrdNo")."','".$paymethod."') ";
		pmysql_query($sql,get_db_conn());

		$sql = "INSERT INTO tblpcardlog (
		ordercode		,
		trans_code		,
		pay_data		,
		pgtype			,
		ok				,
		okdate			,
		price			,
		status			,
		paymethod		,
		edidate			,
		cardname		,
		noinf			,
		quota			,
		ip				,
		goodname		,
		msg				) VALUES (
		'".$agspay->GetResult("rOrdNo")."', 
		'".$agspay->GetResult("rDealNo")."', 
		'ERROR', 
		'C', 
		'N', 
		'".$agspay->GetResult("rApprTm")."', 
		'".$agspay->GetResult("rAmt")."', 
		'N', 
		'".$paymethod."', 
		'".$date."', 
		'".$card_nm."', 
		'".($KVP_NOINT=="1"?"Y":"N")."', 
		'".$agspay->GetResult("rInstmt")."', 
		'".$_SERVER['REMOTE_ADDR']."', 
		'".$agspay->GetResult("rProdNm")."', 
		'".$MSG1."')";
		pmysql_query($sql,get_db_conn());
	}

	$return_data="ordercode=".$agspay->GetResult("rOrdNo")."&real_price=".$agspay->GetResult("rAmt")."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
	$return_data2=str_replace("'","",$return_data);
	
	$sql = "INSERT INTO tblreturndata VALUES ('".$agspay->GetResult("rOrdNo")."','".date("YmdHis")."','".$return_data2."') ";
	pmysql_query($sql,get_db_conn());

	$temp = SendSocketPost($return_host,$return_script,$return_data);
	if($temp!="ok") {
		//error (���� �߼�)
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$agspay->GetResult("rOrdNo")." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
		}
	} else {
		pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$agspay->GetResult("rOrdNo")."'",get_db_conn());
	}
	
	echo "<script>";
	echo "opener.location.href=\"http://".$return_resurl."\";\n";
	echo "window.close();";
	echo "</script>";
	exit;
	

	/*******************************************************************
	* [6] ������ ����ó������ ������ ��� $agspay->GetResult("NetCancID") ���� �̿��Ͽ�                                     
	* ��������� ���� ��Ȯ�ο�û�� �� �� �ֽ��ϴ�.
	* 
	* �߰� �����ͼۼ����� �߻��ϹǷ� ������ ����ó������ �ʾ��� ��쿡�� ����Ͻñ� �ٶ��ϴ�. 
	*
	* ����� :
	* $agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*                           
	*******************************************************************/
	
	/*
	$agspay->SetValue("Type", "Pay"); // ����
	$agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*/
	
	/*******************************************************************
	* [7] ����DB ���� �� ��Ÿ ó���۾� ������н� �������                                      
	*   
	* $cancelReq : "true" ������ҽ���, "false" ������ҽ������.
	*
	* ��������� ���� ����ó���κ� ���� �� �����ϴ� ���    
	* �Ʒ��� �ڵ带 �����Ͽ� �ŷ��� ����� �� �ֽ��ϴ�.
	*	��Ҽ������� : $agspay->GetResult("rCancelSuccYn") (����:y ����:n)
	*	��Ұ���޽��� : $agspay->GetResult("rCancelResMsg")
	*
	* ���ǻ��� :
	* �������(virtual)�� ������� ����� �������� �ʽ��ϴ�.
	*******************************************************************/
	
	// ����ó���κ� ������н� $cancelReq�� "true"�� �����Ͽ� 
	// ������Ҹ� ����ǵ��� �� �� �ֽ��ϴ�.
	// $cancelReq�� "true"������ ���������� �������� �Ǵ��ϼž� �մϴ�.
	
	/*
	$cancelReq = "false";

	if($cancelReq == "true")
	{
		$agspay->SetValue("Type", "Cancel"); // ����
		$agspay->SetValue("CancelMsg", "DB FAIL"); // ��һ���
		$agspay->startPay();
	}
	*/
	


?>
<html>
<head>
</head>
<body onload="javascript:frmAGS_pay_ing.submit();">
<form name=frmAGS_pay_ing method=post action=AGS_pay_result.php>

<!-- �� ���� ���� ��� ���� -->
<input type=hidden name=AuthTy value="<?=$agspay->GetResult("AuthTy")?>">		<!-- �������� -->
<input type=hidden name=SubTy value="<?=$agspay->GetResult("SubTy")?>">			<!-- ����������� -->
<input type=hidden name=rStoreId value="<?=$agspay->GetResult("rStoreId")?>">		<!-- �������̵� -->
<input type=hidden name=rOrdNo value="<?=$agspay->GetResult("rOrdNo")?>">		<!-- �ֹ���ȣ -->
<input type=hidden name=rProdNm value="<?=$agspay->GetResult("ProdNm")?>">		<!-- ��ǰ�� -->
<input type=hidden name=rAmt value="<?=$agspay->GetResult("rAmt")?>">				<!-- �����ݾ� -->
<input type=hidden name=rOrdNm value="<?=$agspay->GetResult("OrdNm")?>">		<!-- �ֹ��ڸ� -->

<input type=hidden name=rSuccYn value="<?=$agspay->GetResult("rSuccYn")?>">	<!-- �������� -->
<input type=hidden name=rResMsg value="<?=$agspay->GetResult("rResMsg")?>">	<!-- ����޽��� -->
<input type=hidden name=rApprTm value="<?=$agspay->GetResult("rApprTm")?>">	<!-- �����ð� -->

<!-- �ſ�ī�� ���� ��� ���� -->
<input type=hidden name=rBusiCd value="<?=$agspay->GetResult("rBusiCd")?>">		<!-- (�ſ�ī�����)�����ڵ� -->
<input type=hidden name=rApprNo value="<?=$agspay->GetResult("rApprNo")?>">		<!-- (�ſ�ī�����)���ι�ȣ -->
<input type=hidden name=rCardCd value="<?=$agspay->GetResult("rCardCd")?>">	<!-- (�ſ�ī�����)ī����ڵ� -->
<input type=hidden name=rDealNo value="<?=$agspay->GetResult("rDealNo")?>">			<!-- (�ſ�ī�����)�ŷ���ȣ -->

<input type=hidden name=rCardNm value="<?=$agspay->GetResult("rCardNm")?>">	<!-- (�Ƚ�Ŭ��,�Ϲݻ��)ī���� -->
<input type=hidden name=rMembNo value="<?=$agspay->GetResult("rMembNo")?>">	<!-- (�Ƚ�Ŭ��,�Ϲݻ��)��������ȣ -->
<input type=hidden name=rAquiCd value="<?=$agspay->GetResult("rAquiCd")?>">		<!-- (�Ƚ�Ŭ��,�Ϲݻ��)���Ի��ڵ� -->
<input type=hidden name=rAquiNm value="<?=$agspay->GetResult("rAquiNm")?>">	<!-- (�Ƚ�Ŭ��,�Ϲݻ��)���Ի�� -->

<!-- �ڵ��� ���� ��� ���� -->
<input type=hidden name=rHP_HANDPHONE value="<?=$agspay->GetResult("HP_HANDPHONE")?>">		<!-- �ڵ�����ȣ -->
<input type=hidden name=rHP_COMPANY value="<?=$agspay->GetResult("HP_COMPANY")?>">			<!-- ��Ż��(SKT,KTF,LGT) -->
<input type=hidden name=rHP_TID value="<?=$agspay->GetResult("rHP_TID")?>">					<!-- ����TID -->
<input type=hidden name=rHP_DATE value="<?=$agspay->GetResult("rHP_DATE")?>">				<!-- �������� -->

<!-- ������� ���� ��� ���� -->
<input type=hidden name=rVirNo value="<?=$agspay->GetResult("rVirNo")?>">					<!-- ������¹�ȣ -->
<input type=hidden name=VIRTUAL_CENTERCD value="<?=$agspay->GetResult("VIRTUAL_CENTERCD")?>">	<!--�Աݰ�����������ڵ�(�츮����:20) -->

<!-- ����������ũ�� ���� ��� ���� -->
<input type=hidden name=ES_SENDNO value="<?=$agspay->GetResult("ES_SENDNO")?>">				<!-- ����������ũ��(������ȣ) -->

</form>
</body> 
</html>
