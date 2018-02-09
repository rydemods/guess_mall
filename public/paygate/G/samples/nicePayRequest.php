 <?php

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("../lib/NicepayLite.php");

	/***************************************
	 * 2. NicepayLite 클래스의 인스턴스 생성 *
	 ***************************************/
	$nicepay = new NicepayLite;

	// 상점 MID를 설정합니다. test시 nictest00m으로 설정하십시요.
	$nicepay->m_MID = "nictest00m";
	// 상점서명키 (꼭 해당 상점키로 바꿔주세요)
	$nicepay->m_MerchantKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
	// 거래 날짜
	$nicepay->m_EdiDate = date("YmdHis");
	// 상품 가격을 설정하여 주십시요.
	$nicepay->m_Price = "1004";
	
	//초기 처리 
	$nicepay->requestProcess();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: 결제 요청</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />


<script src="https://web.nicepay.co.kr/flex/js/nicepay_tr.js" language="javascript"></script>

<script language="javascript">
NicePayUpdate();	//Active-x Control 초기화

/**
nicepay	를 통해 결제를 시작합니다.
*/
function nicepay() {

	var payForm		= document.payForm;
	
	// 필수 사항들을 체크하는 로직을 삽입해주세요.
	goPay(payForm);
}

/**
결제를 요청합니다.
*/
function nicepaySubmit()
{
	document.payForm.submit();
}

/**
결제를 취소 할때 호출됩니다.
*/
function nicepayClose()
{
	alert("결제가 취소 되었습니다");
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
          <td>결제 요청</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" background="images/bodyMiddle.gif">
    <table width="632" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35" height="10">&nbsp;</td> <!--상단여백 높이 10px -->
        <td width="562">&nbsp;</td>
        <td width="35">&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
        <td>결제 요청페이지 샘플입니다. <br> </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> 정보를 기입하신 후 확인버튼을 눌러주십시오.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead01">결제 수단</td> 
            <td id="borderBottom" >
              <input type="checkbox" name="selectType" value="CARD" onClick="javascript:chkPayType();">[신용카드]
			  <input type="checkbox" name="selectType" value="BANK" onClick="javascript:chkPayType();">[계좌이체]
			  <input type="checkbox" name="selectType" value="VBANK" onClick="javascript:chkPayType();">[가상계좌]
			  <input type="checkbox" name="selectType" value="CELLPHONE" onClick="javascript:chkPayType();">[휴대폰결제]
			</td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 짝수행셀의 경우 class="thead02" 사용 -->
			<td width="100" height="30" id="borderBottom" class="thead02" >결제타입</td>
			<td id="borderBottom" >
			  <input type="radio" name="TransTypeRadio" value="0" onClick="javascript:chkTransType('0')" checked>일반</input>
			  <input type="radio" name="TransTypeRadio" value="1" onClick="javascript:chkTransType('1')" >에스크로</input>
			</td>
		  </tr>
		  <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 상품명</td> 
            <td id="borderBottom" ><input name="GoodsName" type="text" value="곰인형"/></td>
          </tr>
         
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">상품주문번호</td> 
            <td id="borderBottom" ><input name="Moid" type="text" value="mnoid1234567890"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 구매자명</td> 
            <td id="borderBottom" ><input name="BuyerName" type="text" value="홍길동"/></td>
          </tr> 
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 구매자 이메일</td> 
            <td id="borderBottom" ><input name="BuyerEmail" type="text" value="test@abc.com"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 구매자 전화번호</td> 
            <td id="borderBottom" ><input name="BuyerTel" type="text" value="12345678"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">* 상점아이디</td> 
            <td id="borderBottom" ><input name="MID" type="text" value="<?php echo($nicepay->m_MID);?>"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">서브몰 아이디</td> 
            <td id="borderBottom" ><input name="SUB_ID" type="text" value=""/></td>
          </tr>
         
		 <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">스킨 타입</td> 
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
            <td width="100" height="30" id="borderBottom" class="thead01">휴대폰결제<br>상품구분</td> 
            <td id="borderBottom" >
              <table width="100%" height="50px" cellpadding="0px" cellspacing="0px">
                <tr>
                  <td width="185px" id="borderBottom">
                    <select name="GoodsCl" style="width:160px;">
				      <option value="1" selected>실물</option>
				      <option value="0">컨텐츠</option>
			        </select>  
                  </td>
                  <td width="273px" class="red" id="borderBottom" ><span class="redBold">1</span> : 실물 <br> <span class="redBold">0</span> : 컨텐츠</td>
                </tr>
              </table>
			</td>
          </tr>
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>


      <tr>
      	<td height="60"></td>
        <td class="btnCenter"><input type="button" value="요청하기" onClick="javascript:nicepay();"></td> <!-- 하단에 버튼이 있는경우 버튼포함 여백 높이 30px -->
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
        <span class="bold">테스트 아이디로 결제된 건에대해서는 당일 오후 11:30분에 일괄 취소됩니다.</span><br/>
        실제아이디 적용시 테스트아이디가 적용되지 않도록 각별한 주의를 부탁드립니다.
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

<!-- Mall Parameters --> 
<input type="hidden" name="PayMethod" value="">
<!-- 상품 갯수 -->
<input type="hidden" name="GoodsCnt" value="1">

<!-- 주소 -->
<input type="hidden" name="BuyerAddr" value="서울시 강남구 역삼동 9-11">

<!-- 상품 가격(상단의 price에서 가격을 지정하십시요) -->
<input type="hidden" name="Amt" value="<?php echo($nicepay->m_Price);?>">

<!-- 결제 타입 0:일반, 1:에스크로 -->
<input type="hidden" name="TransType" value="0">

<!-- 결제 옵션  -->
<input type="hidden" name="OptionList" value="">

<!-- 가상계좌 입금예정 만료일  -->
<input type="hidden" name="VbankExpDate" value="<?php echo($nicepay->m_VBankExpDate); ?>"> 

<!-- 구매자 고객 ID -->
<input type="hidden" name="MallUserID" value=""> 

<!-- 변경 불가 -->
<input type="hidden" name="EdiDate" value="<?php echo($nicepay->m_EdiDate); ?>">
<input type="hidden" name="EncryptData" value="<?php echo($nicepay->m_HashedString); ?>" >
<input type="hidden" name="TrKey" value="">
<input type="hidden" name="TID" value="">

</form>
</body>
</html>