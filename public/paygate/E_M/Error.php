<?php
	if( $AbleBack ) $btn_error = "결제 재시도";
	else		$btn_error = "취 소";

	/*
	 * Get CIURL
	 */
	$URL = GetCIMobileURL( $IsUseCI,$CIURL );

	/*
	 * Get BgColor
	 */
	$BgColor = GetBgColor( $BgColor );
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
//]]>
</script>
</head>
<!-- 가로모드일때 horizontal 추가 -->
<body class="">
	<!-- 색상값은 type01 ~ type10 번까지 -->
	<div class="wrap type<?=$BgColor?>">
		<div class="header">
			<p class="tit">결제 에러</p>
			<a href="<?=$BackURL?>" class="closeBtn"><img src="./images/btn_close.png" width="37" alt="닫기" /></a>
		</div>
		<div class="content">
			<div class="error">
				<dl class="info">
					<dt>에러 내용(<?=$Result?>)</dt>
					<dd><?=str_replace(".","<br>",$ErrMsg)?></dd>
				</dl>
				<p class="customer">상담원 통화가능시간 : <br />
				평일 : 9시 ~ 19시<br />
				<span>토요일, 일요일, 공휴일 휴무</span></p>
			</div>
			
			<p class="btn st02">
				<a href="<?=$BackURL?>" class="on"><?=$btn_error?></a>
			</p>
			<div class="cs">
				<p class="text">다날 고객센터 : 1566-3355</p>
				<span class="logo"><img src="<?=$URL?>" width="77" alt="가맹점로고" /></span>
			</div>
		</div>
	</div>
</body>
</html>
