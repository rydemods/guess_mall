<?php
	/**************************
	 * 1. ���̺귯�� ��Ŭ��� *
	 **************************/
	require("../lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite Ŭ������ �ν��Ͻ� ���� *
	 ***************************************/
	$nicepay = new NicepayLite;
	 
	// �α׸� �����Ͽ� �ֽʽÿ�.
	$nicepay->m_NicepayHome = "./log";	
	
	$nicepay->m_ssl = "true";	

	$nicepay->m_ActionType = "CLO";							// ��� ��û ����
	$nicepay->m_CancelAmt = $CancelAmt;						// ��� �ݾ� ����
	$nicepay->m_TID = $TID;									// ��� TID ����
	$nicepay->m_CancelMsg = $CancelMsg;						// ��� ����
	$nicepay->m_PartialCancelCode = $PartialCancelCode;		// ��ü ���, �κ� ��� ���� ����
	$nicepay->m_CancelPwd = "123456";						// ��� ��й�ȣ ����
		
	// PG�� �����Ͽ� ��� ó���� ����.
	//	��Ҵ� 2001 �Ǵ� 2211�� �����Դϴ�.
	$nicepay->startAction();
	

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: ��� ���</title>
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
          <td>��� ���</td>
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
        <td>��� ��û�� �Ϸ�Ǿ����ϴ�.
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
        <td class="bold"><img src="images/bullet.gif" /> ��ҳ����� Ȯ���ϼ���.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead01">�ŷ� ���̵�</td> 
            <td id="borderBottom" ><? echo($nicepay->m_ResultData["TID"]);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead02">�ŷ� �ð�</td> 
            <td id="borderBottom" ><? echo($nicepay->m_ResultData["CancelTime"]);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // ¦���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead01">��� ����</td> 
            <td id="borderBottom" ><? echo('['.$nicepay->m_ResultData["ResultCode"].'] '.$nicepay->m_ResultData["ResultMsg"]);?>&nbsp;</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">��� �ݾ�</td> 
            <td id="borderBottom" ><? echo($nicepay->m_ResultData["CancelAmt"]);?>&nbsp;</td>
          </tr>
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
        <td class="comment">��Ұ� ������ ��쿡�� �ٽ� ���λ��·� ���� �� �� �����ϴ�..        
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
