<?PHP

	//-------------------------------------------------------------------------------
	// 가맹점 Return 페이지에서 호출한 opener 페이지 샘플 ( PHP )
	// complete.php
	// 2015-03-25	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-------------------------------------------------------------------------------
	header('Content-type: text/html; charset: UTF-8');

	include("payco_config.php");


	//---------------------------------------------------------------------------------
	// 결제창이 닫혔습니다.
	// 완료페이지를 이곳에 작성하시면 됩니다.
	// 고객이 주문한 주문 리스트를 나열하시면 됩니다. (상품링크 또는 주문상태포함 등등...)
	// 이 페이지는 PAYCO 결제창에서 가맹점의 Return URL이 정상적으로 호출이 되어야 표시되는 페이지 입니다.
	// 고객이 PAYCO 결제창을 강제로 닫으면 호출이 안될 수 있습니다.
	//---------------------------------------------------------------------------------

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">

<meta name="keyword" content="컨텐츠">

<title>RETURN_PAYCO_DEMOWEB</title>

<body>
<div>
주문이 정상적으로 완료되었습니다.
</div>
</body>
</html>