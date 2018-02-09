<?php
	session_start();
	
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");
    //**************************************************************************************************************
    //NICE신용평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
    
    //서비스명 :  체크플러스 - 안심본인인증 서비스
    //페이지명 :  체크플러스 - 결과 페이지
    
    //보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 
    //**************************************************************************************************************
    
    $sitecode = $_data->realname_id;				// NICE로부터 부여받은 사이트 코드
    $sitepasswd = $_data->realname_password;			// NICE로부터 부여받은 사이트 패스워드
    

	$self_filename = basename($_SERVER['PHP_SELF']);
	$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
	$loc = substr($_SERVER['PHP_SELF'], 0, $loc);

	$cb_encode_path = $_SERVER[DOCUMENT_ROOT].$loc."CPClient";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
		
    $enc_data = $_POST["EncodeData"];		// 암호화된 결과 데이타
    $sReserved1 = $_POST['param_r1'];		
	$sReserved2 = $_POST['param_r2'];
	$sReserved3 = $_POST['param_r3'];

		//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
    if(preg_match("/[#\&\\-%@\\\:;,\.\'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", $enc_data, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved1, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved2, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved3, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		
    if ($enc_data != "") {

        $plaindata = `$cb_encode_path DEC $sitecode $sitepasswd $enc_data`;		// 암호화된 결과 데이터의 복호화

		if ($plaindata == -1){
            $returnMsg  = "암/복호화 시스템 오류";
            $returnCode  = "0";
        }else if ($plaindata == -4){
            $returnMsg  = "복호화 처리 오류";
            $returnCode  = "0";
        }else if ($plaindata == -5){
            $returnMsg  = "HASH값 불일치 - 복호화 데이터는 리턴됨";
            $returnCode  = "0";
        }else if ($plaindata == -6){
            $returnMsg  = "복호화 데이터 오류";
            $returnCode  = "0";
        }else if ($plaindata==-9){
            $returnMsg  = "입력값 오류";
            $returnCode  = "0";
        }else if ($plaindata == -12){
            $returnMsg  = "사이트 비밀번호 오류";
            $returnCode  = "0";
        }else{
			$returnMsg = "휴대폰 본인인증이 정상적으로 완료 되었습니다.";
            $returnCode  = "1";
            // 복호화가 정상적일 경우 데이터를 파싱합니다.
            $ciphertime = `$cb_encode_path CTS $sitecode $sitepasswd $enc_data`;	// 암호화된 결과 데이터 검증 (복호화한 시간획득)
        
            $requestnumber = GetValue($plaindata , "REQ_SEQ");
            $responsenumber = GetValue($plaindata , "RES_SEQ");
            $authtype = GetValue($plaindata , "AUTH_TYPE");
            $name = GetValue($plaindata , "NAME");
            $birthdate = GetValue($plaindata , "BIRTHDATE");
            $gender = GetValue($plaindata , "GENDER");
            $nationalinfo = GetValue($plaindata , "NATIONALINFO");	//내/외국인정보(사용자 매뉴얼 참조)
			$mobileno = GetValue($plaindata , "MOBILE_NO");	//휴대폰번호

			


            $dupinfo = GetValue($plaindata , "DI");
            $conninfo = GetValue($plaindata , "CI");
			
			//모바일 인증완료된 건에 대해 로그를 남긴다.(2016-03-10 - 김재수 추가)
			$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/checkplus_logs_'.date("Ym").'/';
			$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";

			$outText.= " 이름                                 : ".iconv("EUC-KR","UTF-8", $name)."\n";
			$outText.= " 중복가입 확인값 DI(64 byte) : ".$dupinfo."\n";
			$outText.= " 중복가입 확인값 CI(88 byte) : ".$conninfo."\n";
			$outText.= " 생년월일                           : ".$birthdate."\n";
			$outText.= " 성별 코드                          : ".$gender."\n";
			$outText.= " 내/외국인 정보                   : ".$nationalinfo."\n";

			if(!is_dir($textDir)){
				mkdir($textDir, 0700);
				chmod($textDir, 0777);
			}
			$outText.= "\n";
			$upQrt_f = fopen($textDir.'checkplus_'.date("Ymd").'.txt','a');
			fwrite($upQrt_f, $outText );
			fclose($upQrt_f);
			chmod($textDir."checkplus_".date("Ymd").".txt",0777);

			$_SESSION[ipin][name] = $name;
			$_SESSION[ipin][dupinfo] = $dupinfo;
			$_SESSION[ipin][conninfo] = $conninfo;
			//$_SESSION[ipin][gender] = ($gender-1) * -1;
			$_SESSION[ipin][gender] = $gender;
			$_SESSION[ipin][birthdate] = $birthdate;
			$_SESSION[ipin][mobileno] = $mobileno;
			
			//삭제 요함
			$_SESSION[ipin][plaindata]=$plaindata;
			
			//exdebug($plaindata);
			//exdebug($plaindata);
			//

			if(strcmp($_SESSION["REQ_SEQ"], $requestnumber) != 0)
            {
            	echo "세션값이 다릅니다. 올바른 경로로 접근하시기 바랍니다.<br>";
                $requestnumber = "";
                $responsenumber = "";
                $authtype = "";
                $name = "";
            		$birthdate = "";
            		$gender = "";
            		$nationalinfo = "";
            		$dupinfo = "";
            		$conninfo = "";
					$mobileno = "";
            }
        }
    }
			
	//모바일 인증 모든 건에 대해 로그를 남긴다.(2016-06-23 - 김재수 추가)
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/checkplus_logs_'.date("Ym").'/';
	$outText = '========================='.date("Y-m-d H:i:s")."=============================\n";

	$outText.= " plaindata   : ".iconv("EUC-KR","UTF-8", $plaindata)."\n";
	$outText.= " returnMsg  : ".$returnMsg."\n";
	$outText.= " returnCode : ".$returnCode."\n";
	$outText.= " access_type : pc\n";

	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$outText.= "\n";
	$upQrt_f = fopen($textDir.'checkplus_all_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."checkplus_all_".date("Ymd").".txt",0777);
?>

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

/*가입여부를 같은 화면에 레이어로 띄워야 하므로 페이지 이동을 하지 않고 
	가입 정보를 받아와야 함... 그래서 iframe에서 가입 체크해주는 페이지로 이동
*/
?>
<script>
alert('<?=$returnMsg?>');
opener.parent.ipin_chk('mobile','<?=iconv("EUC-KR","UTF-8", $name)?>');
window.close();
</script>
<!--html>
<head>
    <title>NICE신용평가정보 - CheckPlus 본인인증 테스트</title>
</head>
<body>
    <center>
    <p><p><p><p>
    <?=$returnMsg?><br>
    <table border=1>
        <tr>
            <td>복호화한 시간</td>
            <td><?= $ciphertime ?> (YYMMDDHHMMSS)</td>
        </tr>
        <tr>
            <td>요청 번호</td>
            <td><?= $requestnumber ?></td>
        </tr>            
        <tr>
            <td>나신평응답 번호</td>
            <td><?= $responsenumber ?></td>
        </tr>            
        <tr>
            <td>인증수단</td>
            <td><?= $authtype ?></td>
        </tr>
                <tr>
            <td>성명</td>
            <td><?= $name ?></td>
        </tr>
                <tr>
            <td>생년월일</td>
            <td><?= $birthdate ?></td>
        </tr>
                <tr>
            <td>성별</td>
            <td><?= $gender ?></td>
        </tr>
                <tr>
            <td>내/외국인정보</td>
            <td><?= $nationalinfo ?></td>
        </tr>
                <tr>
            <td>DI(64 byte)</td>
            <td><?= $dupinfo ?></td>
        </tr>
                <tr>
            <td>CI(88 byte)</td>
            <td><?= $conninfo ?></td>
        </tr>
        <tr>
          <td>RESERVED1</td>
          <td><?= $sReserved1 ?></td>
	      </tr>
	      <tr>
	          <td>RESERVED2</td>
	          <td><?= $sReserved2 ?></td>
	      </tr>
	      <tr>
	          <td>RESERVED3</td>
	          <td><?= $sReserved3 ?></td>
	      </tr>
    </table>
    </center>
</body>
</html-->
