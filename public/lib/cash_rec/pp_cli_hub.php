<?
    /* ============================================================================== */
    /* =   PAGE : 등록/변경 처리 PAGE                                               = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2007   KCP Inc.   All Rights Reserved.                    = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* = 라이브러리 및 사이트 정보 include                                          = */
    /* = -------------------------------------------------------------------------- = */

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store");
    header("Pragma: no-cache");
	header("Content-type: text/html; charset=EUC-KR");
	setlocale(LC_CTYPE, "ko_KR.EUC-KR");
	//header("Content-type: text/html; charset=UTF-8");
	$Dir="../../";

    require "./pp_cli_hub_lib.php";
    include "./site.conf";

	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
    /* ============================================================================== */
	$ordercode = $_POST['ordercode'];

	$dataShop = pmysql_fetch("SELECT tax_cnum,tax_cname,tax_cowner,tax_caddr,tax_ctel,tax_type,tax_rate,tax_mid,tax_tid FROM tblshopinfo", get_db_conn());

	$dataRec = pmysql_fetch("SELECT * FROM tbltaxsavelist WHERE ordercode='".$ordercode."'");
	
	if($_POST['flag'] == 'C'){
		$_POST["req_tx"] = "mod";
		$_POST["mod_type"] = "STSC";
		$_POST["mod_value"] = $dataRec['authno'];
		$_POST["mod_gubn"] = "MG02";
	}else{
		$_POST["req_tx"] = "pay";
	}

	if($_POST["req_tx"] == "pay"){
		$tsdtime = date("YmdHis");
	}else{
		$tsdtime = $dataRec['tsdtime'];
	}

	pmysql_free_result($result);

    /* ============================================================================== */
    /* =   01. 요청 정보 설정                                                       = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx = $_POST["req_tx"];									# 요청 종류	승인 : pay, 변경 : mod
    $trad_time = $tsdtime;												# 원거래 시각  $tsdtime
    /* = -------------------------------------------------------------------------- = */
    $ordr_idxx = $ordercode;											# 주문 번호 $ordercode
    $buyr_name = iconv( 'UTF-8', 'EUC-KR', $dataRec['name'] );								# 주문자 이름 $dataRec['name']
    $buyr_tel1 = 'test';										# 주문자 전화번호	 $dataRec['tel']
    $buyr_mail = $dataRec['email'];								# 주문자 E-Mail	 $dataRec['email']
    $good_name = iconv( 'UTF-8', 'EUC-KR', $dataRec['productname'] );				# 상품 정보	 $dataRec['productname']
    $comment = $_POST["comment"];							# 비고
    /* = -------------------------------------------------------------------------- = */
	$_POST["corp_type"] = "0";
	$_POST["corp_tax_type"] = "TG01";
    $corp_type = $_POST["corp_type"];							# 사업장 구분	0 : 직접 판매, 1 : 입점몰 판매
    $corp_tax_type = $_POST["corp_tax_type"];				# 과세/면세 구분	TG01 : 과세, TG02 : 면세
    $corp_tax_no = $dataShop['tax_cnum'];					# 발행 사업자 번호	$dataShop['tax_cnum']
    $corp_nm = $dataShop['tax_cname'];						# 상호	$dataShop['tax_cname']
    $corp_owner_nm = $dataShop['tax_cowner'];			# 대표자명	$dataShop['tax_cowner']
    $corp_addr = $dataShop['tax_caddr'];						# 사업장 주소	$dataShop['tax_caddr']
    $corp_telno = $dataShop['tax_ctel'];						# 사업장 대표 연락처	$dataShop['tax_ctel']
    /* = -------------------------------------------------------------------------- = */
    $tr_code = $dataRec['tr_code'];								# 발행용도	0 : 소득공제용, 1 : 지출증빙용 $dataRec['tr_code']
    $id_info = $dataRec['id_info'];									# 신분확인 ID	$dataRec['id_info']
    $amt_tot = $dataRec['amt1'];									# 거래금액 총 합	$dataRec['amt1']
    $amt_sup = $dataRec['amt2'];									# 공급가액	$dataRec['amt2']
    $amt_svc = $dataRec['amt3'];									# 봉사료	$dataRec['amt3']
    $amt_tax = $dataRec['amt4'];									# 부가가치세	$dataRec['amt4']
    /* = -------------------------------------------------------------------------- = */
    $mod_type = $_POST["mod_type"];							# 변경 타입
    $mod_value = $_POST["mod_value"];						# 변경 요청 거래번호
    $mod_gubn = $_POST["mod_gubn"];						# 변경 요청 거래번호 구분
    $mod_mny = $_POST["mod_mny"];							# 변경 요청 금액
    $rem_mny = $_POST["rem_mny"];							# 변경처리 이전 금액
    /* = -------------------------------------------------------------------------- = */
    $cust_ip = getenv( "REMOTE_ADDR" );						# 요청 IP
    /* ============================================================================== */

	/*
	$ary[] = "UTF-8"; 
	$ary[] = "EUC-KR"; 
	$ary[] = "ASCII"; 
	$ary[] = "JIS"; 
	$ary[] = "eucjp-win"; 
	$ary[] = "sjis-win"; 
	$ary[] = "euc-jp"; 
	$sCharset = mb_detect_encoding($buyr_name, $ary);
	sdebug($sCharset);
	exit;
	*/

	$recTranFlag = "0";													# 현금 영수증 발급 상태 0 : 등록 실패, 1 : 등록 성공, 2 : 취소 실패, 3 : 취소 성공

	/* ============================================================================== */
    /* =   02. 인스턴스 생성 및 초기화                                              = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus  = new C_PAYPLUS_CLI;
    $c_PayPlus->mf_clear();
    /* ============================================================================== */


	/* ============================================================================== */
    /* =   03. 처리 요청 정보 설정, 실행                                            = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 승인 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
	// 업체 환경 정보
	if ( $req_tx == "pay" )
	{
		$tx_cd = "07010000"; // 현금영수증 등록 요청

		// 현금영수증 정보
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "user_type",      $g_conf_user_type );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "trad_time",      $trad_time        );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "tr_code",        $tr_code          );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "id_info",        $id_info          );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tot",        $amt_tot          );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_sup",        $amt_sup          );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_svc",        $amt_svc          );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tax",        $amt_tax          );
		$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_type",       "PAXX"            ); // 선 결제 서비스 구분(PABK - 계좌이체, PAVC - 가상계좌, PAXX - 기타)
		//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_trade_no",   $pay_trade_no ); // 결제 거래번호(PABK, PAVC일 경우 필수)
		//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_tx_id",      $pay_tx_id    ); // 가상계좌 입금통보 TX_ID(PAVC일 경우 필수)

		// 주문 정보
		$good_name = str_replace(","," ",$good_name);
		$c_PayPlus->mf_set_ordr_data( "ordr_idxx",  $ordr_idxx );
		$c_PayPlus->mf_set_ordr_data( "good_name",  $good_name );
		$c_PayPlus->mf_set_ordr_data( "buyr_name",  $buyr_name );
		$c_PayPlus->mf_set_ordr_data( "buyr_tel1",  $buyr_tel1 );
		$c_PayPlus->mf_set_ordr_data( "buyr_mail",  $buyr_mail );
		$c_PayPlus->mf_set_ordr_data( "comment",    $comment   );

		// 가맹점 정보
		$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_type",       $corp_type     );

		if ( $corp_type == "1" ) // 입점몰인 경우 판매상점 DATA 전문 생성
		{
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_tax_type",   $corp_tax_type );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_tax_no",     $corp_tax_no   );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_sell_tax_no",$corp_tax_no   );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_nm",         $corp_nm       );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_owner_nm",   $corp_owner_nm );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_addr",       $corp_addr     );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_telno",      $corp_telno    );
		}

		$c_PayPlus->mf_set_ordr_data( "rcpt_data", $rcpt_data_set );
		$c_PayPlus->mf_set_ordr_data( "corp_data", $corp_data_set );
	}
	/* = -------------------------------------------------------------------------- = */
	/* =   03-2. 취소 요청                                                          = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod" )
	{
		if ( $mod_type == "STSQ" )
		{
			$tx_cd = "07030000"; // 조회 요청
		}
		else
		{
			$tx_cd = "07020000"; // 취소 요청
		}

		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type   );      // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_value",  $mod_value  );
		$c_PayPlus->mf_set_modx_data( "mod_gubn",   $mod_gubn   );
		$c_PayPlus->mf_set_modx_data( "trad_time",  $trad_time  );

		if ( $mod_type == "STPC" ) // 부분취
		{
			$c_PayPlus->mf_set_modx_data( "mod_mny",  $mod_mny  );
			$c_PayPlus->mf_set_modx_data( "rem_mny",  $rem_mny  );
		}
	}

	/* ============================================================================== */
	/* =   03-3. 실행                                                               = */
	/* ------------------------------------------------------------------------------ */
	if ( strlen($tx_cd) > 0 )
	{
		$c_PayPlus->mf_do_tx( "", $g_conf_home_dir, $g_conf_site_id, "", $tx_cd, "", $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx, $cust_ip, $g_conf_log_level, "", $g_conf_tx_mode );
	}
	else
	{
		$c_PayPlus->m_res_cd  = "9562";
		$c_PayPlus->m_res_msg = "연동 오류";
	}
	$res_cd  = $c_PayPlus->m_res_cd;                      // 결과 코드
	$res_msg = $c_PayPlus->m_res_msg;                     // 결과 메시지

	/* ============================================================================== */
	/* =   04. 승인 결과 처리                                                       = */
	/* = -------------------------------------------------------------------------- = */
	if ( $req_tx == "pay" )
	{
		if ( $res_cd == "0000" )
		{
			$cash_no    = $c_PayPlus->mf_get_res_data( "cash_no"    );       // 현금영수증 거래번호
			$receipt_no = $c_PayPlus->mf_get_res_data( "receipt_no" );       // 현금영수증 승인번호
			$app_time   = $c_PayPlus->mf_get_res_data( "app_time"   );       // 승인시간(YYYYMMDDhhmmss)
			$reg_stat   = $c_PayPlus->mf_get_res_data( "reg_stat"   );       // 등록 상태 코드
			$reg_desc   = $c_PayPlus->mf_get_res_data( "reg_desc"   );       // 등록 상태 설명

			/* = -------------------------------------------------------------------------- = */
			/* =   04-1. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
			/* = -------------------------------------------------------------------------- = */
			/* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
			/* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
			/* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
			/* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"     = */
			/* =         로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
			/* =         값을 세팅하시면 됩니다.)                                           = */
			/* = -------------------------------------------------------------------------- = */

			$bSucc = "";	// DB 작업 실패일 경우 "false" 로 세팅

			/* = -------------------------------------------------------------------------- = */
			/* =   04-2. DB 작업 실패일 경우 자동 승인 취소                                 = */
			/* = -------------------------------------------------------------------------- = */
			if ( $bSucc == "false" )
			{
				$c_PayPlus->mf_clear();

				$tx_cd = "07020000"; // 취소 요청

				$c_PayPlus->mf_set_modx_data( "mod_type",  "STSC"     );                    // 원거래 변경 요청 종류
				$c_PayPlus->mf_set_modx_data( "mod_value", $cash_no   );
				$c_PayPlus->mf_set_modx_data( "mod_gubn",  "MG01"     );
				$c_PayPlus->mf_set_modx_data( "trad_time", $trad_time );

				$c_PayPlus->mf_do_tx( "", $g_conf_home_dir, $g_conf_site_id, "", $tx_cd, "", $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx, $cust_ip, $g_conf_log_level, "", $g_conf_tx_mode );

				$res_cd  = $c_PayPlus->m_res_cd;
				$res_msg = $c_PayPlus->m_res_msg;
				$recTranFlag = "0";
			}
			$recTranFlag = "1";
		}else{
			$recTranFlag = "0";
		}
	}else if ( $req_tx == "mod" ){
		/* ============================================================================== */
		/* =	05. 변경 결과 처리	= */
		/* = -------------------------------------------------------------------------- = */
		if ( $res_cd == "0000" )
		{
			$cash_no    = $c_PayPlus->mf_get_res_data( "cash_no"    );       // 현금영수증 거래번호
			$receipt_no = $c_PayPlus->mf_get_res_data( "receipt_no" );       // 현금영수증 승인번호
			$app_time   = $c_PayPlus->mf_get_res_data( "app_time"   );       // 승인시간(YYYYMMDDhhmmss)
			$reg_stat   = $c_PayPlus->mf_get_res_data( "reg_stat"   );       // 등록 상태 코드
			$reg_desc   = $c_PayPlus->mf_get_res_data( "reg_desc"   );       // 등록 상태 설명
			$recTranFlag = "3";
		}else{
			$recTranFlag = "2";
		}
	}


	# $recTranFlag 현금 영수증 발급 상태 0 : 등록 실패, 1 : 등록 성공, 2 : 취소 실패, 3 : 취소 성공
	if($recTranFlag == "0"){
		# 등록 실패
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', error_msg = '".$res_msg."' WHERE ordercode = '".$ordercode."'";
		pmysql_query($sql,get_db_conn());

		$result_msg = "현금영수증 발급이 실패하였습니다. \\n\\n--------------------실패사유--------------------\\n\\n".$res_msg;

	}else if($recTranFlag == "1"){
		# 등록 성공
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', type = 'Y', authno = '".$receipt_no."', oktime = '".$tsdtime."', error_msg = '' WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());

		$result_msg = "현금영수증 발급이 정상적으로 처리되었습니다.";

	}else if($recTranFlag == "2"){
		# 취소 실패
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', error_msg = '".$res_msg."' WHERE ordercode = '".$ordercode."'";
		pmysql_query($sql,get_db_conn());

		$result_msg = "현금영수증 취소가 실패하였습니다. \\n\\n--------------------실패사유--------------------\\n\\n".$res_msg;

	}else if($recTranFlag == "3"){
		# 취소 성공
		$sql = "UPDATE tbltaxsavelist SET tsdtime = '".$tsdtime."', type = 'C', authno = '".$receipt_no."', error_msg	= '' WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());

		$result_msg="현금영수증 취소가 정상적으로 처리되었습니다.";

	}

	$c_PayPlus->mf_clear();

	alert_go($result_msg, "/admin/order_taxsavelist.php");

?>

<!--원래 pp_cli_hub_bottom.php 에 있던 내용이 있던 자리 -->