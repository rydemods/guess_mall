<?php
/****************************** ���� ���� �߰� *****************************/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
/**************************** ���� ���� �߰� �� ****************************/

/**********************************************************************************************
*
* ���ϸ� : AGS_pay_ing.php
* ������������ : 2007/04/25
*
* �ô�����Ʈ �÷����ο��� ���ϵ� ����Ÿ�� �޾Ƽ� ���ϰ�����û�� �մϴ�.
*
* Copyright 2006-2007 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

/** Function Library **/ 
require "global.lib.php";


/**************************************************************************************
*
* [1] �ô�����Ʈ ������ ����� ���� ��ż��� IP/Port ��ȣ
*
* $IsDebug		: �ô�����Ʈ�� �ְ�޴� ������ �� �������� ��� (1:���, 0:�����(�⺻��)) 
* $LOCALADDR	: PG������ ����� ����ϴ� ��ȣȭProcess�� ��ġ�� �ִ� IP
* $LOCALPORT	: ��Ʈ
* $ENCRYPT		: 0:�Ƚ�Ŭ��,�Ϲݰ��� 2:ISP
* $CONN_TIMEOUT : ��ȣȭ ����� ���� ConnectŸ�Ӿƿ� �ð�(��)
* $READ_TIMEOUT : ������ ���� Ÿ�Ӿƿ� �ð�(��)
* 
***************************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.3";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;


/****************************************************************************
*
* [2] AGS_pay.html �� ���� �Ѱܹ��� ����Ÿ
*
****************************************************************************/

/*������*/
$AuthTy		= trim($_POST["AuthTy"]);			//��������
$SubTy 		= trim($_POST["SubTy"]);			//�����������
$StoreId 	= trim($_POST["StoreId"]);			//�������̵�
$OrdNo 		= trim($_POST["OrdNo"]);			//�ֹ���ȣ
$Amt 		= trim($_POST["Amt"]);					//�ݾ�
$UserEmail	= trim($_POST["UserEmail"]);		//�ֹ����̸���
$ProdNm 	= trim($_POST["ProdNm"]);			//��ǰ��

/*�ſ�ī��&������»��*/
$MallUrl 	= trim($_POST["MallUrl"]);			//MallUrl(�������Ա�) - ���� ������ ��������߰�
$UserId 	= trim($_POST["UserId"]);				//ȸ�����̵�


/*�ſ�ī����*/
$OrdNm 		= trim($_POST["OrdNm"]);			//�ֹ��ڸ�
$OrdPhone	= trim($_POST["OrdPhone"]);			//�ֹ��ڿ���ó
$OrdAddr 	= trim($_POST["OrdAddr"]);				//�ֹ����ּ� ��������߰�
$RcpNm 		= trim($_POST["RcpNm"]);			//�����ڸ�
$RcpPhone	= trim($_POST["RcpPhone"]);		//�����ڿ���ó
$DlvAddr	= trim($_POST["DlvAddr"]);				//������ּ�
$Remark 	= trim($_POST["Remark"]);				//���
$DeviId 	= trim($_POST["DeviId"]);					//�ܸ�����̵�
$AuthYn 	= trim($_POST["AuthYn"]);				//��������
$Instmt 	= trim($_POST["Instmt"]);						//�Һΰ�����
$UserIp 	= $_SERVER["REMOTE_ADDR"];			//ȸ�� IP

/*�ſ�ī��(ISP)*/
$partial_mm 		= trim($_POST["partial_mm"]);					//�Ϲ��ҺαⰣ
$noIntMonth 		= trim($_POST["noIntMonth"]);					//�������ҺαⰣ
$KVP_CURRENCY 		= trim($_POST["KVP_CURRENCY"]);	//KVP_��ȭ�ڵ�
$KVP_CARDCODE 		= trim($_POST["KVP_CARDCODE"]);	//KVP_ī����ڵ�
$KVP_SESSIONKEY 	= $_POST["KVP_SESSIONKEY"];		//KVP_SESSIONKEY
$KVP_ENCDATA 		= $_POST["KVP_ENCDATA"];			//KVP_ENCDATA
$KVP_CONAME 		= trim($_POST["KVP_CONAME"]);		//KVP_ī���
$KVP_NOINT 			= trim($_POST["KVP_NOINT"]);				//KVP_������=1 �Ϲ�=0
$KVP_QUOTA 			= trim($_POST["KVP_QUOTA"]);		//KVP_�Һΰ���

/*�ſ�ī��(�Ƚ�)*/
$CardNo 			= trim($_POST["CardNo"]);		//ī���ȣ
$MPI_CAVV 			= $_POST["MPI_CAVV"];		//MPI_CAVV
$MPI_ECI 			= $_POST["MPI_ECI"];			//MPI_ECI
$MPI_MD64 			= $_POST["MPI_MD64"];		//MPI_MD64

/*�ſ�ī��(�Ϲ�)*/
$ExpMon 	= trim($_POST["ExpMon"]);				//��ȿ�Ⱓ(��)
$ExpYear 	= trim($_POST["ExpYear"]);				//��ȿ�Ⱓ(��)
$Passwd 	= trim($_POST["Passwd"]);				//��й�ȣ
$SocId 		= trim($_POST["SocId"]);					//�ֹε�Ϲ�ȣ/����ڵ�Ϲ�ȣ

/*������ü���*/
$ICHE_OUTBANKNAME	= trim($_POST["ICHE_OUTBANKNAME"]);		//��ü�����
$ICHE_OUTACCTNO 	= trim($_POST["ICHE_OUTACCTNO"]);				//��ü���¹�ȣ
$ICHE_OUTBANKMASTER = trim($_POST["ICHE_OUTBANKMASTER"]);	//��ü���¼�����
$ICHE_AMOUNT 		= trim($_POST["ICHE_AMOUNT"]);					//��ü�ݾ�

/*�ڵ������*/
$HP_SERVERINFO 		= trim($_POST["HP_SERVERINFO"]);		//SERVER_INFO(�ڵ�������)
$HP_HANDPHONE 		= trim($_POST["HP_HANDPHONE"]);		//HANDPHONE(�ڵ�������)
$HP_COMPANY 		= trim($_POST["HP_COMPANY"]);			//COMPANY(�ڵ�������)
$HP_ID 				= trim($_POST["HP_ID"]);								//HP_ID(�ڵ�������)
$HP_SUBID 			= trim($_POST["HP_SUBID"]);					//HP_SUBID(�ڵ�������)
$HP_UNITType 		= trim($_POST["HP_UNITType"]);				//HP_UNITType(�ڵ�������)
$HP_IDEN 			= trim($_POST["HP_IDEN"]);							//HP_IDEN(�ڵ�������)
$HP_IPADDR 			= trim($_POST["HP_IPADDR"]);					//HP_IPADDR(�ڵ�������)

/*ARS���*/
$ARS_NAME 		= trim($_POST["ARS_NAME"]);						//ARS_NAME(ARS����)
$ARS_PHONE 		= trim($_POST["ARS_PHONE"]);				//ARS_PHONE(ARS����)

/*������»��*/
$VIRTUAL_CENTERCD	= trim($_POST["VIRTUAL_CENTERCD"]);	//�����ڵ�(�������)
$VIRTUAL_DEPODT 	= trim($_POST["VIRTUAL_DEPODT"]);		//�Աݿ�����(�������)
$ZuminCode 			= trim($_POST["ZuminCode"]);				//�ֹι�ȣ(�������)
$MallPage 			= trim($_POST["MallPage"]);				//���� ��/��� �뺸 ������(�������)
$VIRTUAL_NO 		= trim($_POST["VIRTUAL_NO"]);			//������¹�ȣ(�������)

/*�츮����ũ�λ��*/
$mTId 				= trim($_POST["mTId"]);					//�츮����ũ�� �ֹ���ȣ

/*����������ũ�λ��*/
$ES_SENDNO			= trim($_POST["ES_SENDNO"]);			//����������ũ��(����������ȣ)

$job		= trim($_POST["Job"]);			//������������
/****************************************************************************
*
* [3] ����Ÿ�� ��ȿ���� �˻��մϴ�.
*
****************************************************************************/
$ERRMSG = "";

if( empty( $StoreId ) || $StoreId == "" )
{
	$ERRMSG .= "�������̵� �Է¿��� Ȯ�ο�� <br>";		//�������̵�
}

if( empty( $OrdNo ) || $OrdNo == "" )
{
	$ERRMSG .= "�ֹ���ȣ �Է¿��� Ȯ�ο�� <br>";		//�ֹ���ȣ
}

if( empty( $ProdNm ) || $ProdNm == "" )
{
	$ERRMSG .= "��ǰ�� �Է¿��� Ȯ�ο�� <br>";			//��ǰ��
}

if( empty( $Amt ) || $Amt == "" )
{
	$ERRMSG .= "�ݾ� �Է¿��� Ȯ�ο�� <br>";			//�ݾ�
}

if( empty( $DeviId ) || $DeviId == "" )
{
	$ERRMSG .= "�ܸ�����̵� �Է¿��� Ȯ�ο�� <br>";	//�ܸ�����̵�
}

if( empty( $AuthYn ) || $AuthYn == "" )
{
	$ERRMSG .= "�������� �Է¿��� Ȯ�ο�� <br>";		//��������
}

