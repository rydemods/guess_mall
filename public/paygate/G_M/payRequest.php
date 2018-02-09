<?
		
	// ����Ͻ�
	$ediDate = date("YmdHis");
	
	// ��������Ű (�� �ش� ����Ű�� �ٲ��ּ���)
	$merchantKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
	
	// hash ó��  
	$MerchantID = "nictest00m";
	$price = "1004";
	$str_src = $ediDate.$MerchantID.$price.$merchantKey;

	$hash_String = base64_encode(md5($str_src));
	// ������� �Ա� ������ ����
// 	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
// 	$vDate = date("Ymd",$tomorrow);
	$vDate = date("Ymd",strtotime("+3 day",time()));

		
?>
<html>
<head>
<title>NICE PG :: NICEPAY</title>
<link href="./css/__smart.css" rel="stylesheet" type="text/css"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;"/>
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
<script language="javascript">
/**
	����Ʈ�� ���� ��û
*/
function goPay(form) {
	form.target = "_blank";
	form.method = "post";
	form.action = "https://web.nicepay.co.kr/smart/paySmart.jsp";
	form.submit();
}
</script>
</head>
<body>
<form name="tranMgr">
		<!-- ��ǰ ���� -->
        <input type="hidden" name="GoodsCnt" value="1"/>		
		<!-- �ֹ���ȣ -->
        <input type="hidden" name="Moid" value="merchant_oid_1234567890"/>
		<!-- ������ ��ȭ��ȣ -->
        <input type="hidden" name="BuyerTel" value="0212345678"/>	
		<!-- ������ �̸��� �ּ�  -->
        <input type="hidden" name="BuyerEmail" value="test@abc.com"/>	
        <!-- ������ �ּ� -->
        <input type="hidden" name="BuyerAddr"  value="����� ������ ������ 689"/>	
		          
		<!-- ������� �ԱݿϷ���  -->
		<input type="hidden" name="VbankExpDate"  value="<?=$vDate?>"/>	
        <!-- �������� �������� ����� ���� �����Ͽ� �ֽʽÿ�. �״�� ��޵˴ϴ�  -->
		<input type="hidden" name="MallReserved"  value="param1^param2^param3"/>
		<!-- ��� ��޹��� url�� �����Ͻʽÿ�.  -->
        <input type="hidden" name="ReturnURL" value="http://harim.ajashop.co.kr/paygate/G_M/payResult.php">
	           	        
		<!-- �޴��� ���� ��ǰ���� 1:�ǹ�, 0:������ -->
		<input type="hidden" name="GoodsCl" value="1"/>	
		
		<!-- APP �� WebView�� �����ϴ� ��츸 ����մϴ�. -->		
	  <!--<input type="hidden" name="WapUrl"  value="nicepaysample://"/>	-->					    <!-- ISP �� ������ü ���� URL (�� ��Ű�� �� �Է�) -->
    <!--<input type="hidden" name="IspCancelUrl"  value="nicepaysample://ISPCancel"/>	-->		<!-- ISP ��� URL(�� ��Ű�� �� �Է�) -->        
        
		<!-- �������� ��� ��޹��� ���ڵ��� �����Ͽ� �ֽʽÿ� (utf-8 �Ǵ� euc-kr) -->
		<input type="hidden" name="CharSet" value="euc-kr"/>

        <div class="selectList">
                <ul>
                        <li class="selectBar">
                                <label>��������</label>
                                <select name="PayMethod">
                                	<option value="CARD">�ſ�ī��</option>
                                	<option value="BANK">������ü</option>
                                	<option value="CELLPHONE">�޴������</option>
                                	<option value="VBANK">�������</option>
                                </select>
                        </li>
                        <li class="selectBar">
                                <label>��&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��</label>
                                <input type="text" name="Amt" size="25px;" value="<?=$price?>">
                        </li>
                        <li class="selectBar">
                                <label>��&nbsp;&nbsp;ǰ&nbsp;&nbsp;��</label>
                                 <input type="text" name="GoodsName" size="25px;" value="smart"/>
                        </li>
                         <li class="selectBar">
                                <label>��&nbsp;&nbsp;��&nbsp;&nbsp;��</label>
                                <input type="text" name="BuyerName" size="25px;" value="ȫ�浿">
                        </li>
						<li class="selectBar">
                                <label>M&nbsp;&nbsp;&nbsp;I&nbsp;&nbsp;&nbsp;D&nbsp;</label>
                                <input type="text" name="MID" size="25px;" value="<?=$MerchantID?>">
                        </li>
                </ul>
        </div>

        <div class="btn">
                <img class="right" src="./images/btn_next.png"  onClick="goPay(document.tranMgr);"/>
        </div>
		
		<!-- ����� ID -->
        <input type="hidden" name="MallUserID" value=""/>	
		<!-- ���� ���� ���ʽÿ�.-->
		<input type="hidden" name="EncryptData" value="<?=$hash_String?>"/>
        <input type="hidden" name="ediDate"  value="<?=$ediDate?>"/>
        
</form>
</body>
</html>