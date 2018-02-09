<?php
	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );

	/********************************************************************************
	*
	* 다날 휴대폰 결제
	*
	* - 결제 완료 페이지
	*	결제 확인
	*
	* 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
	* DANAL Commerce Division Technique supporting Team
	* EMail : tech@danal.co.kr
	*
	********************************************************************************/
       
	/*
	 * Get CIURL
	 */
	$URL = GetCIMobileURL( $_POST["IsUseCI"],$_POST["CIURL"] );

	/*
	 * Get BgColor
	 */
	$BgColor = GetBgColor( $_POST["BgColor"] );
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
<link href="./css/style.css" type="text/css" rel="stylesheet"  media="screen" />
<script language="javascript" src="./js/jquery-latest.js" type="text/javascript"></script>
<script language="javascript" src="./js/jquery.mobile-1.2.0.js" type="text/javascript"></script>
<script language="JavaScript" src="./js/Common.js" type="text/javascript"></script>
<script language="javascript">
//<![CDATA[
// Run the script on DOM ready:
$(document).ready(function(){
	OrtChange();
});

var resUrl = "<?=urldecode($_POST['return_resurl'])?>";
function close_popup() {
    document.location.href= resUrl;
}
//]]>
</script>
</head>
<!-- 가로모드일때 horizontal 추가 -->
<body class="">
	<!-- 색상값은 type01 ~ type10 번까지 -->
	<div class="wrap type<?=$BgColor?>">
		<div class="header">
			<p class="tit">결제 성공</p>
		</div>
		<div class="content">
			<p class="message">결제가 정상 처리되었습니다.</p>
			<p class="btn st02">
				<!--WebView 닫으며 App으로 데이터 전송(자세한 사항은 매뉴얼 참조)-->
				<!--<a href="Javascript:window.TeleditApp.Result('xxxxx');" class="on">확인</a>-->
				<a href="javascript:;" onClick="javascript:close_popup();" class="on">확인</a>
			</p>
			<div class="cs">
				<p class="text">다날 고객센터 : 1566-3355</p>
				<span class="logo"><img src="<?=$URL?>" width="77" alt="가맹점로고" /></span>
			</div>
		</div>
	</div>
</body>
</html>