if( strlen($ERRMSG) == 0 )
{
	/****************************************************************************
	* �� ���� ���� ������ ���� ���� ���� ����
	*
	* �� AuthTy  = "card"		�ſ�ī�����
	*	 - SubTy = "isp"			��������ISP
	*	 - SubTy = "visa3d"		�Ƚ�Ŭ��
	*	 - SubTy = "normal"		�Ϲݰ���
	*
	* �� AuthTy  = "iche"		�Ϲ�-������ü
	* 
	* �� AuthTy  = "eiche"		����ũ��-������ü
	*
	* �� AuthTy  = "virtual"		�Ϲ�-�������(�������Ա�)
	* 
	* �� AuthTy  = "evirtual"	����ũ��-�������(�������Ա�)
	* 
	* �� AuthTy  = "hp"			�ڵ�������
	*
	* �� AuthTy  = "ars"			ARS����
	*
	****************************************************************************/

	/****************************** ���� ���� �߰� *****************************/
	if(strlen(RootPath)>0) {
		$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$pathnum=@strpos($hostscript,RootPath);
		$shopurl=substr($hostscript,0,$pathnum).RootPath;
	} else {
		$shopurl=$_SERVER['HTTP_HOST']."/";
	}

	$return_host=$_SERVER['HTTP_HOST'];
	$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
	$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$OrdNo;

	$isreload=false;
	$tblname="";
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$OrdNo."' ";
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
		$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$OrdNo."' ";
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

	if( strcmp( $AuthTy, "card" ) == 0 )
	{
		if( strcmp( $SubTy, "isp" ) == 0 )
		{
			/****************************************************************************
			*
			* [4] �ſ�ī����� - ISP
			* 
			* -- �̺κ��� ���� ó���� ���� ��ȣȭProcess�� Socket����ϴ� �κ��̴�.
			* ���� �ٽ��� �Ǵ� �κ��̹Ƿ� �����Ŀ��� �׽�Ʈ�� �Ͽ��� �Ѵ�.
			* -- ������ ���̴� �Ŵ��� ����
			* 
			* -- ���� ��û ���� ����
			* + �����ͱ���(6) + ISP�����ڵ�(1) + ������
			* + ������ ����(������ ������ "|"�� �Ѵ�.)
			* ��������(6)		| ��üID(20)		| ȸ��ID(20)	 		| �����ݾ�(12)		| 
			* �ֹ���ȣ(40)	| �ܸ����ȣ(10)	| ������(40)			| ��������ȭ(21)		| 
			* �����(100)	| �ֹ��ڸ�(40)	| �ֹ��ڿ���ó(100)	| ��Ÿ�䱸����(350)	|
			* ��ǰ��(300)	| ��ȭ�ڵ�(3)	 	| �Ϲ��ҺαⰣ(2)		| �������ҺαⰣ(2)	| 
			* KVPī���ڵ�(22)	| ����Ű(256)	| ��ȣȭ������(2048) 	| ī���(50)	 		|
			* ȸ�� IP(20)	| ȸ�� Email(50)	|
			* 
			* -- ���� ���� ���� ����
			* + �����ͱ���(6) + ������
			* + ������ ����(������ ������ "|"�� �Ѵ�.
			* ��üID(20)		| �����ڵ�(4)		| �ŷ�������ȣ(6)		| ���ι�ȣ(8)		| 
			* �ŷ��ݾ�(12)	| ��������(1)	 	| ���л���(20)		| ���νð�(14)	| 
			* ī����ڵ�(4)	|
			*    
			* �� "|" ���� �����ʿ��� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
			*   ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
			****************************************************************************/
			
			$ENCTYPE = 2;
			
			/****************************************************************************
			* 
			* ���� ���� Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				"plug15"."|".
				$StoreId."|".
				$UserId."|".
				$Amt."|".
				$OrdNo."|".
				$DeviId."|".
				$RcpNm."|".
				$RcpPhone."|".
				$DlvAddr."|".
				$OrdNm."|".
				$OrdPhone."|".
				$Remark."|".
				$ProdNm."|".
				$KVP_CURRENCY."|".
				$partial_mm."|".
				$noIntMonth."|".
				$KVP_CARDCODE."|".
				$KVP_SESSIONKEY."|".
				$KVP_ENCDATA."|".
				$KVP_CONAME."|".
				$UserIp."|".
				$UserEmail."|";
	
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
			
			/****************************************************************************
			* 
			* ���� �޼��� ����Ʈ
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	
			/****************************************************************************
			* 
			* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT );
			
			if( !$fp )
			{
				/** ���� ���з� ���� ���ν��� �޼��� ���� **/
				
				$rSuccYn = "n";
				$rResMsg = "���� ���з� ���� ���ν���";
			}
			else 
			{
				/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/
				
				$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";
				
				
				/** ���� ������ ��ȣȭProcess�� ���� **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				*
				* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
				* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
				* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
				* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
				
				/** ���� close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* ���� �޼��� ����Ʈ
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** ���� ������(����) üũ ���� **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
			
				/** null �Ǵ� NULL ����, 0 �� �������� ��ȯ
				for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
				{
					$RecvValArray[$i] = trim( $RecvValArray[$i] );
					
					if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
					{
						$RecvValArray[$i] = "";
					}
					
					if( IsNumber( $RecvValArray[$i] ) )
					{
						if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
					}
				} **/
				
				$rStoreId = $RecvValArray[0];
				$rBusiCd = $RecvValArray[1];
				$rOrdNo = $OrdNo;
				$rDealNo = $RecvValArray[2];
				$rApprNo = $RecvValArray[3];
				$rProdNm = $ProdNm;
				$rAmt = $RecvValArray[4];
				$rInstmt = $KVP_QUOTA;
				$rSuccYn = $RecvValArray[5];
				$rResMsg = $RecvValArray[6];
				$rApprTm = $RecvValArray[7];
				$rCardCd = $RecvValArray[8];

				/****************************************************************************
				*
				* �ſ�ī�����(ISP) ����� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ��� 
				* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
				*
				* ���⼭ DB �۾��� �� �ּ���.
				* ����) $rSuccYn ���� 'y' �ϰ�� �ſ�ī����μ���
				* ����) $rSuccYn ���� 'n' �ϰ�� �ſ�ī����ν���
				* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�. 
				*
				****************************************************************************/

				/****************************** ���� ���� �߰� *****************************/
				if($isreload!=true) {
					$date=date("YmdHis");
					$paymethod="C";
					if ($rSuccYn == "y") {	//�������
						$PAY_FLAG="0000"; // ��� �ڵ�(0000:�������, 9999:���ν���)
						$DELI_GBN="N"; // ��ۿ���(N:��ó��, C:�ֹ����)
						$PAY_AUTH_NO=$rApprNo;
						$MSG1="������� - ���ι�ȣ : ".$rApprNo;
						$pay_data="���ι�ȣ : ".$rApprNo;

						$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
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
						'".$rOrdNo."', 
						'".$rDealNo."', 
						'".$pay_data."', 
						'C', 
						'Y', 
						'".$rApprTm."', 
						'".$rAmt."', 
						'N', 
						'".$paymethod."', 
						'".$date."', 
						'".$KVP_CONAME."', 
						'".($KVP_NOINT=="1"?"Y":"N")."', 
						'".$KVP_QUOTA."', 
						'".$_SERVER['REMOTE_ADDR']."', 
						'".$rProdNm."', 
						'".$MSG1."')";
						pmysql_query($sql,get_db_conn());
					} else {	//���ν���
						$PAY_FLAG="9999";
						$DELI_GBN="C";
						$PAY_AUTH_NO="";
						$MSG1=$rResMsg;
						$pay_data=$rResMsg;

						$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
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
						'".$rOrdNo."', 
						'".$rDealNo."', 
						'ERROR', 
						'C', 
						'N', 
						'".$rApprTm."', 
						'".$rAmt."', 
						'N', 
						'".$paymethod."', 
						'".$date."', 
						'".$KVP_CONAME."', 
						'".($KVP_NOINT=="1"?"Y":"N")."', 
						'".$KVP_QUOTA."', 
						'".$_SERVER['REMOTE_ADDR']."', 
						'".$rProdNm."', 
						'".$MSG1."')";
						pmysql_query($sql,get_db_conn());
						//backup_save_sql($sql);
					}
				}			
				$return_data="ordercode=".$rOrdNo."&real_price=".$rAmt."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
				$return_data2=str_replace("'","",$return_data);
				
				$sql = "INSERT INTO tblreturndata VALUES ('".$rOrdNo."','".date("YmdHis")."','".$return_data2."') ";
				pmysql_query($sql,get_db_conn());

				$temp = SendSocketPost($return_host,$return_script,$return_data);
				if($temp!="ok") {
					//error (���� �߼�)
					if(strlen(AdminMail)>0) {
						@mail(AdminMail,"[PG] ".$rOrdNo." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
					}
				} else {
					pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$rOrdNo."'",get_db_conn());
				}

				/****************************** ���� ���� �߰� �� *****************************/
			}
			else
			{
				/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/
				
				$rSuccYn = "n";
				$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
			}
		}
		else if( ( strcmp( $SubTy, "visa3d" ) == 0 ) || ( strcmp( $SubTy, "normal" ) == 0 ) )
		{
			/****************************************************************************
			* 
			* [5] �ſ�ī����� - VISA3D, �Ϲ�
			* 
			* -- �̺κ��� ���� ó���� ���� ��ȣȭProcess�� Socket����ϴ� �κ��̴�.
			* ���� �ٽ��� �Ǵ� �κ��̹Ƿ� �����Ŀ��� ���� ���������� ������ �׽�Ʈ�� �Ͽ��� �Ѵ�.
			* -- ������ ���̴� �Ŵ��� ����
			* 
			* -- ���� ��û ���� ����
			* + �����ͱ���(6) + ��ȣȭ����(1) + ������
			* + ������ ����(������ ������ "|"�� �ϸ� ī���ȣ,��ȿ�Ⱓ,��й�ȣ,�ֹι�ȣ�� ��ȣȭ�ȴ�.)
			* ��������(6)			| ��üID(20)					| ȸ��ID(20)			| �����ݾ�(12)	| �ֹ���ȣ(40)	|
			* �ܸ����ȣ(10)		| ī���ȣ(16)				| ��ȿ�Ⱓ(6)			| �ҺαⰣ(4)		| ��������(1)		| 
			* ī���й�ȣ(2)		| �ֹε�Ϲ�ȣ/����ڹ�ȣ(10)	| ������(40)			| ��������ȭ(21)	| �����(100)	|
			* �ֹ��ڸ�(40)		| �ֹ��ڿ���ó(100)			| ��Ÿ�䱸����(350)	| ��ǰ��(300)	|
			* 
			* -- ���� ���� ���� ����
			* + �����ͱ���(6) + ������
			* + ������ ����(������ ������ "|"�� �ϸ� ��ȣȭProcess���� �ص����� �ǵ����͸� �����ϰ� �ȴ�.
			* ��üID(20)		| �����ڵ�(4)		 | �ֹ���ȣ(40)	| ���ι�ȣ(8)		| �ŷ��ݾ�(12)  |
			* ��������(1)		| ���л���(20)	 | ī����(20) 	| ���νð�(14)	| ī����ڵ�(4)	|
			* ��������ȣ(15)	| ���Ի��ڵ�(4)	 | ���Ի��(20)	| ��ǥ��ȣ(6)		|
			* 
			* �� "|" ���� �����ʿ��� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
			*   ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
			****************************************************************************/
			
			$ENCTYPE = 0;
			
			/****************************************************************************
			* 
			* ���� ���� Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				"plug15"."|".
				$StoreId."|".
				$UserId."|".
				$Amt."|".
				$OrdNo."|".
				$DeviId."|".
				encrypt_aegis($CardNo)."|".
				encrypt_aegis($ExpYear.$ExpMon)."|".
				$Instmt."|".
				$AuthYn."|".
				encrypt_aegis($Passwd)."|".
				encrypt_aegis($SocId)."|".
				$RcpNm."|".
				$RcpPhone."|".
				$DlvAddr."|".
				$OrdNm."|".
				$UserIp.";".$OrdPhone."|".
				$UserEmail.";".$Remark."|".
				$ProdNm."|".
				$MPI_CAVV."|".
				$MPI_MD64."|".
				$MPI_ECI."|";
			
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
			
			/****************************************************************************
			* 
			* ���� �޼��� ����Ʈ
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	
			/****************************************************************************
			* 
			* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT );
			
			
			if( !$fp )
			{
				/** ���� ���з� ���� ���ν��� �޼��� ���� **/
				
				$rSuccYn = "n";
				$rResMsg = "���� ���з� ���� ���ν���";
			}
			else 
			{
				/** ���� ������ ��ȣȭProcess�� ���� **/
				
				fputs( $fp, $sSendMsg );
		
				socket_set_timeout($fp, $READ_TIMEOUT);
		
				/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

				/****************************************************************************
				*
				* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
				* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
				* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
				* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
				
				/** ���� close **/
				
				fclose( $fp );
			}
		
			/****************************************************************************
			* 
			* ���� �޼��� ����Ʈ
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** ���� ������(����) üũ ���� **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
			
				/** null �Ǵ� NULL ����, 0 �� �������� ��ȯ
				for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
				{
					$RecvValArray[$i] = trim( $RecvValArray[$i] );
					
					if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
					{
						$RecvValArray[$i] = "";
					}
					
					if( IsNumber( $RecvValArray[$i] ) )
					{
						if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
					}
				} **/
				
				$rStoreId = $RecvValArray[0];
				$rBusiCd = $RecvValArray[1];
				$rOrdNo = $RecvValArray[2];
				$rApprNo = $RecvValArray[3];
				$rInstmt = $Instmt;
				$rAmt = $RecvValArray[4];
				$rSuccYn = $RecvValArray[5];
				$rResMsg = $RecvValArray[6];
				$rCardNm = $RecvValArray[7];
				$rApprTm = $RecvValArray[8];
				$rCardCd = $RecvValArray[9];
				$rMembNo = $RecvValArray[10];
				$rAquiCd = $RecvValArray[11];
				$rAquiNm = $RecvValArray[12];
				$rBillNo = $RecvValArray[13];
				$rProdNm = $ProdNm;
				
				/****************************************************************************
				*
				* �ſ�ī�����(�Ƚ�Ŭ��, �Ϲݰ���) ����� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ��� 
				* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
				*
				* ���⼭ DB �۾��� �� �ּ���.
				* ����) $rSuccYn ���� 'y' �ϰ�� �ſ�ī����μ���
				* ����) $rSuccYn ���� 'n' �ϰ�� �ſ�ī����ν���
				* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�. 
				*
				****************************************************************************/

				/****************************** ���� ���� �߰� *****************************/
				if($isreload!=true) {
					$date=date("YmdHis");
					$paymethod="C";
					if ($rSuccYn == "y") {	//�������
						$PAY_FLAG="0000"; // ��� �ڵ�(0000:�������, 9999:���ν���)
						$DELI_GBN="N"; // ��ۿ���(N:��ó��, C:�ֹ����)
						$PAY_AUTH_NO=$rApprNo;
						$MSG1="������� - ���ι�ȣ : ".$rApprNo;
						$pay_data="���ι�ȣ : ".$rApprNo;

						$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
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
						'".$rOrdNo."', 
						'".$rApprNo."', 
						'".$pay_data."', 
						'C', 
						'Y', 
						'".$rApprTm."', 
						'".$rAmt."', 
						'N', 
						'".$paymethod."', 
						'".$date."', 
						'".$rCardNm."', 
						'".($KVP_NOINT=="1"?"Y":"N")."', 
						'".$KVP_QUOTA."', 
						'".$_SERVER['REMOTE_ADDR']."', 
						'".$rProdNm."', 
						'".$MSG1."')";
						pmysql_query($sql,get_db_conn());
					} else {	//���ν���
						$PAY_FLAG="9999";
						$DELI_GBN="C";
						$PAY_AUTH_NO="";
						$MSG1=$rResMsg;
						$pay_data=$rResMsg;

						$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
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
						'".$rOrdNo."', 
						'".$rBillNo."', 
						'ERROR', 
						'C', 
						'N', 
						'".$rApprTm."', 
						'".$rAmt."', 
						'N', 
						'".$paymethod."', 
						'".$date."', 
						'".$rCardNm."', 
						'".($KVP_NOINT=="1"?"Y":"N")."', 
						'".$KVP_QUOTA."', 
						'".$_SERVER['REMOTE_ADDR']."', 
						'".$rProdNm."', 
						'".$MSG1."')";
						pmysql_query($sql,get_db_conn());
						//backup_save_sql($sql);
					}
				}			
				$return_data="ordercode=".$rOrdNo."&real_price=".$rAmt."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
				$return_data2=str_replace("'","",$return_data);
				
				$sql = "INSERT INTO tblreturndata VALUES ('".$rOrdNo."','".date("YmdHis")."','".$return_data2."') ";
				pmysql_query($sql,get_db_conn());

				$temp = SendSocketPost($return_host,$return_script,$return_data);
				if($temp!="ok") {
					//error (���� �߼�)
					if(strlen(AdminMail)>0) {
						@mail(AdminMail,"[PG] ".$rOrdNo." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
					}
				} else {
					pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$rOrdNo."'",get_db_conn());
				}

				/****************************** ���� ���� �߰� �� *****************************/
			}
			else
			{
				/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/
				
				$rSuccYn = "n";
				$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
			}
		}
	}
	else if( strcmp( $AuthTy, "iche" ) == 0 )
	{
		/****************************************************************************
		* 
		* [6] ������ü�Ϸ�
		* 
		* ������ü�� �ô�����Ʈ �÷����ο��� ��ü�Ϸ��� ��������� ��ȯ�մϴ�.
		* �׷��Ƿ� ���� ������������ �ô�����Ʈ ���ϰ� ����� �ʿ䰡 �����ϴ�.
		* ������ü ��������� DB�� �����Ͻ÷��� �̺κп��� �۾��Ͻʽÿ�.
   		* 
		* ������ü�� ��������ʴ� ������ AGS_pay.html���� ���ҹ���� �� �ſ�ī��(����)���� ������ �����ñ� �ٶ��ϴ�.
		*  
		* �� "|" ���� �����ʿ��� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
		*   ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
		****************************************************************************/
	
		$rStoreId = $StoreId;
		$rOrdNo = $OrdNo;
		$rProdNm = $ProdNm;
		$rAmt = $Amt;
		
		/****************************************************************************
		*
		* ���⼭ DB �۾��� �� �ּ���.
		* ����) ������ü�� ��� ������ü�����ϰ�� �� �������� �����ϵ��� �Ǿ� �ֽ��ϴ�.
		* �� �ʼ�ó������ :	ICHE_OUTBANKNAME	: ��ü�����
		*					ICHE_OUTBANKMASTER	: ��ü���¿�����
		*					ICHE_AMOUNT			: ��ü�ݾ�
		*
		****************************************************************************/

		/****************************** ���� ���� �߰� *****************************/
		if($isreload!=true) {
			$date=date("YmdHis");
			$paymethod="V";
			
			$PAY_FLAG="0000";
			$DELI_GBN="N";
			$PAY_AUTH_NO="";
			$MSG1="�������";
			$pay_data=$ICHE_OUTBANKNAME." ".$ICHE_OUTACCTNO." (������:".$ICHE_OUTBANKMASTER.")";
			
			$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);

			$sql = "INSERT INTO tblptranslog(
			ordercode		,
			trans_code		,
			pay_data		,
			pgtype			,
			ok				,
			okdate			,
			price			,
			bank_name		,
			ip				,
			goodname		,
			msg				) VALUES (
			'".$rOrdNo."', 
			'', 
			'".$pay_data."', 
			'C', 
			'Y', 
			'".$date."', 
			'".$ICHE_AMOUNT."', 
			'".$ICHE_OUTBANKNAME."', 
			'".$_SERVER['REMOTE_ADDR']."', 
			'".$rProdNm."', 
			'".$MSG1."')";
			pmysql_query($sql,get_db_conn());
		}

		$return_data="ordercode=".$rOrdNo."&real_price=".$rAmt."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
		$return_data2=str_replace("'","",$return_data);
		$sql = "INSERT INTO tblreturndata VALUES ('".$rOrdNo."','".date("YmdHis")."','".$return_data2."') ";
		pmysql_query($sql,get_db_conn());

		$temp = SendSocketPost($return_host,$return_script,$return_data);
		if($temp!="ok") {
			//error (���� �߼�)
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"[PG] ".$rOrdNo." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
			}
		} else {
			pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$rOrdNo."'",get_db_conn());
		}

		/****************************** ���� ���� �߰� �� *****************************/
	}
	else if( strcmp( $AuthTy, "virtual" ) == 0 ) //��������߰�
	{
		/****************************************************************************
		*
		* [8] ������� ����
		* 
		* -- �̺κ��� ���� ó���� ���� ��ȣȭProcess�� Socket����ϴ� �κ��̴�.
		* ���� �ٽ��� �Ǵ� �κ��̹Ƿ� �����Ŀ��� �׽�Ʈ�� �Ͽ��� �Ѵ�.
		* -- ������ ���̴� �Ŵ��� ����
		* 
		* -- ���� ��û ���� ����
		* + �����ͱ���(6) + ��ȣȭ ����(1) + ������
		* + ������ ����(������ ������ "|"�� �Ѵ�.)
		* ��������(10)		| ��üID(20)		| �ֹ���ȣ(40)	 	| �����ڵ�(4)			| ������¹�ȣ(20) |
		* �ŷ��ݾ�(13)		| �Աݿ�����(8)	| �����ڸ�(20)		| �ֹι�ȣ(13)		| 
		* �̵���ȭ(21)		| �̸���(50)		| �������ּ�(100)		| �����ڸ�(20)		|
		* �����ڿ���ó(21)	| ������ּ�(100)	| ��ǰ��(100)		| ��Ÿ�䱸����(300)	| ���� ������(50)	 |	���� ������(100)|
		* 
		* -- ���� ���� ���� ����
		* + �����ͱ���(6) + ��ȣȭ ����(1) + ������
		* + ������ ����(������ ������ "|"�� �Ѵ�.
		* ��������(10)	| ��üID(20)		| ��������(14)	| ������¹�ȣ(20)	| ����ڵ�(1)		| ����޽���(100)	 | 
		*
		* ������� �Ϲ� : vir_n ��Ŭ�� : vir_u ����ũ�� : vir_s   
		* ������¹�ȣ �� ��ǰ�� �߰� 2005-11-10
		*
		* �� "|" ���� �����ʿ��� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
		*   ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
		*
		****************************************************************************/
		
		$ENCTYPE = "V";
		
		/****************************************************************************
		* 
		* ���� ���� Make
		* 
		****************************************************************************/
		
		$sDataMsg = $ENCTYPE.
			/* $AuthTy."|". */
			"vir_n|".
			$StoreId."|".
			$OrdNo."|".
			$VIRTUAL_CENTERCD."|".
			$VIRTUAL_NO."|". 
			$Amt."|".
			$VIRTUAL_DEPODT."|".
			$OrdNm."|".
			$ZuminCode."|".
			$OrdPhone."|".
			$UserEmail."|".
			$OrdAddr."|".
			$RcpNm."|".
			$RcpPhone."|".
			$DlvAddr."|".
			$ProdNm."|".
			$Remark."|".
			$MallUrl."|".
			$MallPage."|";
			
		$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
		
		/****************************************************************************
		* 
		* ���� �޼��� ����Ʈ
		* 
		****************************************************************************/
		
		if( $IsDebug == 1 )
		{
			print $sSendMsg."<br>";
		}
		
		/****************************************************************************
		* 
		* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
		* 
		****************************************************************************/
		
		$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT );
		
		if( !$fp )
		{
			/** ���� ���з� ���� ���ν��� �޼��� ���� **/
			
			$rSuccYn = "n";
			$rResMsg = "���� ���з� ���� ���ν���";
		}
		else 
		{
			/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/
			
			$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";
			
			/** ���� ������ ��ȣȭProcess�� ���� **/
			
			fputs( $fp, $sSendMsg );
			
			socket_set_timeout($fp, $READ_TIMEOUT);
			
			/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/
			
			$sRecvLen = fgets( $fp, 7 );
			$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
			/****************************************************************************
			*
			* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
			* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
			* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
			* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
			* $sRecvLen = fgets( $fp, 6 );
			* $sRecvMsg = fgets( $fp, $sRecvLen );
			*
			****************************************************************************/
			
			/** ���� close **/
			
			fclose( $fp );
		}
		
		/****************************************************************************
		* 
		* ���� �޼��� ����Ʈ
		* 
		****************************************************************************/
		
		if( $IsDebug == 1 )	
		{
			print $sRecvMsg."<br>";
		}
		
		if( strlen( $sRecvMsg ) == $sRecvLen )
		{
			/** ���� ������(����) üũ ���� **/
			
			$RecvValArray = array();
			$RecvValArray = explode( "|", $sRecvMsg );
			
			/** null �Ǵ� NULL ����, 0 �� �������� ��ȯ
			for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
			{
			$RecvValArray[$i] = trim( $RecvValArray[$i] );
			
			if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
			{
			$RecvValArray[$i] = "";
			}
			
			if( IsNumber( $RecvValArray[$i] ) )
			{
			if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
			}
			} **/
			
			$rAuthTy    = $RecvValArray[0];
			$rStoreId   = $RecvValArray[1];
			$rApprTm    = $RecvValArray[2];
			$rVirNo     = $RecvValArray[3];
			$rSuccYn    = $RecvValArray[4];
			$rResMsg    = $RecvValArray[5];
			
			if($rSuccYn == "y") {
				$rResMsg_exp = explode(":", $rResMsg);
				$ES_SENDNO = $rResMsg_exp[1]; // ��� �޼������� $ES_SENDNO �̾ƿ���
			} else {
				$ES_SENDNO = "";
			}
			
			$rOrdNo = $OrdNo;
			$rProdNm = $ProdNm;
			$rAmt = $Amt;
			$rOrdNm = $OrdNm;
			
			/****************************************************************************
			*
			* ������¹��� ����� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ��� 
			* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
			*
			* ���⼭ DB �۾��� �� �ּ���.
			* ����) $rSuccYn ���� 'y' �ϰ�� �Ϲݰ�����°������μ���
			* ����) $rSuccYn ���� 'n' �ϰ�� �Ϲݰ�����°������ν���
			* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�. 
			*
			****************************************************************************/


			/****************************** ���� ���� �߰� *****************************/
			if($isreload!=true) {
				$date=date("YmdHis");
				if(strlen($ES_SENDNO)>0 || $job == "onlyvirtualselfescrow") {
					$paymethod="Q";
				} else {
					$paymethod="O";
				}

				$PAY_AUTH_NO="";
				if ($rSuccYn == "y") {	//�������
					$PAY_FLAG="0000"; // ��� �ڵ�(0000:�������, 9999:���ν���)
					$DELI_GBN="N"; // ��ۿ���(N:��ó��, C:�ֹ����)
					$MSG1=$rResMsg;

					if($VIRTUAL_CENTERCD == "20")
						$pay_data="�츮����(20) ".$rVirNo." (������:������ȿ��)";
					else if($VIRTUAL_CENTERCD == "88")
						$pay_data="��������(88) ".$rVirNo." (������:������ȿ��)";

					$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
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
					'".$rOrdNo."', 
					'".$ES_SENDNO."', 
					'".$pay_data."', 
					'C', 
					'M', 
					'".$rApprTm."', 
					'".$rAmt."', 
					'N', 
					'".$paymethod."', 
					'".$rOrdNm."', 
					'".$rVirNo."', 
					'".$_SERVER['REMOTE_ADDR']."', 
					'".$rProdNm."', 
					'".$MSG1."')";
					pmysql_query($sql,get_db_conn());
				} else {	//���ν���
					$PAY_FLAG="9999";
					$DELI_GBN="C";
					$MSG1=$rResMsg;
					$pay_data=$rResMsg;
					
					$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
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
					'".$rOrdNo."', 
					'', 
					'ERROR', 
					'C', 
					'N', 
					'".$rApprTm."', 
					'".$rAmt."', 
					'N', 
					'".$paymethod."', 
					'".$rOrdNm."', 
					'".$rVirNo."', 
					'".$_SERVER['REMOTE_ADDR']."', 
					'".$rProdNm."', 
					'".$MSG1."')";
					pmysql_query($sql,get_db_conn());
				}
			}			
			$return_data="ordercode=".$rOrdNo."&real_price=".$rAmt."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
			$return_data2=str_replace("'","",$return_data);
			
			$sql = "INSERT INTO tblreturndata VALUES ('".$rOrdNo."','".date("YmdHis")."','".$return_data2."') ";
			pmysql_query($sql,get_db_conn());

			$temp = SendSocketPost($return_host,$return_script,$return_data);
			if($temp!="ok") {
				//error (���� �߼�)
				if(strlen(AdminMail)>0) {
					@mail(AdminMail,"[PG] ".$rOrdNo." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
				}
			} else {
				pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$rOrdNo."'",get_db_conn());
			}

			/****************************** ���� ���� �߰� �� *****************************/
		}
		else
		{
			/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/
			
			$rSuccYn = "n";
			$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
		}
	}
	else if( strcmp( $AuthTy, "hp" ) == 0 )
	{
		/****************************************************************************
		* 
		* [7] �ڵ��� ����
		*
		*  �ڵ��� ������ ��������ʴ� ������ AGS_pay.html���� ���ҹ���� �� �ſ�ī��(����)���� ������ �����ñ� �ٶ��ϴ�.
		* 
		*  �̺κ��� ���� ó���� ���� ��ȣȭProcess�� Socket����ϴ� �κ��̴�.
		*  ���� �ٽ��� �Ǵ� �κ��̹Ƿ� �����Ŀ��� �׽�Ʈ�� �Ͽ��� �Ѵ�.
		*  -- ���� ��û ���� ����
		*  + �����ͱ���(6) + �ڵ��������ڵ�(1) + ������
		*  + ������ ����(������ ������ "|"�� �Ѵ�.)
		* 
		*  -- ���� ���� ���� ����
		*  + �����ͱ���(6) + ������
		*  + ������ ����(������ ������ "|"�� �Ѵ�.
		*
		*  �� "|" ���� �����ʿ��� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
		*    ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
		****************************************************************************/
			
		$ENCTYPE = h;
		$StrSubTy = Bill;
		
		/****************************************************************************
		* 
		* ���� ���� Make
		* 
		****************************************************************************/
		
		$sDataMsg = $ENCTYPE.
			$StrSubTy."|".
			$StoreId."|".
			$HP_SERVERINFO."|".
			$HP_ID."|".
			$HP_SUBID."|".
			$OrdNo."|".
			$Amt."|".
			$HP_UNITType."|".
			$HP_HANDPHONE."|".
			$HP_COMPANY."|".
			$HP_IDEN."|".
			$UserId."|".
			$UserEmail."|".
			$HP_IPADDR."|".
			$ProdNm."|";

		$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

		/****************************************************************************
		* 
		* ���� �޼��� ����Ʈ
		* 
		****************************************************************************/
		
		if( $IsDebug == 1 )
		{
			print $sSendMsg."<br>";
		}

		/****************************************************************************
		* 
		* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
		* 
		****************************************************************************/
		
		$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT );
		
		
		if( !$fp )
		{
			/** ���� ���з� ���� ���ν��� �޼��� ���� **/
			
			$rSuccYn = "n";
			$rResMsg = "���� ���з� ���� ���ν���";
		}
		else 
		{
			/** ���� ������ ��ȣȭProcess�� ���� **/
			
			fputs( $fp, $sSendMsg );
			
			socket_set_timeout($fp, $READ_TIMEOUT);
			
			/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/
			
			$sRecvLen = fgets( $fp, 7 );
			$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
		
			/****************************************************************************
			*
			* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
			* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
			* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
			* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
			* $sRecvLen = fgets( $fp, 6 );
			* $sRecvMsg = fgets( $fp, $sRecvLen );
			*
			****************************************************************************/
	
			/** ���� close **/
			
			fclose( $fp );
		}
		
		/****************************************************************************
		* 
		* ���� �޼��� ����Ʈ
		* 
		****************************************************************************/
		
		if( $IsDebug == 1 )	
		{
			print $sRecvMsg."<br>";
		}
		
		if( strlen( $sRecvMsg ) == $sRecvLen )
		{
			/** ���� ������(����) üũ ���� **/
			
			$RecvValArray = array();
			$RecvValArray = explode( "|", $sRecvMsg );
		
			/** null �Ǵ� NULL ����, 0 �� �������� ��ȯ
			for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
			{
				$RecvValArray[$i] = trim( $RecvValArray[$i] );
				
				if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
				{
					$RecvValArray[$i] = "";
				}
				
				if( IsNumber( $RecvValArray[$i] ) )
				{
					if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
				}
			} **/
			
			$rStoreId = $RecvValArray[0];	
			$rSuccYn = $RecvValArray[1];
			$rResMsg = $RecvValArray[2];
			$rHP_DATE = $RecvValArray[3];
			$rHP_TID = $RecvValArray[4];
			$rAmt = $Amt;
			$rOrdNo = $OrdNo;
			$rProdNm = $ProdNm;
			
			/****************************************************************************
			*
			* �ڵ������� ����� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ��� 
			* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
			*
			* ���⼭ DB �۾��� �� �ּ���.
			* ����) $rSuccYn ���� 'y' �ϰ�� �ڵ����������μ���
			* ����) $rSuccYn ���� 'n' �ϰ�� �ڵ����������ν���
			* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�. 
			*
			****************************************************************************/

			/****************************** ���� ���� �߰� *****************************/
			if($isreload!=true) {
				$date=date("YmdHis");
				$paymethod="M";
				$PAY_AUTH_NO="";
				if ($rSuccYn == "y") {	//�������
					$PAY_FLAG="0000"; // ��� �ڵ�(0000:�������, 9999:���ν���)
					$DELI_GBN="N"; // ��ۿ���(N:��ó��, C:�ֹ����)
					$MSG1=$rResMsg;
					$pay_data="HP ���� TID : ".$rHP_TID."(".$HP_COMPANY.":".$HP_HANDPHONE.")";

					$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "INSERT INTO tblpmobilelog(
					ordercode		,
					trans_code		,
					pay_data		,
					pgtype			,
					ok				,
					okdate			,
					price			,
					ip				,
					goodname		,
					msg				) VALUES (
					'".$rOrdNo."', 
					'".$rHP_TID."', 
					'".$pay_data."', 
					'C', 
					'M', 
					'".$rHP_DATE."000000', 
					'".$rAmt."', 
					'".$_SERVER['REMOTE_ADDR']."', 
					'".$rProdNm."', 
					'".$MSG1."')";
					pmysql_query($sql,get_db_conn());
				} else {	//���ν���
					$PAY_FLAG="9999";
					$DELI_GBN="C";
					$MSG1=$rResMsg;
					$pay_data=$rResMsg;
					
					$sql = "INSERT INTO tblpordercode VALUES ('".$rOrdNo."','".$paymethod."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "INSERT INTO tblpmobilelog(
					ordercode		,
					trans_code		,
					pay_data		,
					pgtype			,
					ok				,
					okdate			,
					price			,
					ip				,
					goodname		,
					msg				) VALUES (
					'".$rOrdNo."', 
					'".$rHP_TID."', 
					'ERROR', 
					'C', 
					'N', 
					'".$rHP_DATE."000000', 
					'".$rAmt."', 
					'".$_SERVER['REMOTE_ADDR']."', 
					'".$rProdNm."', 
					'".$MSG1."')";
					pmysql_query($sql,get_db_conn());
				}
			}			
			$return_data="ordercode=".$rOrdNo."&real_price=".$rAmt."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG."&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$MSG1;
			$return_data2=str_replace("'","",$return_data);
			
			$sql = "INSERT INTO tblreturndata VALUES ('".$rOrdNo."','".date("YmdHis")."','".$return_data2."') ";
			pmysql_query($sql,get_db_conn());

			$temp = SendSocketPost($return_host,$return_script,$return_data);
			if($temp!="ok") {
				//error (���� �߼�)
				if(strlen(AdminMail)>0) {
					@mail(AdminMail,"[PG] ".$rOrdNo." �������� ������Ʈ ����","$return_host<br>$return_script<br>$return_data");
				}
			} else {
				pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$rOrdNo."'",get_db_conn());
			}

			/****************************** ���� ���� �߰� �� *****************************/

		}
		else
		{
			/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/
			
			$rSuccYn = "n";
			$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
		}
	}
	
	//ī�� ������ ��°��� ������ �����̺��� �̾ƿ��� ���ؼ� ���̺� ����
	if($rSuccYn!='n' && ( strcmp( $AuthTy, "hp" ) == 0 || strcmp( $AuthTy, "card" ) == 0)){
		$qry="insert into tbl_card_estimate(pay_data,ordercode)values('".$sRecvMsg."','".$rOrdNo."')";
		pmysql_query($qry);
	}
	
}
else
{
	$rSuccYn = "n";
	$rResMsg = $ERRMSG;
}
echo "<script>";
echo "opener.location.href=\"http://".$return_resurl."\";\n";
echo "window.close();";
echo "</script>";
exit;
