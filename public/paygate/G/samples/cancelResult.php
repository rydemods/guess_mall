<?php
	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("../lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite 클래스의 인스턴스 생성 *
	 ***************************************/
	$nicepay = new NicepayLite;
	 
	// 로그를 설정하여 주십시요.
	$nicepay->m_NicepayHome = "./log";	
	
	$nicepay->m_ssl = "true";	

	$nicepay->m_ActionType = "CLO";							// 취소 요청 선언
	$nicepay->m_CancelAmt = $CancelAmt;						// 취소 금액 설정
	$nicepay->m_TID = $TID;									// 취소 TID 설정
	$nicepay->m_CancelMsg = $CancelMsg;						// 취소 사유
	$nicepay->m_PartialCancelCode = $PartialCancelCode;		// 전체 취소, 부분 취소 여부 설정
	$nicepay->m_CancelPwd = "123456";						// 취소 비밀번호 설정
		
	// PG에 접속하여 취소 처리를 진행.
	//	취소는 2001 또는 2211이 성공입니다.
	$nicepay->startAction();
	

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: 취소 결과</title>
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
          <td>취소 결과</td>
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
        <td>취소 요청이 완료되었습니다.
        </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> 취소내역을 확인하세요.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead01">거래 아이디</td> 
            <td id="borderBottom" ><? echo($nicepay->m_ResultData["TID"]);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead02">거래 시간</td> 
            <td id="borderBottom" ><? echo($nicepay->m_ResultData["CancelTime"]);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 짝수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead01">결과 내용</td> 
            <td id="borderBottom" ><? echo('['.$nicepay->m_ResultData["ResultCode"].'] '.$nicepay->m_ResultData["ResultMsg"]);?>&nbsp;</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">취소 금액</td> 
            <td id="borderBottom" ><? echo($nicepay->m_ResultData["CancelAmt"]);?>&nbsp;</td>
          </tr>
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
        <td height="15"></td>  <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">취소가 성공한 경우에는 다시 승인상태로 복구 할 수 없습니다..        
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
</body>
</html>
