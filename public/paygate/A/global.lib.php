<?php
header("Content-Type: text/html; charset=UTF-8");
/* ============================================================================== */
/* =   01. 지불 데이터 셋업 (업체에 맞게 수정)                                  = */
/* = -------------------------------------------------------------------------- = */
$g_conf_home_dir	= $Dir."paygate/A/payplus";	// BIN 절대경로 입력
$g_conf_log_level	= "3";											// 변경불가
//$g_conf_pa_url		= "testpaygw.kcp.co.kr";							// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
$g_conf_pa_url		= "paygw.kcp.co.kr";							// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
$g_conf_pa_port		= "8090";										// 포트번호 , 변경불가
$g_conf_mode		= 0;											// 변경불가
$g_conf_log_path = $Dir."paygate/A/payplus/log";


/* ============================================================================== */
/* =   02. 지불 요청 정보 설정                                                  = */
/* = -------------------------------------------------------------------------- = */
$site_cd        = $_POST[  "site_cd"         ];             // 사이트 코드
$site_key       = $_POST[  "site_key"        ];             // 사이트 키
$req_tx         = $_POST[  "req_tx"          ];             // 요청 종류
$cust_ip        = $_SERVER[  "REMOTE_ADDR"   ];             // 요청 IP
$ordr_idxx      = $_POST[  "ordr_idxx"       ];             // 쇼핑몰 주문번호
$good_name      = $_POST[  "good_name"       ];             // 상품명
/* = -------------------------------------------------------------------------- = */
$good_mny       = $_POST[  "good_mny"        ];             // 결제 총금액
$tran_cd        = $_POST[  "tran_cd"         ];             // 처리 종류
/* = -------------------------------------------------------------------------- = */
$res_cd         = "";                                       // 응답코드
$res_msg        = "";                                       // 응답메시지
$tno            = $_POST[  "tno"             ];             // KCP 거래 고유 번호
/* = -------------------------------------------------------------------------- = */
$buyr_name      = $_POST[  "buyr_name"       ];             // 주문자명
$buyr_tel1      = $_POST[  "buyr_tel1"       ];             // 주문자 전화번호
$buyr_tel2      = $_POST[  "buyr_tel2"       ];             // 주문자 핸드폰 번호
$buyr_mail      = $_POST[  "buyr_mail"       ];             // 주문자 E-mail 주소
/* = -------------------------------------------------------------------------- = */
$bank_name      = "";                                       // 은행명
$bank_issu      = $_POST[  "bank_issu"       ];             // 계좌이체 서비스사
/* = -------------------------------------------------------------------------- = */
$commid         = "";                         // 통신사 코드
$mobile_no      = "";                         // 휴대폰 코드
/* = -------------------------------------------------------------------------- = */
$mod_type       = $_POST[  "mod_type"        ];             // 변경TYPE VALUE 승인취소시 필요
$mod_desc       = $_POST[  "mod_desc"        ];             // 변경사유
/* = -------------------------------------------------------------------------- = */
$use_pay_method = $_POST[  "use_pay_method"  ];             // 결제 방법
$bSucc          = "";                                       // 업체 DB 처리 성공 여부
$acnt_yn        = $_POST[  "acnt_yn"         ];             // 상태변경시 계좌이체, 가상계좌 여부
/* = -------------------------------------------------------------------------- = */
$card_cd        = "";                                       // 신용카드 코드
$card_name      = "";                                       // 신용카드 명
$app_time       = "";                                       // 승인시간 (모든 결제 수단 공통)
$app_no         = "";                                       // 신용카드 승인번호
$noinf          = "";                                       // 신용카드 무이자 여부
$quota          = "";                                       // 신용카드 할부개월
$bankname       = "";                                       // 은행명
$depositor      = "";                                       // 입금 계좌 예금주 성명
$account        = "";                                       // 입금 계좌 번호

/* = -------------------------------------------------------------------------- = */
$cash_yn        = $_POST[ "cash_yn"        ]; // 현금영수증 등록 여부
$cash_authno    = "";                         // 현금 영수증 승인 번호
$cash_tr_code   = $_POST[ "cash_tr_code"   ]; // 현금 영수증 발행 구분
$cash_id_info   = $_POST[ "cash_id_info"   ]; // 현금 영수증 등록 번호
/* = -------------------------------------------------------------------------- = */

