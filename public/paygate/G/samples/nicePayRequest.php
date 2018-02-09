 <?php

	/**************************
	 * 1. ���̺귯�� ��Ŭ��� *
	 **************************/
	require("../lib/NicepayLite.php");

	/***************************************
	 * 2. NicepayLite Ŭ������ �ν��Ͻ� ���� *
	 ***************************************/
	$nicepay = new NicepayLite;

	// ���� MID�� �����մϴ�. test�� nictest00m���� �����Ͻʽÿ�.
	$nicepay->m_MID = "nictest00m";
	// ��������Ű (�� �ش� ����Ű�� �ٲ��ּ���)
	$nicepay->m_MerchantKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
	// �ŷ� ��¥
	$nicepay->m_EdiDate = date("YmdHis");
	// ��ǰ ������ �����Ͽ� �ֽʽÿ�.
	$nicepay->m_Price = "1004";
	
	//�ʱ� ó�� 
	$nicepay->requestProcess();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: ���� ��û</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />


<script src="https://web.nicepay.co.kr/flex/js/nicepay_tr.js" language="javascript"></script>

<script language="javascript">
NicePayUpdate();	//Active-x Control �ʱ�ȭ

/**
nicepay	�� ���� ������ �����մϴ�.
*/
function nicepay() {

	var payForm		= document.payForm;
	
	// �ʼ� ���׵��� üũ�ϴ� ������ �������ּ���.
	goPay(payForm);
}

/**
������ ��û�մϴ�.
*/
function nicepaySubmit()
{
	document.payForm.submit();
}

/**
������ ��� �Ҷ� ȣ��˴ϴ�.
*/
function nicepayClose()
{
	alert("������ ��� �Ǿ����ϴ�");
}

function chkTransType(value)
{
	document.payForm.TransType.value = value;
}

function chkPayType()
{
	document.payForm.PayMethod.value = checkedButtonValue('selectType');
}
</script>
</head>
<body>
<br>
<br>
<form name="payForm" method="post" action="nicePayResult.php">
<table width="632" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
  	<td >
  	  <table width="632" border="0" cellspacing="0" cellpadding="0" class="title">
        <tr>
          <td width="35">&nbsp;</td>
          <td>���� ��û</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" background="images/bodyMiddle.gif">
    <table width="632" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35" height="10">&nbsp;</td> <!--��ܿ��� ���� 10px -->
        <td width="562">&nbsp;</td>
        <td width="35">&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
        <td>���� ��û������ �����Դϴ�. <br> </td> 
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
            <!-- ���̺� �Ϲ��� ���̴� 30px // Ȧ���༿�� ��� class="thead01" ��� -->
            <td width="100" height="30" id="borderBottom" class="thead01">���� ����</td> 
            <td id="borderBottom" >
              <input type="checkbox" name="selectType" value="CARD" onClick="javascript:chkPayType();">[�ſ�ī��]
			  <input type="checkbox" name="selectType" value="BANK" onClick="javascript:chkPayType();">[������ü]
			  <input type="checkbox" name="selectType" value="VBANK" onClick="javascript:chkPayType();">[�������]
			  <input type="checkbox" name="selectType" value="CELLPHONE" onClick="javascript:chkPayType();">[�޴�������]
			</td>
          </tr>
          <tr>
            <!-- ���̺� �Ϲ��� ���̴� 30px // ¦���༿�� ��� class="thead02" ��� -->
			<td width="100" height="30" id="borderBottom" class="thead02" >����Ÿ��</td>
			<td id="borderBottom" >
			  <input type="radio" name="TransTypeRadio" value="0" onClick="javascript:chkTransType('0')" checked>�Ϲ�</input>
			  <input type="radio" name="TransTypeRadio" value="1" onClick="javascript:chkTransType('1')" >����ũ��</input>
			</td>
		  </tr>
		  <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ��ǰ��</td> 
            <td id="borderBottom" ><input name="GoodsName" type="text" value="������"/></td>
          </tr>
         
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">��ǰ�ֹ���ȣ</td> 
            <td id="borderBottom" ><input name="Moid" type="text" value="mnoid1234567890"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* �����ڸ�</td> 
            <td id="borderBottom" ><input name="BuyerName" type="text" value="ȫ�浿"/></td>
          </tr> 
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* ������ �̸���</td> 
            <td id="borderBottom" ><input name="BuyerEmail" type="text" value="test@abc.com"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* ������ ��ȭ��ȣ</td> 
            <td id="borderBottom" ><input name="BuyerTel" type="text" value="12345678"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* �������̵�</td> 
            <td id="borderBottom" ><input name="MID" type="text" value="<?php echo($nicepay->m_MID);?>"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">����� ���̵�</td> 
            <td id="borderBottom" ><input name="SUB_ID" type="text" value=""/></td>
          </tr>
         
		 <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">��Ų Ÿ��</td> 
            <td id="borderBottom" >
              <select name="SkinType">
					<option value="blue">BLUE</option>
					<option value="purple">PURPLE</option>
					<option value="red">RED</option>
					<option value="green">GREEN</option>
				</select>
			</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">�޴�������<br>��ǰ����</td> 
            <td id="borderBottom" >
              <table width="100%" height="50px" cellpadding="0px" cellspacing="0px">
                <tr>
                  <td width="185px" id="borderBottom">
                    <select name="GoodsCl" style="width:160px;">
				      <option value="1" selected>�ǹ�</option>
				      <option value="0">������</option>
			        </select>  
                  </td>
                  <td width="273px" class="red" id="borderBottom" ><span class="redBold">1</span> : �ǹ� <br> <span class="redBold">0</span> : ������</td>
                </tr>
              </table>
			</td>
          </tr>
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>


      <tr>
      	<td height="60"></td>
        <td class="btnCenter"><input type="button" value="��û�ϱ�" onClick="javascript:nicepay();"></td> <!-- �ϴܿ� ��ư�� �ִ°�� ��ư���� ���� ���� 30px -->
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
        <span class="bold">�׽�Ʈ ���̵�� ������ �ǿ����ؼ��� ���� ���� 11:30�п� �ϰ� ��ҵ˴ϴ�.</span><br/>
        �������̵� ����� �׽�Ʈ���̵� ������� �ʵ��� ������ ���Ǹ� ��Ź�帳�ϴ�.
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

<!-- Mall Parameters --> 
<input type="hidden" name="PayMethod" value="">
<!-- ��ǰ ���� -->
<input type="hidden" name="GoodsCnt" value="1">

<!-- �ּ� -->
<input type="hidden" name="BuyerAddr" value="����� ������ ���ﵿ 9-11">

<!-- ��ǰ ����(����� price���� ������ �����Ͻʽÿ�) -->
<input type="hidden" name="Amt" value="<?php echo($nicepay->m_Price);?>">

<!-- ���� Ÿ�� 0:�Ϲ�, 1:����ũ�� -->
<input type="hidden" name="TransType" value="0">

<!-- ���� �ɼ�  -->
<input type="hidden" name="OptionList" value="">

<!-- ������� �Աݿ��� ������  -->
<input type="hidden" name="VbankExpDate" value="<?php echo($nicepay->m_VBankExpDate); ?>"> 

<!-- ������ �� ID -->
<input type="hidden" name="MallUserID" value=""> 

<!-- ���� �Ұ� -->
<input type="hidden" name="EdiDate" value="<?php echo($nicepay->m_EdiDate); ?>">
<input type="hidden" name="EncryptData" value="<?php echo($nicepay->m_HashedString); ?>" >
<input type="hidden" name="TrKey" value="">
<input type="hidden" name="TID" value="">

</form>
</body>
</html>