<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: 현금영수증 발급요청</title>
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
          <td>현금영수증 발급요청</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" background="images/bodyMiddle.gif"><table width="632" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35" height="10">&nbsp;</td> <!--상단여백 높이 10px -->
        <td width="562">&nbsp;</td>
        <td width="35">&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
        <td>현금영수증 발급요청 샘플입니다. </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> 정보를 기입하신 후 발급버튼을 눌러주십시오.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 상품명</td> 
            <td id="borderBottom" ><input name="GoodsName" type="text" value="곰돌이인형" /></td>
          </tr>
		  <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 주문번호</td> 
            <td id="borderBottom" ><input name="Moid" type="text" value="Moid12345" /></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 구매자명</td> 
            <td id="borderBottom" ><input name="BuyerName" type="text" value="홍길동" /></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 현금영수증 요청금액</td> 
            <td id="borderBottom" ><input name="ReceiptAmt" type="text" value="1000"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 공급가액</td> 
            <td id="borderBottom" ><input name="ReceiptSupplyAmt" type="text" value="900"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 부가가치세</td> 
            <td id="borderBottom" ><input name="ReceiptVAT" type="text" value="100"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 봉사료</td> 
            <td id="borderBottom" ><input name="ReceiptServiceAmt" type="text" value="0"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 증빙구분</td> 
            <td id="borderBottom" ><input name="ReceiptType" type="text" value="1"/> ※ 1. 소득공제, 2. 지출증빙</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 식별값</td> 
            <td id="borderBottom" ><input name="ReceiptTypeNo" type="text" value=""/> ※ 국세청현금영수증 Site에서 등록한 주민번호 또는 휴대폰 번호등</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 서브몰사업자번호</td> 
            <td id="borderBottom" ><input name="ReceiptSubNum" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 서브몰사업자 상호</td> 
            <td id="borderBottom" ><input name="ReceiptSubCoNm" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 서브몰사업자 대표자</td> 
            <td id="borderBottom" ><input name="ReceiptSubBossNm" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 서브몰사업자 전화번호</td> 
            <td id="borderBottom" ><input name="ReceiptSubTel" type="text" value=""/> </td>
          </tr>
           <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 상점아이디</td> 
            <td id="borderBottom" ><input name="MID" type="text" value="nictest08m"/></td>
          </tr>

		

		
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
      	<td height="60"></td>
        <td class="btnCenter"><input type="button" value="발급하기" onClick="goReceipt();"></td> <!-- 하단에 버튼이 있는경우 버튼포함 여백 높이 30px -->
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15"></td>  <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">* 표 항목은 반드시 기입해주시기 바랍니다.<br><br/>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="10"></td>  <!--하단여백 높이 10px -->
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