$escw_used      = $_POST[  "escw_used"       ];             // 에스크로 사용 여부
$pay_mod        = $_POST[  "pay_mod"         ];             // 에스크로 결제처리 모드
$deli_term      = $_POST[  "deli_term"       ];             // 배송 소요일
$bask_cntx      = $_POST[  "bask_cntx"       ];             // 장바구니 상품 개수
$good_info      = $_POST[  "good_info"       ];             // 장바구니 상품 상세 정보
$rcvr_name      = $_POST[  "rcvr_name"       ];             // 수취인 이름
$rcvr_tel1      = $_POST[  "rcvr_tel1"       ];             // 수취인 전화번호
$rcvr_tel2      = $_POST[  "rcvr_tel2"       ];             // 수취인 휴대폰번호
$rcvr_mail      = $_POST[  "rcvr_mail"       ];             // 수취인 E-Mail
$rcvr_zipx      = $_POST[  "rcvr_zipx"       ];             // 수취인 우편번호
$rcvr_add1      = $_POST[  "rcvr_add1"       ];             // 수취인 주소
$rcvr_add2      = $_POST[  "rcvr_add2"       ];             // 수취인 상세주소
$escw_yn        = "";                         // 에스크로 여부
/* ============================================================================== */


/* ============================================================================== */
/* =   PAGE : 라이브버리 PAGE                                                   = */
/* = -------------------------------------------------------------------------- = */
/* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.                   = */
/* ============================================================================== */

/* ============================================================================== */
/* =   지불 연동 CLASS                                                          = */
/* ============================================================================== */
class   C_PP_CLI {
	var   $m_payx_common;
	var   $m_payx_card;
	var   $m_ordr_data;
	var   $m_rcvr_data;
	var   $m_escw_data;
	var   $m_modx_data;
	var   $m_encx_data;
	var   $m_encx_info;

	/* -------------------------------------------------------------------- */
	/* -   처리 결과 값                                                   - */
	/* -------------------------------------------------------------------- */
	var   $m_res_data;
	var   $m_res_cd;
	var   $m_res_msg;

	/* -------------------------------------------------------------------- */
	/* -   생성자                                                         - */
	/* -------------------------------------------------------------------- */
	function  C_PP_CLI() {
		$this->m_payx_common = "";
		$this->m_payx_card   = "";
		$this->m_ordr_data   = "";
		$this->m_rcvr_data   = "";
		$this->m_escw_data   = "";
		$this->m_modx_data   = "";
		$this->m_encx_data   = "";
		$this->m_encx_info   = "";
	}

	function  mf_init($mode) {
		if ( $mode == "1" ) {
			if ( !extension_loaded( 'pp_cli_dl_php' ) ) {
				dl( "pp_cli_dl_php.so" );
			}
		}
	}

	function  mf_clear() {
		$this->m_payx_common = "";
		$this->m_payx_card   = "";
		$this->m_ordr_data   = "";
		$this->m_rcvr_data   = "";
		$this->m_escw_data   = "";
		$this->m_modx_data   = "";
		$this->m_encx_data   = "";
		$this->m_encx_info   = "";
	}

	function  mf_gen_trace_no( $site_cd, $ip, $mode ) {
		if ( $mode == "1" ) {
			$trace_no = lfPP_CLI_DL__gen_trace_no( $site_cd, $ip );
		} else {
			$trace_no = "";
		}

		return  $trace_no;
	}

	/* -------------------------------------------------------------------- */
	/* -   FUNC  :  ENC DATA 정보 설정 함수                               - */
	/* -------------------------------------------------------------------- */
	function  mf_set_payx_common_data( $name, $val ) {
		if ( $val != "" ) {
			$this->m_payx_common .= ( $name . '=' . $val . chr( 31 ) );
		}
	}

	function  mf_set_payx_card_data( $name, $val ) {
		if ( $val != "" ) {
			$this->m_payx_card .= ( $name . '=' . $val . chr( 31 ) );
		}
	}

	function  mf_set_ordr_data( $name, $val ) {
		if ( $val != "" ) {
			$this->m_ordr_data .= ( $name . '=' . $val . chr( 31 ) );
		}
	}

	function  mf_set_rcvr_data( $name, $val ) {
		if ( $val != "" ) {
			$this->m_rcvr_data .= ( $name . '=' . $val . chr( 31 ) );
		}
	}

	function  mf_set_escw_data( $name, $val ) {
		if ( $val != "" ) {
			$this->m_escw_data .= ( $name . '=' . $val . chr( 29 ) );
		}
	}

	function  mf_set_modx_data( $name, $val ) {
		if ( $val != "" ) {
			$this->m_modx_data .= ( $name . '=' . $val . chr( 31 ) );
		}
	}

