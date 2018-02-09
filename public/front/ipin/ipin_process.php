<?php

	session_start();

	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");

	//보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 

	/********************************************************************************************************************************************
		NICE신용평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
		
		서비스명 : 가상주민번호서비스 (IPIN) 서비스
		페이지명 : 가상주민번호서비스 (IPIN) 사용자 인증 정보 처리 페이지
		
				   수신받은 데이터(인증결과)를 메인화면으로 되돌려주고, close를 하는 역활을 합니다.
	*********************************************************************************************************************************************/
	
	// 사용자 정보 및 CP 요청번호를 암호화한 데이타입니다. (ipin_main.php 페이지에서 암호화된 데이타와는 다릅니다.)
	$sResponseData = $_POST['enc_data'];
	
	// ipin_main.php 페이지에서 설정한 데이타가 있다면, 아래와 같이 확인가능합니다.
	$sReservedParam1  = $_POST['param_r1'];
	$sReservedParam2  = $_POST['param_r2'];
	$sReservedParam3  = $_POST['param_r3'];
	
		//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
    if(preg_match("/[#\&\\-%@\\\:;,\.\'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", $sResponseData, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam1, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam2, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReservedParam3, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// 암호화된 사용자 정보가 존재하는 경우
	if ($sResponseData != "") {



		/********************************************************************************************************************************************
			NICE신용평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
			
			서비스명 : 가상주민번호서비스 (IPIN) 서비스
			페이지명 : 가상주민번호서비스 (IPIN) 사용자 인증 정보 결과 페이지
			
					   수신받은 데이터(인증결과)를 복호화하여 사용자 정보를 확인합니다.
		*********************************************************************************************************************************************/
		
		$sSiteCode					= $_data->ipin_id;			// IPIN 서비스 사이트 코드		(NICE신용평가정보에서 발급한 사이트코드)
		$sSitePw						= $_data->ipin_password;			// IPIN 서비스 사이트 패스워드	(NICE신용평가정보에서 발급한 사이트패스워드)
		
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
					
							$sRtnMsg = "아이핀 본인인증이 정상적으로 완료 되었습니다.";
							
							$_SESSION[ipin][vno] = $arrData[1];
							$_SESSION[ipin][name] = $arrData[2];
							$_SESSION[ipin][dupinfo] = $arrData[3];
							if($arrData[5] == '1'){
								$_SESSION[ipin][gender] = '1';
							}else if($arrData[5] == '0'){
								$_SESSION[ipin][gender] = '2';
							}
							//$_SESSION[ipin][gender] = ($arrData[5]-1) * -1;
							$_SESSION[ipin][birthdate] = $arrData[6];

							$strVno      		= $arrData[1];	// 가상주민번호 (13자리이며, 숫자 또는 문자 포함)
							$strUserName		= $arrData[2];	// 이름
							$strDupInfo			= $arrData[3];	// 중복가입 확인값 (64Byte 고유값)
							$strAgeInfo			= $arrData[4];	// 연령대 코드 (개발 가이드 참조)
							$strGender			= $arrData[5];	// 성별 코드 (개발 가이드 참조)
							$strBirthDate		= $arrData[6];	// 생년월일 (YYYYMMDD)
							$strNationalInfo	= $arrData[7];	// 내/외국인 정보 (개발 가이드 참조)
							
							//아이핀 인증완료된 건에 대해 로그를 남긴다.(2016-03-10 추가)
							$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/ipin_logs_'.date("Ym").'/';
							$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
							$outText.= " 가상주민번호     : ".$strVno."\n";
							$outText.= " 이름                 : ".iconv("EUC-KR","UTF-8", $strUserName)."\n";
							$outText.= " 중복가입 확인값 : ".$strDupInfo."\n";
							$outText.= " 연령대 코드       : ".$strAgeInfo."\n";
							$outText.= " 성별 코드          : ".$strGender."\n";
							$outText.= " 생년월일           : ".$strBirthDate."\n";
							$outText.= " 내/외국인 정보   : ".$strNationalInfo."\n";

							if(!is_dir($textDir)){
								mkdir($textDir, 0700);
								chmod($textDir, 0777);
							}
							$outText.= "\n";
							$upQrt_f = fopen($textDir.'ipin_'.date("Ymd").'.txt','a');
							fwrite($upQrt_f, $outText );
							fclose($upQrt_f);
							chmod($textDir."ipin_".date("Ymd").".txt",0777);

							echo "<script>alert('$sRtnMsg'); opener.parent.ipin_chk('ipin','".iconv("EUC-KR","UTF-8", $strUserName)."');  self.close();</script>";
						
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

?>

<html>
<head>
	<title>NICE신용평가정보 가상주민번호 서비스</title>
</head>
<body>

<?
	} else {
?>

<html>
<head>
	<title>NICE신용평가정보 가상주민번호 서비스</title>
	<body onLoad="self.close()">

<?
	}
?>

</body>
</html>