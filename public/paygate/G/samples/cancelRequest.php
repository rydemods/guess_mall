<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: ���� ��� ��û</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script language="javascript">
<!--
function goCancel() {
	
	var formNm = document.tranMgr;
	
	// TID validation
	if(formNm.TID.value == "") {
		alert("TID�� Ȯ���ϼ���.");
		return ;
	} else if(formNm.TID.value.length > 30 || formNm.TID.value.length < 30) {
		alert("TID ���̸� Ȯ���ϼ���.");
		return ;
	}
	// ��ұݾ�
	if(formNm.CancelAmt.value == "") {
		alert("�ݾ��� �Է��ϼ���.");
		return ;
	} else if(formNm.CancelAmt.value.length > 12 ) {
		alert("�ݾ� �Է� ���� �ʰ�.");
		return ;
	}
	
	if(formNm.PartialCancelCode.value == '1'){
		if(formNm.TID.value.substring(10,12) != '01' &&  formNm.TID.value.substring(10,12) != '02' &&  formNm.TID.value.substring(10,12) != '03'){
			alert("�ſ�ī�����, ������ü, ������¸� �κ����/�κ�ȯ���� �����մϴ�");
			return false;
		}
	}
	
	formNm.submit();
	
}

-->
</script>
</head>
<body>
<br>
<form name="tranMgr" method="post" action="cancelResult.php">
<table width="632" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
  	<td >
  	  <table width="632" border="0" cellspacing="0" cellpadding="0" class="title">
        <tr>
          <td width="35">&nbsp;</td>
          <td>��� ��û</td>
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
        <td>��� ��û �����Դϴ�. </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--�������� ������ ���� ���� 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> ������ �����Ͻ� �� Ȯ�ι�ư�� �����ֽʽÿ�.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* TID</td> 
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td id="borderBottom" ><input name="TID" type="text" value="nictest00m01011104111037554275" size="30" maxlength="30"/></td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // ¦���༿�� ��� class="thead02" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead02">* ��ұݾ�</td> 
            <td id="borderBottom" ><input name="CancelAmt" type="text" value="1000"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ��һ���</td> 
            <td id="borderBottom" ><input name="CancelMsg" type="text" value="�� ��û"/></td>
          </tr>
          <tr>
          	<!-- ���̺� �β����� �ϴ� ����� ���̴� 50px -->
            <th height="50" class="thead02">* �κ���� ����</th> 
            <td>
              <table width="100%" height="50px" cellpadding="0px" cellspacing="0px">
                <tr>
                  <td width="185px">
                    <select name="PartialCancelCode" style="width:160px;">
					  <option value="0">��ü ���</option>
					  <option value="1">�κ� ���</option>
			  		</select>
				  </td>
                  <td width="273px" class="red"><span class="redBold">0</span> : ��ü���<br /><span class="redBold">1</span> : �κ����</td>
                </tr>
              </table>
            </td>
          </tr>
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
      	<td height="60"></td>
        <td class="btnCenter"><input type="button" value="����ϱ�" onClick="goCancel();"></td> <!-- �ϴܿ� ��ư�� �ִ°�� ��ư���� ���� ���� 30px -->
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
        <span class="bold">��Ұ� �̷���� �Ŀ��� �ٽ� �ǵ��� �� ������ ���� �����Ͻñ� �ٶ��ϴ�.</span><br/>
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

<input type="hidden" name="SupplyAmt" value="900">

</form>
</body>
</html>
