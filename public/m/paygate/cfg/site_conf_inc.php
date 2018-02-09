<?

	define("enckeyConf", "password");

	//에스크로 설정정보 읽어옴
	function GetEscrowTypeConf($escrow_info) {
		$val = array();
		$list = explode("|",$escrow_info);
		for ($i=0;$i<count($list); $i++) {
			$data = explode("=",$list[$i]);
			$val[$data[0]] = $data[1];
		}
		return $val;
	}

	function getPgdataConf() {
		$arrDataId=array();
		if($f=@file("../../authkey/pg")) {
			for($i=0;$i<count($f);$i++) {
				$f[$i]=trim($f[$i]);
				if (strpos($f[$i],"escrow_id:::")===0) $arrDataId['E']=decrypt_authkeyConf(substr($f[$i],12));
				elseif (strpos($f[$i],"trans_id:::")===0) $arrDataId['T']=decrypt_authkeyConf(substr($f[$i],11));
				elseif (strpos($f[$i],"virtual_id:::")===0) $arrDataId['V']=decrypt_authkeyConf(substr($f[$i],13));
				elseif (strpos($f[$i],"card_id:::")===0) $arrDataId['C']=decrypt_authkeyConf(substr($f[$i],10));
				elseif (strpos($f[$i],"mobile_id:::")===0) $arrDataId['M']=decrypt_authkeyConf(substr($f[$i],12));
			}
		}
		return $arrDataId;
	}

	function decrypt_authkeyConf($str) {
		return decrypt_md5Conf($str,"*ghkddnjsrl*");
	}

	function decrypt_md5Conf($hex_buf,$key="") {
			if(ord($key)==0) $key=enckeyConf;
			$len = strlen($hex_buf);
			$buf = '';
			$ret_buf = '';
			$buf = pack("H*",$hex_buf);
			$key1 = pack("H*", md5($key));
			while($buf) {
					$m = substr($buf, 0, 16);
					$buf = substr($buf, 16);

					$c = "";
					$len_m = strlen($m);
					$len_key1 = strlen($key1);
					for($i=0;$i<16;$i++) {
							$m1 = ($len_m>$i) ? $m{$i} : 0;
							$m2 = ($len_key1>$i) ? $key1{$i} : 0;
							if($len_m>$i)
							$c .= $m1^$m2;
					}
					$ret_buf .= $m = $c;
					$key1 = pack("H*",md5($key.$key1.$m));
			}
			$ret_buf=rtrim($ret_buf,'0');
			return($ret_buf);
	}


	$arrDataId = getPgdataConf();

	$pgid_info = GetEscrowTypeConf($arrDataId['C']);

    /* ============================================================================== */
    /* =   PAGE : 결제 정보 환경 설정 PAGE                                          = */
    /* =----------------------------------------------------------------------------= */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do			        = */
    /* =----------------------------------------------------------------------------= */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* = g_conf_site_cd, g_conf_site_key 설정                                       = */
    /* = 실결제시 KCP에서 발급한 사이트코드(site_cd), 사이트키(site_key)를 반드시   = */
    /* = 변경해 주셔야 결제가 정상적으로 진행됩니다.                                = */
    /* =----------------------------------------------------------------------------= */
    /* = 테스트 시 : 사이트코드(T0000)와 사이트키(3grptw1.zW0GSo4PQdaGvsF__)로      = */
    /* =            설정해 주십시오.                                                = */
    /* = 실결제 시 : 반드시 KCP에서 발급한 사이트코드(site_cd)와 사이트키(site_key) = */
    /* =            로 설정해 주십시오.                                             = */
    /* ============================================================================== */
    $g_conf_site_cd   = $pgid_info['ID'];
    $g_conf_site_key  = $pgid_info['KEY'];
	#$g_conf_site_cd   = 'T0000';
    #$g_conf_site_key  = '3grptw1.zW0GSo4PQdaGvsF__';



    /* ============================================================================== */
    /* = ※ 주의 ※                                                                 = */
    /* = * g_conf_home_dir 변수 설정                                                = */
    /* =----------------------------------------------------------------------------= */
    /* =   BIN 절대 경로 입력 (bin전까지 설정						                = */
    /* ============================================================================== */
	//duometis001@116.122.37.141:/home/duometis001/nexolve/public/m/paygate/cfg/site_conf_inc.php
   // $g_conf_home_dir  = "/data1/newpg/pay/kimhj/ax_hub_linux_jsp_add";       // BIN 절대경로 입력 (bin전까지) 
    $g_conf_home_dir  = $_SERVER['DOCUMENT_ROOT']."/m/paygate/receipt";       // BIN 절대경로 입력 (bin전까지) 
    
    /* ============================================================================== */
    /* = ※ 주의 ※                                                                 = */
    /* = * g_conf_gw_url 설정                                                       = */
    /* =----------------------------------------------------------------------------= */
    /* = 테스트 시 : testpaygw.kcp.co.kr로 설정해 주십시오.                         = */
    /* = 실결제 시 : paygw.kcp.co.kr로 설정해 주십시오.                             = */
    /* ============================================================================== */
	if($g_conf_site_cd == 'T0000' || $g_conf_site_cd == 'T0007'){
		$g_conf_gw_url = "testpaygw.kcp.co.kr";
	}else{
		$g_conf_gw_url = "paygw.kcp.co.kr";
	}

    /* ============================================================================== */
    /* = ※ 주의 ※                                                                 = */
    /* = * g_conf_js_url 설정                                                       = */
    /* =----------------------------------------------------------------------------= */
	/* = 테스트 시 : src="http://pay.kcp.co.kr/plugin/payplus_test.js"              = */
	/* =             src="https://pay.kcp.co.kr/plugin/payplus_test.js"             = */
    /* = 실결제 시 : src="http://pay.kcp.co.kr/plugin/payplus.js"                   = */
	/* =             src="https://pay.kcp.co.kr/plugin/payplus.js"                  = */
    /* =                                                                            = */
	/* = 테스트 시(UTF-8) : src="http://pay.kcp.co.kr/plugin/payplus_test_un.js"    = */
	/* =                    src="https://pay.kcp.co.kr/plugin/payplus_test_un.js"   = */
    /* = 실결제 시(UTF-8) : src="http://pay.kcp.co.kr/plugin/payplus_un.js"         = */
	/* =                    src="https://pay.kcp.co.kr/plugin/payplus_un.js"        = */
    /* ============================================================================== */
	if($g_conf_site_cd == 'T0000' || $g_conf_site_cd == 'T0007'){
		$g_conf_js_url = "https://pay.kcp.co.kr/plugin/payplus_test_un.js";
	}else{
	  $g_conf_js_url = "https://pay.kcp.co.kr/plugin/payplus_un.js";
	}

    /* ============================================================================== */
    /* = 스마트폰 SOAP 통신 설정                                                     = */
    /* =----------------------------------------------------------------------------= */
    /* = 테스트 시 : KCPPaymentService.wsdl                                         = */
    /* = 실결제 시 : real_KCPPaymentService.wsdl                                    = */
    /* ============================================================================== */
   
	if($g_conf_site_cd == 'T0000' || $g_conf_site_cd == 'T0007'){
		$g_wsdl = "KCPPaymentService.wsdl";
	}else{
		$g_wsdl = "real_KCPPaymentService.wsdl";
	}
	/*
	$f = fopen("kcpTest.txt","a+");
	fwrite($f,"g_conf_site_cd : ".$g_conf_site_cd."\r\n");
	fwrite($f,"g_conf_site_key : ".$g_conf_site_key."\r\n");
	fclose($f);
	chmod("kcpTest.txt",0777);
	*/

    /* ============================================================================== */
    /* = g_conf_site_name 설정                                                      = */
    /* =----------------------------------------------------------------------------= */
    /* = 사이트명 설정(한글 불가) : 반드시 영문자로 설정하여 주시기 바랍니다.       = */
    /* ============================================================================== */
    $g_conf_site_name = "shinwon";

	/*
	$f = fopen("kcpTest.txt","a+");
	fwrite($f,"g_conf_site_cd : ".$g_conf_site_cd."\r\n");
	fwrite($f,"g_conf_site_key : ".$g_conf_site_key."\r\n");
	fwrite($f,"g_conf_gw_url : ".$g_conf_gw_url."\r\n");
	fwrite($f,"g_conf_js_url : ".$g_conf_js_url."\r\n");
	fwrite($f,"g_wsdl : ".$g_wsdl."\r\n");
	fclose($f);
	chmod("kcpTest.txt",0777);
	*/


    /* ============================================================================== */
    /* = 지불 데이터 셋업 (변경 불가)                                               = */
    /* ============================================================================== */
    $g_conf_log_level = "3";
    $g_conf_gw_port   = "8090";        // 포트번호(변경불가)
	$module_type      = "01";          // 변경불가
?>
