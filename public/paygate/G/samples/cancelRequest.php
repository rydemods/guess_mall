<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: 결제 취소 요청</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script language="javascript">
<!--
function goCancel() {
	
	var formNm = document.tranMgr;
	
	// TID validation
	if(formNm.TID.value == "") {
		alert("TID를 확인하세요.");
		return ;
	} else if(formNm.TID.value.length > 30 || formNm.TID.value.length < 30) {
		alert("TID 길이를 확인하세요.");
		return ;
	}
	// 취소금액
	if(formNm.CancelAmt.value == "") {
		alert("금액을 입력하세요.");
		return ;
	} else if(formNm.CancelAmt.value.length > 12 ) {
		alert("금액 입력 길이 초과.");
		return ;
	}
	
	if(formNm.PartialCancelCode.value == '1'){
		if(formNm.TID.value.substring(10,12) != '01' &&  formNm.TID.value.substring(10,12) != '02' &&  formNm.TID.value.substring(10,12) != '03'){
			alert("신용카드결제, 계좌이체, 가상계좌만 부분취소/부분환불이 가능합니다");
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
          <td>취소 요청</td>
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
        <td>취소 요청 샘플입니다. </td> 
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
            <td width="100" height="30" id="borderBottom" class="thead01">* TID</td> 
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td id="borderBottom" ><input name="TID" type="text" value="nictest00m01011104111037554275" size="30" maxlength="30"/></td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 짝수행셀의 경우 class="thead02" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead02">* 취소금액</td> 
            <td id="borderBottom" ><input name="CancelAmt" type="text" value="1000"/></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">* 취소사유</td> 
            <td id="borderBottom" ><input name="CancelMsg" type="text" value="고객 요청"/></td>
          </tr>
          <tr>
          	<!-- 테이블 두꺼워야 하는 경우의 높이는 50px -->
            <th height="50" class="thead02">* 부분취소 여부</th> 
            <td>
              <table width="100%" height="50px" cellpadding="0px" cellspacing="0px">
                <tr>
                  <td width="185px">
                    <select name="PartialCancelCode" style="width:160px;">
					  <option value="0">전체 취소</option>
					  <option value="1">부분 취소</option>
			  		</select>
				  </td>
                  <td width="273px" class="red"><span class="redBold">0</span> : 전체취소<br /><span class="redBold">1</span> : 부분취소</td>
                </tr>
              </table>
            </td>
          </tr>
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
      	<td height="60"></td>
        <td class="btnCenter"><input type="button" value="취소하기" onClick="goCancel();"></td> <!-- 하단에 버튼이 있는경우 버튼포함 여백 높이 30px -->
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
        <span class="bold">취소가 이루어진 후에는 다시 되돌릴 수 없으니 이점 참고하시기 바랍니다.</span><br/>
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

<input type="hidden" name="SupplyAmt" value="900">

</form>
</body>
</html>
