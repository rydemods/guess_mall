<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: ���ݿ����� �߱޿�û</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script language="javascript">
<!--
function goReceipt() {
	
	var formNm = document.tranMgr;
	formNm.submit();
	
}

-->
</script>
</head>
<body>
<br>
<form name="tranMgr" method="post" action="niceReceiptResult.php">
<table width="632" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
  	<td >
  	  <table width="632" border="0" cellspacing="0" cellpadding="0" class="title">
        <tr>
          <td width="35">&nbsp;</td>
          <td>���ݿ����� �߱޿�û</td>
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
        <td>���ݿ����� �߱޿�û �����Դϴ�. </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--�������� ������ ���� ���� 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> ������ �����Ͻ� �� �߱޹�ư�� �����ֽʽÿ�.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ��ǰ��</td> 
            <td id="borderBottom" ><input name="GoodsName" type="text" value="����������" /></td>
          </tr>
		  <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* �ֹ���ȣ</td> 
            <td id="borderBottom" ><input name="Moid" type="text" value="Moid12345" /></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* �����ڸ�</td> 
            <td id="borderBottom" ><input name="BuyerName" type="text" value="ȫ�浿" /></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* ���ݿ����� ��û�ݾ�</td> 
            <td id="borderBottom" ><input name="ReceiptAmt" type="text" value="1000"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ���ް���</td> 
            <td id="borderBottom" ><input name="ReceiptSupplyAmt" type="text" value="900"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* �ΰ���ġ��</td> 
            <td id="borderBottom" ><input name="ReceiptVAT" type="text" value="100"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* �����</td> 
            <td id="borderBottom" ><input name="ReceiptServiceAmt" type="text" value="0"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* ��������</td> 
            <td id="borderBottom" ><input name="ReceiptType" type="text" value="1"/> �� 1. �ҵ����, 2. ��������</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* �ĺ���</td> 
            <td id="borderBottom" ><input name="ReceiptTypeNo" type="text" value=""/> �� ����û���ݿ����� Site���� ����� �ֹι�ȣ �Ǵ� �޴��� ��ȣ��</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* ���������ڹ�ȣ</td> 
            <td id="borderBottom" ><input name="ReceiptSubNum" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ���������� ��ȣ</td> 
            <td id="borderBottom" ><input name="ReceiptSubCoNm" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* ���������� ��ǥ��</td> 
            <td id="borderBottom" ><input name="ReceiptSubBossNm" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ���������� ��ȭ��ȣ</td> 
            <td id="borderBottom" ><input name="ReceiptSubTel" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* �������̵�</td> 
            <td id="borderBottom" ><input name="MID" type="text" value="nictest08m"/></td>
          </tr>

		

		
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
      	<td height="60"></td>
        <td class="btnCenter"><input type="button" value="�߱��ϱ�" onClick="goReceipt();"></td> <!-- �ϴܿ� ��ư�� �ִ°�� ��ư���� ���� ���� 30px -->
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15"></td>  <!--�������� ������ ���� ���� 15px-->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">* ǥ �׸��� �ݵ�� �������ֽñ� �ٶ��ϴ�.<br><br/>
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
</form>
</body>
</html>
