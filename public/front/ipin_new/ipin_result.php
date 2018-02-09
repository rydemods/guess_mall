<html>
<head>
	<title>NICE평가정보 가상주민번호 서비스</title>
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
		NICE평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
		
		서비스명 : 가상주민번호서비스 (IPIN) 서비스
		페이지명 : 가상주민번호서비스 (IPIN) 사용자 인증 정보 결과 페이지
		
				   수신받은 데이터(인증결과)를 복호화하여 사용자 정보를 확인합니다.
	*********************************************************************************************************************************************/
	
	$sSiteCode				= "BB42";			// IPIN 서비스 사이트 코드		(NICE평가정보에서 발급한 사이트코드)
	$sSitePw					= "Sinwon1!";			// IPIN 서비스 사이트 패스워드	(NICE평가정보에서 발급한 사이트패스워드)

  $sEncData = $_POST['enc_data'];	// ipin_process.php 에서 리턴받은 암호화 된 사용자 인증 정보
    
	//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
    if(preg_match('~[^0-9a-zA-Z+/=]~', $sEncData, $match)) {echo "입력 값 확인이 필요합니다"; exit;}
    if(base64_encode(base64_decode($sEncData))!=$sEncData) {echo " 입력 값 확인이 필요합니다"; exit;}		
  //////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
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
	$sModulePath				= "";
	
	// ipin_main.php 에서 저장한 세션 정보를 추출합니다.
	// 데이타 위변조 방지를 위해 확인하기 위함이므로, 필수사항은 아니며 보안을 위한 권고사항입니다.
  $sCPRequest = $_SESSION['CPREQUEST'];
   
	$sDecData					= "";			// 복호화 된 사용자 인증 정보
	$sRtnMsg					= "";			// 처리결과 메세지
    
    if ($sEncData != "") {
    
    	// 사용자 정보를 복호화 합니다.
    	// 실행방법은 싱글쿼터(`) 외에도, 'exec(), system(), shell_exec()' 등등 귀사 정책에 맞게 처리하시기 바랍니다.
    	$sDecData = `$sModulePath RES $sSiteCode $sSitePw $sEncData`;
    	
    	if ($sDecData == -9) {
    		$sRtnMsg = "입력값 오류 : 복호화 처리시, 필요한 파라미터값의 정보를 정확하게 입력해 주시기 바랍니다.";
    	} else if ($sDecData == -12) {
    		$sRtnMsg = "NICE평가정보에서 발급한 개발정보가 정확한지 확인해 보세요.";
    	} else {
    	
    		/*
    			- 복호화된 데이타 구성
    			'데이터에 대한 byte:데이터' 형식으로 구성되어 있습니다.
    		*/
    		$arrData = split(":", $sDecData);
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
				
				$strResultCode		= GetValue($sDecData, "RESULT_CODE");			// 결과코드
				if ($strResultCode == 1) {
					$strCPRequest	= GetValue($sDecData, "CPREQUESTNO");			// CP 요청번호
					
					if ($sCPRequest == $strCPRequest) {
				
//						$sRtnMsg = "사용자 인증 성공";
						$sRtnMsg = "아이핀 본인인증이 정상적으로 완료 되었습니다.";

						$_SESSION[ipin][vno] = GetValue($sDecData, "VNUMBER");
						$_SESSION[ipin][name] = GetValue($sDecData, "NAME");
						$_SESSION[ipin][dupinfo] = GetValue($sDecData, "DUPINFO");
						$_SESSION[ipin][conninfo] = GetValue($sDecData, "COINFO1");
						if(GetValue($sDecData, "GENDERCODE") == '1'){
							$_SESSION[ipin][gender] = '1';
						}else if(GetValue($sDecData, "GENDERCODE") == '0'){
							$_SESSION[ipin][gender] = '2';
						}
						$_SESSION[ipin][birthdate] = GetValue($sDecData, "BIRTHDATE");

						$strVnumber			= GetValue($sDecData, "VNUMBER");			// 가상주민번호 (13자리이며, 숫자 또는 문자 포함)
						$strUserName		= GetValue($sDecData, "NAME");				// 이름
						$strDupInfo			= GetValue($sDecData, "DUPINFO");			// 중복가입 확인값 (DI - 64 byte 고유값)
						$strGender			= GetValue($sDecData, "GENDERCODE");	// 성별 코드 (개발 가이드 참조)
						$strAgeInfo			= GetValue($sDecData, "AGECODE");			// 연령대 코드 (개발 가이드 참조)
						$strBirthDate		= GetValue($sDecData, "BIRTHDATE");		// 생년월일 (YYYYMMDD)
						$strNationalInfo	= GetValue($sDecData, "NATIONALINFO");	// 내/외국인 정보 (개발 가이드 참조)
						$strAuthInfo		= GetValue($sDecData, "AUTHMETHOD");	// 본인확인 수단 (개발 가이드 참조)
						$strCoInfo			= GetValue($sDecData, "COINFO1");			// 연계정보 확인값 (CI - 88 byte 고유값)
						$strCIUpdate		= GetValue($sDecData, "CIUPDATE");		// CI 갱신정보

						//아이핀 인증완료된 건에 대해 로그를 남긴다.(2018-01-26 - 김재수 추가)
						$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/ipin_logs_new'.date("Ym").'/';
						$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
						$outText.= " 가상주민번호     : ".$strVnumber."\n";
						$outText.= " 이름                 : ".iconv("EUC-KR","UTF-8", $strUserName)."\n";
						$outText.= " 중복가입 확인값(DI) : ".$strDupInfo."\n";
						$outText.= " 중복가입 확인값(CI) : ".$strCoInfo."\n";
						$outText.= " CI 갱신정보 : ".$strCIUpdate."\n";
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

						echo "<script>parent.ipin_chk('ipin','".iconv("EUC-KR","UTF-8", $strUserName)."');</script>";

					} else {
						$sRtnMsg = "CP 요청번호 불일치 : 세션에 넣은 $sCPRequest 데이타를 확인해 주시기 바랍니다.";
					}
				} else {
					$sRtnMsg = "리턴값 확인 후, NICE평가정보 개발 담당자에게 문의해 주세요. [$strResultCode]";
				}
    		
    		} else {
    			$sRtnMsg = "리턴값 확인 후, NICE평가정보 개발 담당자에게 문의해 주세요. [$iCount]";
    		}
    	
    	}
    } else {
    	$sRtnMsg = "처리할 암호화 데이타가 없습니다.";
    }
    
?>

	처리결과 : <?= $sRtnMsg ?><br>
	이름 : <?= $strUserName ?><br>

	<form name="user" method="post">
		<input type="hidden" name="enc_data" value="<?= $sEncData ?>"><br>
	</form>
</body>
</html>

<?
    function GetValue($str , $name)
    {
        $pos1 = 0;  //length의 시작 위치
        $pos2 = 0;  //:의 위치

        while( $pos1 <= strlen($str) )
        {
            $pos2 = strpos( $str , ":" , $pos1);
            $len = substr($str , $pos1 , $pos2 - $pos1);
            $key = substr($str , $pos2 + 1 , $len);
            $pos1 = $pos2 + $len + 1;
            if( $key == $name )
            {
                $pos2 = strpos( $str , ":" , $pos1);
                $len = substr($str , $pos1 , $pos2 - $pos1);
                $value = substr($str , $pos2 + 1 , $len);
                return $value;
            }
            else
            {
                // 다르면 스킵한다.
                $pos2 = strpos( $str , ":" , $pos1);
                $len = substr($str , $pos1 , $pos2 - $pos1);
                $pos1 = $pos2 + $len + 1;
            }            
        }
    }
?>