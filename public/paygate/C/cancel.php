<?php
/*
�ſ�ī��/�ڵ��� ���ó��
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");
$AuthTy = "cancel";  // �ô�����Ʈ ��� �Ķ���� ����
$StoreId=$_POST["storeid"];  // �ô�����Ʈ ��� �Ķ���� ����

$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

$DealNo="";
$ApprNo="";
$SubTy="";
$ApprTm="";

if (empty($StoreId)) {
	alert_go('AllTheGate ����ID�� �����ϴ�.',-1);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	alert_go(get_message("�ش� ���ΰ��� �������� �ʽ��ϴ�."),-1);
}
pmysql_free_result($result);

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
	} else {
		if($paymethod == "C") {
			$DealNo = $row->trans_code;
			$pay_data_exp = explode(" : ",$row->pay_data);
			$ApprNo = $pay_data_exp[1];

			if($ApprNo == $DealNo) {
				$SubTy="visa3d";
				$ApprTm = $row->okdate;
			} else {
				$SubTy="isp";
				$ApprTm = substr($row->okdate,0,8);
			}
		} else if($paymethod == "M") {
			$SubTy="hp";
		}
	}
} else {
	alert_go(get_message("�ش� ���ΰ��� �������� �ʽ��ϴ�."),-1);
}
pmysql_free_result($result);

/**********************************************************************************************
*
* ���ϸ� : AGS_cancel_ing.php
* �ۼ����� : 2007/04/25
* 
* �ô�����Ʈ �÷����ο��� ���ϵ� ����Ÿ�� �޾Ƽ� ������ҿ�û�� �մϴ�.
*
* Copyright 2006-2007 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/ 

/** Function Library **/ 
require "global.lib.php";


