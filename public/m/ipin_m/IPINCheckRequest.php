<?php
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );

	//#######################################################################################
	//#####
	//#####	나이스아이핀(대체인증키) 서비스 샘플 페이지 소스				한국신용정보(주)
	//#####
	//#####	================================================================================
	//#####
	//#####	* 본 페이지는 귀사의 화면에 맞게 수정하십시오.
	//#####	  단, Head 영역에 설정된 Javascript를 수정하거나 변경하시면 사용할 수 없습니다.
	//#####
	//#######################################################################################


	//========================================================================================
	//=====	▣ 회원사 ID, 사이트식별정보 설정 : 계약시에 발급된 회원사 ID를 설정하십시오. ▣
	//========================================================================================
	echo $_SERVER[PHP_SELF];
	print_r($ipin);
	$NiceId = $ipin[id];
	echo $NiceId;
	$SIKey = $ipin[SIKey];

	//========================================================================================
	//=====	▣ 반환 결과를 수신할 URL을 설정하십시오. (단, 페이지는 그대로 사용하십시오)
	//=====	   한신정 서비스에 전달되어 사용되므로 반드시 절대 URL 경로를 설정하셔야 합니다.
	//========================================================================================

	//EX) http://귀사의도메인/NiceCheckPopup.php
	$self_filename = basename($_SERVER[PHP_SELF]);
	$loc = strpos($_SERVER[PHP_SELF], $self_filename);
	$sub_path = substr($_SERVER[PHP_SELF], 0, $loc);

	$Port = ($_SERVER[SERVER_PORT] == 80)? "":$_SERVER[SERVER_PORT];
	if (strlen($Port)>0) $Port = ":".$Port;
	$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';

	$ReturnURL = $Protocol.$_SERVER[HTTP_HOST].$Port.$sub_path."NiceCheckCallback.php";
	if($_GET['callType'] == 'applyipin') $ReturnURL = $Protocol.$_SERVER['HTTP_HOST'].$Port.$sub_path."IPINApply.php";
	$strOrderNo = date("Ymd") . rand(100000000000,999999999999); //주문번호 20자리 .. 매 요청마다 중복되지 않도록 유의

	// 해킹방지를 위해 요청정보 세션에 저장
	//session_start();		//library에서 함.
	$sess_OrderNo = $strOrderNo;
	session_register("sess_OrderNo");
	$_SESSION['sess_OrderNo'] = $strOrderNo;
	session_register("sess_callType");
	$_SESSION['sess_callType'] = $_GET['callType'];

?>
<html>
	<head>
		<title>한국신용정보주식회사 개인인증키(대체인증키) 서비스 샘플 페이지</Title>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

		<!--	==========================================================	-->
		<!--	한국신용정보주식회사 처리 모듈 (수정 및 변경하지 마십시오)	-->
		<!--	==========================================================	-->
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.msg.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>

		<LINK href="./nice.nuguya.oivs.css" type=text/css rel=stylesheet>
	</head>

	<script language="javascript">

		document.onkeydown = onKeyDown;

		function onKeyDown( event )
		{
			var e = event;
			if ( event == null ) e = window.event;

			if ( e.keyCode == 13 ) goIDCheck();
		}

		function loadAction()
		{
			if ( document.all.PingInfo.value == "" )
			{
				alert( "한국신용정보(주)의 개인인증키 서비스가 점검중입니다.\n잠시후 다시 시도하시기 바랍니다.\n\n상태가 계속되면 사이트관리자에게 문의하십시오" );
				return;
			}
			goIDCheck();
		}

		function validate()
		{
			var NiceId		= document.getElementById( "NiceId" );
			var PingInfo	= document.getElementById( "PingInfo" );
			var ReturnURL	= document.getElementById( "ReturnURL" );

			if ( NiceId.value == "" )
			{
				alert( getCheckMessage( "S60" ) );
				NiceId.focus();
				return false;
			}

			if ( PingInfo.value == "" )
			{
				alert( getCheckMessage( "S61" ) );
				return false;
			}

			if ( ReturnURL.value == "" )
			{
				alert( getCheckMessage( "S64" ) );
				ReturnURL.focus();
				return false;
			}

			return true;
		}

		function goIDCheck()
		{
			if ( validate() == true )
			{
				var strNiceId 	= document.getElementById( "NiceId" ).value;
				var strPingInfo	= document.getElementById( "PingInfo" ).value;
				var strOrderNo	= document.getElementById( "OrderNo" ).value;
				var strInqRsn	= document.getElementById( "InqRsn" ).value;
				var strReturnUrl	= document.getElementById( "ReturnURL" ).value;
				var strSIKey 	= document.getElementById( "SIKey" ).value;

				document.reqForm.SendInfo.value = makeCertKeyInfoPA( strNiceId, strPingInfo, strOrderNo, strInqRsn, strReturnUrl, strSIKey );
//				document.reqForm.SendInfo.value = makeCertKeyInfo( strNiceId, strPingInfo, strOrderNo, strInqRsn, strReturnUrl );
				document.reqForm.ProcessType.value = strPersonalCertKey;

				//var popupWindow = window.open( "", "popupCertKey", "top=100, left=200, status=0, width=417, height=490" );
				document.reqForm.target = "popupCertKey";
				document.reqForm.action = strCertKeyServiceUrl;
				document.reqForm.submit();
				//popupWindow.focus();
			}

			return;
		}

	</script>

	<BODY onLoad="javascript:loadAction();" >
		<FORM id="reqForm" name="reqForm" method="POST" action="">
			<input class="small" type="hidden" id="SendInfo" name="SendInfo" >
			<input class="small" type="hidden" id="ProcessType" name="ProcessType" >
		</FORM>
		<FORM id="pageForm" name="pageForm" method="POST" action="">
	  		<INPUT type="hidden" id="NiceId" name="NiceId" value="<? echo $NiceId ; ?>">
	  		<INPUT type="hidden" id="SIKey" name="SIKey" value="<? echo $SIKey ; ?>">
			<INPUT type="hidden" id="PingInfo" name="PingInfo" value="<? echo getPingInfo(); ?>">
	  		<INPUT type="hidden" id="ReturnURL" name="ReturnURL" value="<? echo $ReturnURL ; ?>" >
			<!--	조회사유를 설정하십시오 ( '10'-회원가입, '20'-기존회원 확인, '30'-성인인증, '40'-비회원 확인, '50'-기타 사유 )	-->
			<input type="hidden" id="InqRsn" name="InqRsn" value="10">
			<!--	주문번호를 설정하십시오. (최소 14자리, 20자리미만)	-->
			<input type="hidden" id="OrderNo" name="OrderNo" value="<? echo $strOrderNo; ?>">
		</form>
	</BODY>

</html>
