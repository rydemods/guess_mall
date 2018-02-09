<?php
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayWEB.php';
require_once dirname(__FILE__).'/lib/nicepay/core/Constants.php';
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';

/** 1. Request Wrapper 클래스를 등록한다.  */ 
$httpRequestWrapper = new NicePayHttpServletRequestWrapper($_REQUEST);
$_REQUEST = $httpRequestWrapper->getHttpRequestMap();

/** 2. 소켓 어댑터와 연동하는 Web 인터페이스 객체를 생성한다.*/
$nicepayWEB = new NicePayWEB();

/** 2-1. 로그 디렉토리 설정 */
$nicepayWEB->setParam("NICEPAY_LOG_HOME","C:\log");

/** 2-2. 로그 모드 설정(0: DISABLE, 1: ENABLE) */
$nicepayWEB->setParam("APP_LOG","1");

/** 2-3. 암호화플래그 설정(N: 평문, S:암호화) */
$nicepayWEB->setParam("EncFlag","S");

/** 2-4. 서비스모드 설정(결제 서비스 : PY0 , 취소 서비스 : CL0) */
$nicepayWEB->setParam("SERVICE_MODE", "PY0");

/** 2-5. 통화구분 설정(현재 KRW(원화) 가능)  */
$nicepayWEB->setParam("Currency", "KRW");


$nicepayWEB->setParam("PayMethod",'CASHRCPT');


/** 2-7 라이센스키 설정 
	상점 ID에 맞는 상점키를 설정하십시요.
	*/
$nicepayWEB->setParam("LicenseKey","YmbbO3a5I3Oo+rKNUNHXtYTUAbeeM939ytI4PUh6IkVOMSngSL/LykbYSsnBE2gAp9tHWNLTb1xHak0nyar1xA==");


/** 3. 결제 요청 */
$responseDTO = $nicepayWEB->doService($_REQUEST);


/** 4. 결제 결과 */
$resultCode = $responseDTO->getParameter("ResultCode"); // 결과코드 (정상 :3001 , 그 외 에러)
$resultMsg = $responseDTO->getParameter("ResultMsg");   // 결과메시지
$authDate = $responseDTO->getParameter("AuthDate");   // 승인일시 YYMMDDHH24mmss
$tid = $responseDTO->getParameter("TID");  // 거래ID
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>NICEPAY :: 현금영수증 발급 결과</title>
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
          <td>현금영수증 발급 결과</td>
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
        <td>현금영수증 발급 요청이 완료되었습니다.
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
        <td class="bold"><img src="images/bullet.gif" /> 현금영수증 발급 내역을 확인하세요.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead01">거래 아이디</td> 
            <td id="borderBottom" ><? echo($tid);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead02">발급 시간</td> 
            <td id="borderBottom" ><? echo($authDate);?>&nbsp;</td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 짝수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead01">결과 내용</td> 
            <td id="borderBottom" ><? echo('['.$resultCode.'] '.$resultMsg);?>&nbsp;</td>
          </tr>
          
        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
        <td height="60"></td> 
        <td class="btnCenter"><input type="button" value="전표출력" onClick="viewReceipt('<? echo($tid);?>');"></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">현금영수증 발급  익일 이후에 국체청 현금영수증 Site에서 발급 내용을 확인하실 수 있습니다.         
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