/****************************************************************************
*
* [1] �ô�����Ʈ ������ ����� ���� ��ż��� IP/Port ��ȣ
*
* $IsDebug : 1:����,���� �޼��� Print 0:������
* $LOCALADDR : PG������ ����� ����ϴ� ��ȣȭProcess�� ��ġ�� �ִ� IP 
* $LOCALPORT : ��Ʈ
* $ENCRYPT : 0:�Ƚ�Ŭ��,�Ϲݰ��� 2:ISP
* $CONN_TIMEOUT : ��ȣȭ ����� ���� ConnectŸ�Ӿƿ� �ð�(��)
* $READ_TIMEOUT : ������ ���� Ÿ�Ӿƿ� �ð�(��)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.3";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;

if( strcmp( $SubTy, "isp" ) == 0 )
{
	/****************************************************************************
	*
	* [3] �ſ�ī�������� - ISP
	*
	* -- �̺κ��� ��� ���� ó���� ���� PG����Process�� Socket����ϴ� �κ��̴�.
	* ���� �ٽ��� �Ǵ� �κ��̹Ƿ� �����Ŀ��� ���� ���������� ������ �׽�Ʈ�� �Ͽ��� �Ѵ�.
	* -- ������ ���̴� �Ŵ��� ����
	*	    
	* -- ��� ���� ��û ���� ����
	* + �����ͱ���(6) + ��ȣȭ����(1) + ������
	* + ������ ����(������ ������ "|"�� �Ѵ�.
	* ��������(6)	| ��ü���̵�(20) 	| ���ι�ȣ(20) 	| ���νð�(8)	| �ŷ�������ȣ(6) |
	*
	* -- ��� ���� ���� ���� ����
	* + �����ͱ���(6) + ������
	* + ������ ����(������ ������ "|"�� �Ѵ�.
	* ��üID(20)	| ���ι�ȣ(20)	| ���νð�(8)	| �����ڵ�(4)	| �ŷ�������ȣ(6)	| ��������(1)	|
	*		   
	****************************************************************************/
	
	$ENCTYPE = 2;
	
	/****************************************************************************
	* 
	* ���� ���� Make
	* 
	****************************************************************************/
		
	$sDataMsg = $ENCTYPE.
		$AuthTy."|".
		$StoreId."|".
		$ApprNo."|".
		$ApprTm."|".
		$DealNo."|";

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
		alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $rResMsg"),-1);
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
		$rApprNo = $RecvValArray[1];
		$rApprTm = $RecvValArray[2];
		$rBusiCd = $RecvValArray[3];
		$rDealNo = $RecvValArray[4];
		$rSuccYn = $RecvValArray[5];
		$rResMsg = $RecvValArray[6];
		
		/****************************************************************************
		*
		* �ſ�ī�����(ISP) ��Ұ���� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ��� 
		* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
		*
		* ���⼭ DB �۾��� �� �ּ���.
		* ����) $rSuccYn ���� 'y' �ϰ�� �ſ�ī����Ҽ���
		* ����) $rSuccYn ���� 'n' �ϰ�� �ſ�ī����ҽ���
		* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�. 
		*
		****************************************************************************/
	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/
		
		$rSuccYn = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
		alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $rResMsg"),-1);
	}
}
else if( ( strcmp( $SubTy, "visa3d" ) == 0 ) || ( strcmp( $SubTy, "normal" ) == 0 ) )
{
	/****************************************************************************
	*
	* [4] �ſ�ī�������� - VISA3D, �Ϲ�
	*
	* -- �̺κ��� ��� ���� ó���� ���� ��ȣȭProcess�� Socket����ϴ� �κ��̴�.
	* ���� �ٽ��� �Ǵ� �κ��̹Ƿ� �����Ŀ��� ���� ���������� ������ �׽�Ʈ�� �Ͽ��� �Ѵ�.
	*
	* -- ��� ���� ��û ���� ����
	* + �����ͱ���(6) + ��ȣȭ����(1) + ������
	* + ������ ����(������ ������ "|"�� �ϸ� ī���ȣ,��ȿ�Ⱓ,��й�ȣ,�ֹι�ȣ�� ��ȣȭ�ȴ�.)
	* ��������(6)	| ��ü���̵�(20) 	| ���ι�ȣ(8) 	| ���νð�(14) 	| ī���ȣ(16) 	|
	*
	* -- ��� ���� ���� ���� ����
	* + �����ͱ���(6) + ������
	* + ������ ����(������ ������ "|"�� �ϸ� ��ȣȭProcess���� �ص����� �ǵ����͸� �����ϰ� �ȴ�.
	* ��üID(20)	| ���ι�ȣ(8)	| ���νð�(14)	| �����ڵ�(4)	| ��������(1)	|
	* �ֹ���ȣ(20)	| �Һΰ���(2)	| �����ݾ�(20)	| ī����(20)	| ī����ڵ�(4) 	|
	* ��������ȣ(15)	| ���Ի��ڵ�(4)	| ���Ի��(20)	| ��ǥ��ȣ(6)
	*		   
	****************************************************************************/
	
	$ENCTYPE = 0;
	
	/****************************************************************************
	* 
	* ���� ���� Make
	* 
	****************************************************************************/
	
	$sDataMsg = $ENCTYPE.
		$AuthTy."|".
		$StoreId."|".
		$ApprNo."|".
		$ApprTm."|".
		encrypt_aegis($CardNo)."|";

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
		alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $rResMsg"),-1);
		
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
		$rApprNo = $RecvValArray[1];
		$rApprTm = $RecvValArray[2];
		$rBusiCd = $RecvValArray[3];
		$rSuccYn = $RecvValArray[4];
		$rOrdNo = $RecvValArray[5];
		$rInstmt = $RecvValArray[6];
		$rAmt = $RecvValArray[7];
		$rCardNm = $RecvValArray[8];
		$rCardCd = $RecvValArray[9];
		$rMembNo = $RecvValArray[10];
		$rAquiCd = $RecvValArray[11];
		$rAquiNm = $RecvValArray[12];
		$rBillNo = $RecvValArray[13];
		
		/****************************************************************************
		*
		* �ſ�ī�����(�Ƚ�Ŭ��, �Ϲݰ���) ��Ұ���� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ��� 
		* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
		*
		* ���⼭ DB �۾��� �� �ּ���.
		* ����) $rSuccYn ���� 'y' �ϰ�� �ſ�ī����Ҽ���
		* ����) $rSuccYn ���� 'n' �ϰ�� �ſ�ī����ҽ���
		* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�. 
		*
		****************************************************************************/
	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/
		
		$rSuccYn = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
		alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $rResMsg"),-1);
	}
	
} else if( strcmp( $SubTy, "hp" ) == 0 ) {
	$rSuccYn = "y";
} else {
	$rSuccYn = "n";
	$rResMsg = "���� ������������ ī�����, �޴������� �ǿ����� ��Ұ� �����մϴ�.";
	alert_go(get_message("$rResMsg"),-1);
}

if($rSuccYn=="y") {
	//������Ʈ
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "canceldate	= '".date("YmdHis")."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	if (pmysql_errno()) {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$tblname." ��� update ����!","$sql - ".pmysql_error());
		}
		alert_go(get_message("��Ҵ� ���� ó���Ǿ����� ����DB�� �ݿ��� �ȵǾ����ϴ�.\\n\\n�����ڿ��� �����Ͻñ� �ٶ��ϴ�."),-1);
	}

	if(strcmp( $SubTy, "hp" ) == 0) {		
		echo "<script>alert('".get_message("���θ� DB�� ���ó���� ���������� �Ϸ� �Ǿ����ϴ�.")."');</script>\n";
	} else {
		echo "<script>alert('".get_message("������Ұ� ���������� ó���Ǿ����ϴ�.\\n\\nAllTheGate �������������� ��ҿ��θ� �� Ȯ���Ͻñ� �ٶ��ϴ�.")."');</script>\n";
	}
} else {
	alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $rResMsg"),-1);
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
