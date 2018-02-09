<html>
<head>
	<title>NICE신용평가정보 가상주민번호 서비스</title>
<style type="text/css"> 
BODY
{
    COLOR: #7f7f7f;
    FONT-FAMILY: "Dotum","DotumChe","Arial";
    BACKGROUND-COLOR: #ffffff;
}
</style>
</head>

<body>

<?php
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");
//보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 

	session_start();
	/********************************************************************************************************************************************
		NICE신용평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
		
		서비스명 : 가상주민번호서비스 (IPIN) 서비스
		페이지명 : 가상주민번호서비스 (IPIN) 사용자 인증 정보 결과 페이지
		
				   수신받은 데이터(인증결과)를 복호화하여 사용자 정보를 확인합니다.
	*********************************************************************************************************************************************/
	
	$sSiteCode					= $_data->ipin_id;			// IPIN 서비스 사이트 코드		(NICE신용평가정보에서 발급한 사이트코드)
	$sSitePw					= $_data->ipin_password;			// IPIN 서비스 사이트 패스워드	(NICE신용평가정보에서 발급한 사이트패스워드)
	$sModulePath				= "";			// 하단내용 참조

	$self_filename = basename($_SERVER['PHP_SELF']);
	$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
	$loc = substr($_SERVER['PHP_SELF'], 0, $loc);
	$sModulePath = $_SERVER['DOCUMENT_ROOT'].$loc."IPINClient";

	$sModulePath = $_SERVER['DOCUMENT_ROOT']."/front/ipin/IPINClient";
	$sReturnURL					= "";			// 하단내용 참조

	$Port = ($_SERVER['SERVER_PORT'] == 80) ? "" : $_SERVER['SERVER_PORT'];
	if (strlen($Port) > 0) $Port = ":".$Port;
	$Protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$sReturnURL = $Protocol.$_SERVER['HTTP_HOST'].$Port.$loc."ipin_process.php";

	
  $sEncData = $_POST['enc_data'];	// ipin_process.php 에서 리턴받은 암호화 된 사용자 인증 정보
  
		//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
    if(preg_match("/[#\&\\-%@\\\:;,\.\'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", $sEncData, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////  
	
	// ipin_main.php 에서 저장한 세션 정보를 추출합니다.
	// 데이타 위변조 방지를 위해 확인하기 위함이므로, 필수사항은 아니며 보안을 위한 권고사항입니다.
	$sCPRequest = $_SESSION['CPREQUEST'];
    
    if ($sEncData != "") {
    
    	// 사용자 정보를 복호화 합니다.
    	// 실행방법은 싱글쿼터(`) 외에도, 'exec(), system(), shell_exec()' 등등 귀사 정책에 맞게 처리하시기 바랍니다.
    	$sDecData = `$sModulePath RES $sSiteCode $sSitePw $sEncData`;
    	
    	if ($sDecData == -9) {
    		$sRtnMsg = "입력값 오류 : 복호화 처리시, 필요한 파라미터값의 정보를 정확하게 입력해 주시기 바랍니다.";
    	} else if ($sDecData == -12) {
    		$sRtnMsg = "NICE신용평가정보에서 발급한 개발정보가 정확한지 확인해 보세요.";
    	} else {
    	
    		// 복호화된 데이타 구분자는 ^ 이며, 구분자로 데이타를 파싱합니다.
    		/*
    			- 복호화된 데이타 구성
    			가상주민번호확인처리결과코드^가상주민번호^성명^중복확인값(DupInfo)^연령정보^성별정보^생년월일(YYYYMMDD)^내외국인정보^고객사 요청 Sequence
    		*/
    		$arrData = explode("^", $sDecData);
    		$iCount = count($arrData);
    		
    		if ($iCount >= 5) {
    		
    			/*
					다음과 같이 사용자 정보를 추출할 수 있습니다.
					사용자에게 보여주는 정보는, '이름' 데이타만 노출 가능합니다.
				
					사용자 정보를 다른 페이지에서 이용하실 경우에는
					보안을 위하여 암호화 데이타($sEncData)를 통신하여 복호화 후 이용하실것을 권장합니다. (현재 페이지와 같은 처리방식)
					
					만약, 복호화된 정보를 통신해야 하는 경우엔 데이타가 유출되지 않도록 주의해 주세요. (세션처리 권장)
					form 태그의 hidden 처리는 데이타 유출 위험이 높으므로 권장하지 않습니다.
				*/
				
				$strResultCode	= $arrData[0];			// 결과코드
				if ($strResultCode == 1) {
					$strCPRequest	= $arrData[8];			// CP 요청번호
					
					if ($sCPRequest == $strCPRequest) {
				
						$sRtnMsg = "사용자 인증 성공";
						
						$_SESSION[ipin][vno] = $arrData[1];
						$_SESSION[ipin][name] = $arrData[2];
						$_SESSION[ipin][dupinfo] = $arrData[3];
						$_SESSION[ipin][gender] = ($arrData[5]-1) * -1;
						$_SESSION[ipin][birthdate] = $arrData[6];

						$strVno      		= $arrData[1];	// 가상주민번호 (13자리이며, 숫자 또는 문자 포함)
						$strUserName		= $arrData[2];	// 이름
						$strDupInfo			= $arrData[3];	// 중복가입 확인값 (64Byte 고유값)
						$strAgeInfo			= $arrData[4];	// 연령대 코드 (개발 가이드 참조)
					    $strGender			= $arrData[5];	// 성별 코드 (개발 가이드 참조)
					    $strBirthDate		= $arrData[6];	// 생년월일 (YYYYMMDD)
					    $strNationalInfo	= $arrData[7];	// 내/외국인 정보 (개발 가이드 참조)

						echo "<script>parent.ipin_chk('ipin','".iconv("EUC-KR","UTF-8", $strUserName)."');</script>";
					
					} else {
						$sRtnMsg = "CP 요청번호 불일치 : 세션에 넣은 $sCPRequest 데이타를 확인해 주시기 바랍니다.";
					}
				} else {
					$sRtnMsg = "리턴값 확인 후, NICE신용평가정보 개발 담당자에게 문의해 주세요. [$strResultCode]";
				}
    		
    		} else {
    			$sRtnMsg = "리턴값 확인 후, NICE신용평가정보 개발 담당자에게 문의해 주세요.";
    		}
    	
    	}
    } else {
    	$sRtnMsg = "처리할 암호화 데이타가 없습니다.";
    }
    
    /*
	┌ sModulePath 변수에 대한 설명  ─────────────────────────────────────────────────────
		모듈 경로설정은, '/절대경로/모듈명' 으로 정의해 주셔야 합니다.
		
		+ FTP 로 모듈 업로드시 전송형태를 'binary' 로 지정해 주시고, 권한은 755 로 설정해 주세요.
		
		+ 절대경로 확인방법
		  1. Telnet 또는 SSH 접속 후, cd 명령어를 이용하여 모듈이 존재하는 곳까지 이동합니다.
		  2. pwd 명령어을 이용하면 절대경로를 확인하실 수 있습니다.
		  3. 확인된 절대경로에 '/모듈명'을 추가로 정의해 주세요.
	└────────────────────────────────────────────────────────────────────
	*/
	
?>

	처리결과 : <?= $sRtnMsg ?><br>
	이름 : <?= $strUserName ?><br>

	<form name="user" method="post">
		<input type="hidden" name="enc_data" value="<?= $sEncData ?>"><br>
	</form>
</body>
</html>