	function  mf_set_encx_data( $encx_data, $encx_info ) {
		$this->m_encx_data = $encx_data;
		$this->m_encx_info = $encx_info;
	}

	/* -------------------------------------------------------------------- */
	/* -   FUNC  :  지불 처리 함수                                        - */
	/* -------------------------------------------------------------------- */
	function  mf_do_tx( $trace_no,  $home_dir, $site_cd,
	$site_key,  $tx_cd,    $pub_key_str,
	$pa_url,    $pa_port,  $user_agent,
	$ordr_idxx, $cust_ip,
	$log_level, $opt, $mode )
	{
		$payx_data = $this->mf_get_payx_data();

		$ordr_data = $this->mf_get_data( "ordr_data", $this->m_ordr_data );
		$rcvr_data = $this->mf_get_data( "rcvr_data", $this->m_rcvr_data );
		$escw_data = $this->mf_get_data( "escw_data", $this->m_escw_data );
		$modx_data = $this->mf_get_data( "mod_data",  $this->m_modx_data );

		if ( $mode == "1" ) {
			$res_data = lfPP_CLI_DL__do_tx_2( $trace_no, $home_dir, $site_cd,
			$site_key, $tx_cd,    $pub_key_str,
			$pa_url,   $pa_port,  $user_agent,
			$ordr_idxx,
			$payx_data, $ordr_data,
			$rcvr_data, $escw_data,
			$modx_data,
			$this->m_encx_data, $this->m_encx_info,
			$log_level, $opt );
		} else {
			$res_data = $this->mf_exec( $home_dir . "/bin/pp_cli",
			"-h",
			"home="      . $home_dir          . "," .
			"site_cd="   . $site_cd           . "," .
			"site_key="  . $site_key          . "," .
			"tx_cd="     . $tx_cd             . "," .
			"pa_url="    . $pa_url            . "," .
			"pa_port="   . $pa_port           . "," .
			"ordr_idxx=" . $ordr_idxx         . "," .
			"payx_data=" . $payx_data         . "," .
			"ordr_data=" . $ordr_data         . "," .
			"rcvr_data=" . $rcvr_data         . "," .
			"escw_data=" . $escw_data         . "," .
			"modx_data=" . $modx_data         . "," .
			"enc_data="  . $this->m_encx_data . "," .
			"enc_info="  . $this->m_encx_info . "," .
			"trace_no="  . $trace_no          . "," .
			"cust_ip="   . $cust_ip           . "," .
			"log_level=" . $log_level         . "," .
			"opt="       . $opt               . "" );
			if ( $res_data == "" ) {
				$res_data = "res_cd=9502" . chr( 31 ) . "res_msg=연동 모듈 호출 오류";
			} else{ 
				$res_data = iconv("EUC-KR","UTF-8",$res_data); 
			}
		}
		parse_str( str_replace( chr( 31 ), "&", $res_data ), $this->m_res_data );
		$this->m_res_cd  = $this->m_res_data[ "res_cd"  ];
		$this->m_res_msg = $this->m_res_data[ "res_msg" ];
	}

	/* -------------------------------------------------------------------- */
	/* -   FUNC  :  처리 결과 값을 리턴하는 함수                           - */
	/* -------------------------------------------------------------------- */
	function  mf_get_res_data( $name ) {
		return  $this->m_res_data[ $name ];
	}

	function  mf_get_payx_data() {
		if ( $this->m_payx_common != "" || $this->m_payx_card != "" ) {
			$my_data  = "payx_data=";
		}
		if ( $this->m_payx_common != "" ) {
			$my_data .= "common=" . $this->m_payx_common . chr( 30 );
		}
		if ( $this->m_payx_card != "" ) {
			$my_data .= ( "card=" . $this->m_payx_card   . chr( 30 ) );
		}
		return  $my_data;
	}

	function  mf_get_data( $data_name, $data ) {
		if ( $data != "" ) {
			$my_data = $data_name . "=" . $data;
		} else {
			$my_data = "";
		}
		return  $my_data;
	}

	function  mf_exec() {
		//한글 깨짐방지 추가
		setlocale(LC_CTYPE, "korean");
		$arg = func_get_args();

		if ( is_array( $arg[0] ) )  $arg = $arg[0];

		$exec_cmd = array_shift( $arg );

		while ( list(,$i) = each($arg) ) {
			$exec_cmd .= " " . escapeshellarg( $i );
		}
		$rt = exec( $exec_cmd );
		return  $rt;
	}
}
