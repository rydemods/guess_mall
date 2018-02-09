<?php
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayWEB.php';
require_once dirname(__FILE__).'/lib/nicepay/core/Constants.php';
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';

/** 1. Request Wrapper Ŭ������ ����Ѵ�.  */ 
$httpRequestWrapper = new NicePayHttpServletRequestWrapper($_REQUEST);
$_REQUEST = $httpRequestWrapper->getHttpRequestMap();

/** 2. ���� ����Ϳ� �����ϴ� Web �������̽� ��ü�� �����Ѵ�.*/
$nicepayWEB = new NicePayWEB();

/** 2-1. �α� ���丮 ���� */
$nicepayWEB->setParam("NICEPAY_LOG_HOME","C:\log");

/** 2-2. �α� ��� ����(0: DISABLE, 1: ENABLE) */
$nicepayWEB->setParam("APP_LOG","1");

/** 2-3. ��ȣȭ�÷��� ����(N: ��, S:��ȣȭ) */
$nicepayWEB->setParam("EncFlag","S");

/** 2-4. ���񽺸�� ����(���� ���� : PY0 , ��� ���� : CL0) */
$nicepayWEB->setParam("SERVICE_MODE", "PY0");

/** 2-5. ��ȭ���� ����(���� KRW(��ȭ) ����)  */
$nicepayWEB->setParam("Currency", "KRW");


$nicepayWEB->setParam("PayMethod",'CASHRCPT');


/** 2-7 ���̼���Ű ���� 
	���� ID�� �´� ����Ű�� �����Ͻʽÿ�.
	*/
$nicepayWEB->setParam("LicenseKey","YmbbO3a5I3Oo+rKNUNHXtYTUAbeeM939ytI4PUh6IkVOMSngSL/LykbYSsnBE2gAp9tHWNLTb1xHak0nyar1xA==");


/** 3. ���� ��û */
$responseDTO = $nicepayWEB->doService($_REQUEST);


/** 4. ���� ��� */
$resultCode = $responseDTO->getParameter("ResultCode"); // ����ڵ� (���� :3001 , �� �� ����)
$resultMsg = $responseDTO->getParameter("ResultMsg");   // ����޽���
$authDate = $responseDTO->getParameter("AuthDate");   // �����Ͻ� YYMMDDHH24mmss
$tid = $responseDTO->getParameter("TID");  // �ŷ�ID
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: ���ݿ����� �߱� ���</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />

<script language="javascript">
<!--
function viewReceipt(TID) {
	
	 var status = "toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=414,height=622";
     window.open("https://pg.nicepay.co.kr/issue/IssueLoader.jsp?TID="+TID+"&type=1","popupIssue",status);
    
}

-->
</script>

</head>
<body>
<br>
<table width="632" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
  	<td >
  	  <table width="632" border="0" cellspacing="0" cellpadding="0" class="title">
        <tr>
          <td width="35">&nbsp;</td>
          <td>���ݿ����� �߱� ���</td>
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
        <td>���ݿ����� �߱� ��û�� �Ϸ�Ǿ����ϴ�.
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
        <td class="bold"><img src="images/bullet.gif" /> ���ݿ����� �߱� ������ Ȯ���ϼ���.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead01">�ŷ� ���̵�</td> 
            <td id="borderBottom" ><? echo($tid);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead02">�߱� �ð�</td> 
            <td id="borderBottom" ><? echo($authDate);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // ¦���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead01">��� ����</td> 
            <td id="borderBottom" ><? echo('['.$resultCode.'] '.$resultMsg);?>&nbsp;</td>
          </tr>
          
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
        <td height="60"></td> 
        <td class="btnCenter"><input type="button" value="��ǥ���" onClick="viewReceipt('<? echo($tid);?>');"></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">���ݿ����� �߱�  ���� ���Ŀ� ��üû ���ݿ����� Site���� �߱� ������ Ȯ���Ͻ� �� �ֽ��ϴ�.         
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