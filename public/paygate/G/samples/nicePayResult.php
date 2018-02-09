<?php

	/**************************
	 * 1. ���̺귯�� ��Ŭ��� *
	 **************************/
	require("../lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite Ŭ������ �ν��Ͻ� ���� *
	 ***************************************/
	$nicepay = new NicepayLite;

	//�α׸� ������ ���丮�� �����Ͻʽÿ�. 
	$nicepay->m_NicepayHome = "./log";	
	
	/**************************************
	* 3. ���� ��û �Ķ���� ����	      *
	***************************************/
	$nicepay->m_GoodsName = $GoodsName;
	$nicepay->m_GoodsCnt = $m_GoodsCnt;
	$nicepay->m_Price = $Amt;
	$nicepay->m_Moid = $Moid;
	$nicepay->m_BuyerName = $BuyerName;
	$nicepay->m_BuyerEmail = $BuyerEmail;
	$nicepay->m_BuyerTel = $BuyerTel;
	$nicepay->m_MallUserID = $MallUserID;
	$nicepay->m_GoodsCl = $GoodsCl; 
	$nicepay->m_MID = $MID;
	$nicepay->m_MallIP = $MallIP;
	$nicepay->m_TrKey = $TrKey;
	$nicepay->m_EncryptedData = $EncryptedData;
	$nicepay->m_PayMethod = $PayMethod;
	$nicepay->m_TransType = $TransType;
	$nicepay->m_ActionType = "PYO";

	// ����Ű�� �����Ͽ� �ֽʽÿ�.
	$nicepay->m_LicenseKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
    
	
	$nicepay->m_NetCancelAmt = $Amt; //���� �ݾ׿� �°� ���� 
	$nicepay->m_NetCancelPW = "123456";	// ���� ��� �н����� ����
		
	$nicepay->m_ssl = "true";

	// PG�� �����Ͽ� ���� ó���� ����.
	$nicepay->startAction();
	
	/**************************************
	* 4. ���� ���					      *
	***************************************/	
	$resultCode = $nicepay->m_ResultData["ResultCode"];	// ��� �ڵ�


	$paySuccess = false;		// ���� ���� ����
	if($PayMethod == "CARD"){	//�ſ�ī��
		if($resultCode == "3001") $paySuccess = true;	// ����ڵ� (���� :3001 , �� �� ����)
	}else if($PayMethod == "BANK"){		//������ü
		if($resultCode == "4000") $paySuccess = true;	// ����ڵ� (���� :4000 , �� �� ����)
	}else if($PayMethod == "CELLPHONE"){			//�޴���
		if($resultCode == "A000") $paySuccess = true;	//����ڵ� (���� : A000, �� �� ������)
	}else if($PayMethod == "VBANK"){		//�������
		if($resultCode == "4100") $paySuccess = true;	// ����ڵ� (���� :4100 , �� �� ����)
	}

	if($paySuccess == true){
	   // ���� ������ DBó�� �ϼ���.
	}else{
	   // ���� ���н� DBó�� �ϼ���.
	}
	
?>	
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: ���� ��û ���</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<br>
<table width="632" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
  	<td >
  	  <table width="632" border="0" cellspacing="0" cellpadding="0" class="title">
        <tr>
          <td width="35">&nbsp;</td>
          <td>���� ��û ���</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" background="images/bodyMiddle.gif"><table width="632" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35" height="10">&nbsp;</td> <!--��ܿ��� ���� 10px -->
        <td width="562">&nbsp;</td>
        <td width="35">&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
        <td>���� ��û�� �Ϸ�Ǿ����ϴ�.
        </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--�������� ������ ���� ���� 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> ���� �����Դϴ�.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead01">��� ����</td> 
            <td id="borderBottom" >&nbsp;[<?=$nicepay->m_ResultData["ResultCode"]?>] <?=$nicepay->m_ResultData["ResultMsg"]?></td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead02" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead02">���� ����</td> 
            <td id="borderBottom" >&nbsp;<?=$PayMethod ?></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">��ǰ��</td> 
            <td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["GoodsName"]?></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">�ݾ�</td> 
            <td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["Amt"]?> ��</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">�ŷ����̵�</td> 
            <td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["TID"]?></td>
          </tr>
			 <?if($PayMethod == "CARD"){?>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">ī����ڵ�</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["CardCode"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">ī����</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["CardName"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">�Һΰ���</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["CardQuota"]?>&nbsp;</td>
			  </tr>
		   <?}else if($PayMethod == "BANK"){?>
		      <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">���� �ڵ�</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["BankCode"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">�����</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["BankName"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">���ݿ����� Ÿ��</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["RcptType"]?>&nbsp;&nbsp;(0:�������,1:�ҵ����,2:��������)</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">���ݿ����� ���ι�ȣ</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["RcptAuthCode"]?>&nbsp;</td>
			  </tr>
		  <?}else if($PayMethod== "CELLPHONE"){?>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">����� ����</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["Carrier"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">�޴��� ��ȣ</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["DstAddr"]?>&nbsp;</td>
			  </tr>
		  <?}else if($PayMethod == "VBANK"){?>
		       <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">�Ա� ���� �ڵ�</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankBankCode"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">�Ա� �����</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankBankName"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">�Ա� ����</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankNum"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">�Ա� ����</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankExpDate"]?>&nbsp;</td>
			  </tr>

		  <?}?>


        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
        <td height="15"></td>  <!--�������� ������ ���� ���� 15px-->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">�׽�Ʈ ���̵��ΰ�� ���� ���� 11�� 30�п� ��ҵ˴ϴ�.        
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="10"></td>  <!--�ϴܿ��� ���� 10px -->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>  
    </table></td>
  </tr>
  <tr>
    <td><img src="images/bodyBottom.gif" /></td>
  </tr>
</table>
</body>
</html>

	
