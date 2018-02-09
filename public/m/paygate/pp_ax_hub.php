<?php
header("Content-Type: text/html; charset=UTF-8");
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once "cfg/site_conf_inc.php";
    require_once "pp_ax_hub_lib.php";
	$savetemp = "";
	 /* ============================================================================== */
    /* =   01. 지불 요청 정보 설정                                                  = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx         = $_POST[ "req_tx"         ]; // 요청 종류
    $tran_cd        = $_POST[ "tran_cd"        ]; // 처리 종류
    /* = -------------------------------------------------------------------------- = */
    $cust_ip        = getenv( "REMOTE_ADDR"    ); // 요청 IP
    $ordr_idxx      = $_POST[ "ordr_idxx"      ]; // 쇼핑몰 주문번호
    $good_name      = $_POST[ "good_name"      ]; // 상품명

    $good_mny       = $_POST[ "good_mny"       ]; // 결제 총금액
    /* = -------------------------------------------------------------------------- = */
    $res_cd         = "";                         // 응답코드
    $res_msg        = "";                         // 응답메시지
    $tno            = $_POST[ "tno"            ]; // KCP 거래 고유 번호
    /* = -------------------------------------------------------------------------- = */
    $buyr_name      = $_POST[ "buyr_name"      ]; // 주문자명
    $buyr_tel1      = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호
    $buyr_tel2      = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호
    $buyr_mail      = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소
    /* = -------------------------------------------------------------------------- = */
    $mod_type       = $_POST[ "mod_type"       ]; // 변경TYPE VALUE 승인취소시 필요
    $mod_desc       = $_POST[ "mod_desc"       ]; // 변경사유
    $panc_mod_mny   = "";                         // 부분취소 금액
    $panc_rem_mny   = "";                         // 부분취소 가능 금액
    $mod_tax_mny    = $_POST[ "mod_tax_mny"    ]; // 공급가 부분 취소 요청 금액
    $mod_vat_mny    = $_POST[ "mod_vat_mny"    ]; // 부과세 부분 취소 요청 금액
    $mod_free_mny   = $_POST[ "mod_free_mny"   ]; // 비과세 부분 취소 요청 금액
	

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


    /* = -------------------------------------------------------------------------- = */
    $use_pay_method = $_POST[ "use_pay_method" ]; // 결제 방법
    $bSucc          = "";                         // 업체 DB 처리 성공 여부
    /* = -------------------------------------------------------------------------- = */
    $app_time       = "";                         // 승인시간 (모든 결제 수단 공통)
    $amount         = "";                         // KCP 실제 거래 금액
    /* = -------------------------------------------------------------------------- = */
    $card_cd        = "";                         // 신용카드 코드
    $card_name      = "";                         // 신용카드 명
    $app_no         = "";                         // 신용카드 승인번호
    $noinf          = "";                         // 신용카드 무이자 여부
    $quota          = "";                         // 신용카드 할부개월
    $partcanc_yn    = "";                         // 부분취소 가능유무
    $card_bin_type_01 = "";                       // 카드구분1
    $card_bin_type_02 = "";                       // 카드구분2
    /* = -------------------------------------------------------------------------- = */
    $bank_name      = "";                         // 은행명
    $bank_code      = "";                         // 은행코드
    /* = -------------------------------------------------------------------------- = */
    $bankname       = "";                         // 입금할 은행명
    $depositor      = "";                         // 입금할 계좌 예금주 성명
    $account        = "";                         // 입금할 계좌 번호
    $va_date        = "";                         // 가상계좌 입금마감시간
    /* = -------------------------------------------------------------------------- = */
    $pnt_issue      = "";                         // 결제 포인트사 코드
    $pnt_amount     = "";                         // 적립금액 or 사용금액
    $pnt_app_time   = "";                         // 승인시간
    $pnt_app_no     = "";                         // 승인번호
    $add_pnt        = "";                         // 발생 포인트
    $use_pnt        = "";                         // 사용가능 포인트
    $rsv_pnt        = "";                         // 적립 포인트
    /* = -------------------------------------------------------------------------- = */
    $commid         = "";                         // 통신사 코드
    $mobile_no      = "";                         // 휴대폰 번호
    $van_cd         = "";
    /* = -------------------------------------------------------------------------- = */
    $tk_van_code    = "";                         // 발급사 코드
    $tk_app_no      = "";                         // 상품권 승인 번호
    /* = -------------------------------------------------------------------------- = */
    $cash_yn        = $_POST[ "cash_yn"        ]; // 현금영수증 등록 여부
    $cash_authno    = "";                         // 현금 영수증 승인 번호
    $cash_tr_code   = $_POST[ "cash_tr_code"   ]; // 현금 영수증 발행 구분
    $cash_id_info   = $_POST[ "cash_id_info"   ]; // 현금 영수증 등록 번호

    $param_opt_1    = $_POST[ "param_opt_1" ];
    $param_opt_2    = $_POST[ "param_opt_2" ];
    $param_opt_3    = $_POST[ "param_opt_3" ];

    # 결제 체크
    $paycode    = $param_opt_2; // 결제코드
    $basketidxs = $param_opt_3; // 장바구니 idxs
	//로그정보 남김
	$savetemp.= "PP_AX_HUB.PHP ==================================================================#".date('YmdHis')."\n";
	$savetemp.= " ordercode : ".$ordr_idxx." \n";
    $savetemp.= " param_opt_1 : ".$param_opt_1."\n";
    $savetemp.= " param_opt_2 : ".$param_opt_2."\n";
    $savetemp.= " param_opt_3 : ".$param_opt_3."\n";

	list($countOrderCode) = pmysql_fetch("SELECT count(ordercode) FROM tblorderinfo WHERE ordercode='{$ordr_idxx}'");
	if(!$countOrderCode){
		if ( $bank_issu != "SCOB" ) {
			$c_PayPlus=new C_PP_CLI;
			############### 승인요청 ###################
			if($req_tx=="pay") {
				$c_PayPlus->mf_set_encx_data($_POST["enc_data"] , $_POST["enc_info"]);
			}

			############## 실행 ########################
			if($tran_cd!="" ) {
				$c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
									  $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
									  $cust_ip, $g_conf_log_level, 0, 0 ); // 응답 전문 처리		
				$tno=$c_PayPlus->mf_get_res_data("tno");
			} else {
				$c_PayPlus->m_res_cd="9562";
				$c_PayPlus->m_res_msg="연동 오류";
			}

			$res_cd = $c_PayPlus->m_res_cd;
			$res_msg = $c_PayPlus->m_res_msg;

			$bSucc=true;

			################# 승인결과처리 #################
			if ($req_tx=="pay") {
				if( $res_cd=="0000") {
					################## 05-1. 신용카드 승인 결과 처리 #############
					if ($use_pay_method=="100000000000") {
						$card_cd          = $c_PayPlus->mf_get_res_data( "card_cd"   );  // 카드 코드
						$card_name        = $c_PayPlus->mf_get_res_data( "card_name" );  // 카드 종류
						$app_time         = $c_PayPlus->mf_get_res_data( "app_time"  );  // 승인 시간
						$app_no           = $c_PayPlus->mf_get_res_data( "app_no"    );  // 승인 번호
						$noinf            = $c_PayPlus->mf_get_res_data( "noinf"     );  // 무이자 여부 ( 'Y' : 무이자 )
						$quota            = $c_PayPlus->mf_get_res_data( "quota"     );  // 할부 개월
					}

					/* = -------------------------------------------------------------------------- = */
					/* =   05-1-1. 금융결제원 계좌이체 승인 결과 처리                               = */
					/* = -------------------------------------------------------------------------- = */
					if ($use_pay_method == "010000000000" ) {
						$bank_name        = $c_PayPlus->mf_get_res_data( "bank_name" );  // 이체한 은행 이름
					}

					################# 05-2. 가상계좌 승인 결과 처리 #############
					if ($use_pay_method=="001000000000") {
						$bankname         = $c_PayPlus->mf_get_res_data( "bankname"  );  // 입금할 은행 이름
						$depositor        = $c_PayPlus->mf_get_res_data( "depositor" );  // 입금할 계좌 예금주
						$account          = $c_PayPlus->mf_get_res_data( "account"   );  // 입금할 계좌 번호
					}

					################# 05-3. 휴대폰 승인 결과 처리 ###############
					if ($use_pay_method=="000010000000") {
						$app_time         = $c_PayPlus->mf_get_res_data( "hp_app_time"  );  // 승인 시간
					}
				}
			}
		} else {	########## 08. 계좌이체 결과 처리 (전문통신을 하지 않는 경우) ##############
			$res_cd    = $_POST["res_cd"];	// 응답코드
			$res_msg   = $_POST["res_msg"];	// 응답메시지
			
			if ($use_pay_method=="010000000000") {
				$bank_name=$_POST[ "bank_name" ];	// 은행명
			}
		}

		if(strlen(RootPath)>0) {
			$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
			$pathnum=@strpos($hostscript,RootPath);
			$shopurl=substr($hostscript,0,$pathnum).RootPath;
		} else {
			$shopurl=$_SERVER['HTTP_HOST']."/";
		}

		$return_host=$_SERVER['HTTP_HOST'];
		$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
		$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$ordr_idxx."&paycode=".$paycode;

		$isreload=false;
		$tblname="";
		$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordr_idxx."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$paymethod=$row->paymethod;
			if(strstr("CP", $paymethod)) $tblname="tblpcardlog";
			else if(strstr("OQ", $paymethod)) $tblname="tblpvirtuallog";
			else if($paymethod=="M") $tblname="tblpmobilelog";
			else if($paymethod=="V") $tblname="tblptranslog";
		}
		pmysql_free_result($result);

		if(strlen($tblname)>0) {
			$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordr_idxx."' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$isreload=true;
				$pay_data=$row->pay_data;
				$good_mny = $row->price;
				if ($row->ok=="Y") {
					$PAY_GLAG="0000";
					$DELI_GBN="N";
				} else if ($row->ok=="N") {
					$PAY_FLAG="9999";
					$DELI_GBN="C";
				}
				if(strstr("CP", $paymethod)) $PAY_AUTH_NO = "00000000";
			}
			pmysql_free_result($result);
		}


		if ( $bank_issu != "SCOB" ) {
			if ($req_tx == "pay") {    // 거래 구분 : 승인
				if ($bSucc) {
					if($isreload!=true) {
						$date=date("YmdHis");
						if ($res_cd == "0000") {	//정상승인
							$PAY_FLAG="0000";
							$DELI_GBN="N";
							$MSG1=$res_msg;
							$pay_data=$res_msg;
							$ok="Y";
							if ($use_pay_method == "100000000000") {	//신용카드
								$tblname="tblpcardlog";
								$paymethod="C";
								if($pay_mod=="Y") $paymethod="P";
								$PAY_AUTH_NO=$app_no;
								$MSG1="정상승인 - 승인번호 : ".$PAY_AUTH_NO;
								$pay_data="승인번호 : ".$app_no."";
							} else if ($use_pay_method == "010000000000") {	//계좌이체
								$tblname="tblptranslog";
								$paymethod="V";
								$PAY_AUTH_NO="";
								$card_name="";
								$noinf="";
								$quota="";
							} else if ($use_pay_method == "001000000000") { //가상계좌
								$ok="M";
								$tblname="tblpvirtuallog";
								$paymethod="O";
								if($pay_mod=="Y") $paymethod="Q";
								$PAY_AUTH_NO="";
								$card_name="";
								$noinf="";
								$quota="";
								$pay_data=$bankname." ".$account." (예금주:".$depositor.")";
							} else if ($use_pay_method == "000010000000") { //휴대폰
								$tblname="tblpmobilelog";
								$paymethod="M";
								$PAY_AUTH_NO="";
								$card_name="";
								$noinf="";
								$quota="";
							}
							$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
							pmysql_query($sql,get_db_conn());
							//backup_save_sql($sql);
							/*
							$sql = "INSERT ".$tblname." SET ";
							$sql.= "ordercode		= '".$ordr_idxx."', ";
							$sql.= "trans_code		= '".$tno."', ";
							$sql.= "pay_data		= '".$pay_data."', ";
							$sql.= "pgtype			= 'A', ";
							$sql.= "ok				= '".$ok."', ";
							$sql.= "okdate			= '".$date."', ";
							$sql.= "price			= '".$good_mny."', ";
							if ($use_pay_method == "100000000000") {		//신용카드
								$sql.= "status			= 'N', ";
								$sql.= "paymethod		= '".$paymethod."', ";
								$sql.= "edidate			= '".$date."', ";
								$sql.= "cardname		= '".$card_name."', ";
								$sql.= "noinf			= '".$noinf."', ";
								$sql.= "quota			= '".$quota."', ";
							} else if($use_pay_method == "010000000000") {	//계좌이체
								$sql.= "bank_name		= '".$bank_name."', ";
							} else if($use_pay_method=="001000000000") {	//가상계좌
								$sql.= "status			= 'N', ";
								$sql.= "paymethod		= '".$paymethod."', ";
								$sql.= "sender_name		= '".$buyr_name."', ";
								$sql.= "account			= '".$account."', ";
							} else if ($use_pay_method == "000010000000") { //휴대폰

							}
							$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
							$sql.= "goodname		= '".$good_name."', ";
							$sql.= "msg				= '".$MSG1."' ";
							*/


							if ($use_pay_method == "100000000000") {//신용카드
								$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota";
								$addQueryVal = ", 'N', '".$paymethod."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."'";
							} else if($use_pay_method == "010000000000") {//계좌이체
								$addQueryCol = ", bank_name";
								$addQueryVal = ", '".$bank_name."'";
							} else if($use_pay_method=="001000000000") {//가상계좌
								$addQueryCol = ", status, paymethod, sender_name, account";
								$addQueryVal = ", 'N', '".$paymethod."', '".$buyr_name."', '".$account."'";
							} else if ($use_pay_method == "000010000000") {//휴대폰
							}
							$sql = "
										INSERT INTO ".$tblname." 
										(
											ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."
										)
										VALUES
										(
											'".$ordr_idxx."', '".$tno."', '".$pay_data."', 'A', '".$ok."', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."
										)
							";
							pmysql_query($sql,get_db_conn());
							//backup_save_sql($sql);
							# 거래승인 로그
							$savetemp.= "정상승인 성공 \n";
							$savetemp.= "SQL : ".$sql."\n";
							$savetemp.= "bSucc : ".$bSucc."\n";
						} else {	//승인실패
							$PAY_FLAG="9999";
							$DELI_GBN="C";
							$MSG1=$res_msg;
							$PAY_AUTH_NO="";
							$pay_data=$res_msg;
							if ($use_pay_method == "100000000000") {	//신용카드
								$tblname="tblpcardlog";
								$paymethod="C";
								if($pay_mod=="Y") $paymethod="P";
							} else if ($use_pay_method == "010000000000") {	//계좌이체
								$tblname="tblptranslog";
								$paymethod="V";
								$card_name="";
								$noinf="";
								$quota="";
							} else if ($use_pay_method == "001000000000") { //가상계좌
								$tblname="tblpvirtuallog";
								$paymethod="O";
								if($pay_mod=="Y") $paymethod="Q";
								$card_name="";
								$noinf="";
								$quota="";
							} else if ($use_pay_method == "000010000000") { //휴대폰
								$tblname="tblpmobilelog";
								$paymethod="M";
								$card_name="";
								$noinf="";
								$quota="";
							}

							$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
							pmysql_query($sql,get_db_conn());
							//backup_save_sql($sql);
							/*
							$sql = "INSERT ".$tblname." SET ";
							$sql.= "ordercode		= '".$ordr_idxx."', ";
							$sql.= "trans_code		= '".$tno."', ";
							$sql.= "pay_data		= 'ERROR', ";
							$sql.= "pgtype			= 'A', ";
							$sql.= "ok				= 'N', ";
							$sql.= "okdate			= '".$date."', ";
							$sql.= "price			= '".$good_mny."', ";
							if ($use_pay_method == "100000000000") {		//신용카드
								$sql.= "status			= 'N', ";
								$sql.= "paymethod		= '".$paymethod."', ";
								$sql.= "edidate			= '".$date."', ";
								$sql.= "cardname		= '".$card_name."', ";
								$sql.= "noinf			= '".$noinf."', ";
								$sql.= "quota			= '".$quota."', ";
							} else if($use_pay_method == "010000000000") {	//계좌이체
								$sql.= "bank_name		= '".$bank_name."', ";
							} else if($use_pay_method=="001000000000") {	//가상계좌
								$sql.= "status			= 'N', ";
								$sql.= "paymethod		= '".$paymethod."', ";
								$sql.= "sender_name		= '".$buyr_name."', ";
								$sql.= "account			= '".$account."', ";
							} else if ($use_pay_method == "000010000000") { //휴대폰

							}
							$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
							$sql.= "goodname		= '".$good_name."', ";
							$sql.= "msg				= '".$MSG1."' ";
							*/


							if ($use_pay_method == "100000000000") {//신용카드
								$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota";
								$addQueryVal = ", 'N', '".$paymethod."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."'";
							} else if($use_pay_method == "010000000000") {//계좌이체
								$addQueryCol = ", bank_name";
								$addQueryVal = ", '".$bank_name."'";
							} else if($use_pay_method=="001000000000") {//가상계좌
								$addQueryCol = ", status, paymethod, sender_name, account";
								$addQueryVal = ", 'N', '".$paymethod."', '".$buyr_name."', '".$account."'";
							} else if ($use_pay_method == "000010000000") {//휴대폰
							}
							$sql = "
										INSERT INTO ".$tblname." 
										(
											ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."									
										)
										VALUES
										(
											'".$ordr_idxx."', '".$tno."', 'ERROR', 'A', 'N', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."						
										)
							";
							pmysql_query($sql,get_db_conn());
							//backup_save_sql($sql);
							#실패
							$savetemp.= " 정상승인 실패 \n";
							$savetemp.= " SQL : ".$sql." \n";
							$savetemp.= " bSucc : ".$bSucc."\n";
						}
					}			
					$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
                    $return_data .= "&paycode=".$paycode."&basketidxs=".$basketidxs;
					$return_data2=str_replace("'","",$return_data);
					$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
					pmysql_query($sql,get_db_conn());
					//backup_save_sql($sql);

					$temp = SendSocketPost($return_host,$return_script,$return_data);
					if( trim( $temp ) != "ok" ) {
						//error (메일 발송)
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
						}
                        $bSucc = false;
					} else {
						//pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
					}
				}
			}
		} else {
			if($isreload!=true) {
				$date=date("YmdHis");
				if ($res_cd == "0000") {	//정상승인
					$PAY_FLAG="0000";
					$DELI_GBN="N";
					$MSG1=$res_msg;
					$pay_data=$res_msg;
					$ok="Y";
					if ($use_pay_method == "010000000000") {	//계좌이체
						$tblname="tblptranslog";
						$paymethod="V";
						$PAY_AUTH_NO="";
						$card_name="";
						$noinf="";
						$quota="";

						$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
						pmysql_query($sql,get_db_conn());
						//backup_save_sql($sql);
						/*
						$sql = "INSERT ".$tblname." SET ";
						$sql.= "ordercode		= '".$ordr_idxx."', ";
						$sql.= "trans_code		= '".$tno."', ";
						$sql.= "pay_data		= '".$pay_data."', ";
						$sql.= "pgtype			= 'A', ";
						$sql.= "ok				= '".$ok."', ";
						$sql.= "okdate			= '".$date."', ";
						$sql.= "price			= '".$good_mny."', ";
						$sql.= "bank_name		= '".$bank_name."', ";
						$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
						$sql.= "goodname		= '".$good_name."', ";
						$sql.= "msg				= '".$MSG1."' ";
						*/

						$sql = "
									INSERT INTO ".$tblname." 
									(
										ordercode, trans_code, pay_data, pgtype, ok, okdate, price, bank_name, ip, goodname, msg
									)
									VALUES
									(
										'".$ordr_idxx."', '".$tno."', '".$pay_data."', 'A', '".$ok."', '".$date."', '".$good_mny."', '".$bank_name."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."'
									)
						";
						pmysql_query($sql,get_db_conn());
						//backup_save_sql($sql);
					}			
				} else {	//승인실패
					$PAY_FLAG="9999";
					$DELI_GBN="C";
					$MSG1=$res_msg;
					$PAY_AUTH_NO="";
					$pay_data=$res_msg;
					if ($use_pay_method == "010000000000") {	//계좌이체
						$tblname="tblptranslog";
						$paymethod="V";
						$card_name="";
						$noinf="";
						$quota="";

						$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
						pmysql_query($sql,get_db_conn());
						//backup_save_sql($sql);
						/*
						$sql = "INSERT ".$tblname." SET ";
						$sql.= "ordercode		= '".$ordr_idxx."', ";
						$sql.= "trans_code		= '".$tno."', ";
						$sql.= "pay_data		= 'ERROR', ";
						$sql.= "pgtype			= 'A', ";
						$sql.= "ok				= 'N', ";
						$sql.= "okdate			= '".$date."', ";
						$sql.= "price			= '".$good_mny."', ";
						$sql.= "bank_name		= '".$bank_name."', ";
						$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
						$sql.= "goodname		= '".$good_name."', ";
						$sql.= "msg				= '".$MSG1."' ";
						*/

						$sql = "
									INSERT INTO ".$tblname." 
									(
										ordercode, trans_code, pay_data, pgtype, ok, okdate, price, bank_name, ip, goodname, msg
									)
									VALUES
									(
										'".$ordr_idxx."', '".$tno."', 'ERROR', 'A', 'N', '".$date."', '".$good_mny."', '".$bank_name."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."'
									)
						";
						pmysql_query($sql,get_db_conn());
						//backup_save_sql($sql);
						$savetemp.= " 정상승인 실패 ";
						$savetemp.= " SQL : ".$sql."\n";
						$savetemp.= " bSucc : ";
					}
				}
			}

			$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
            $return_data .= "&paycode=".$paycode."&basketidxs=".$basketidxs;
			$return_data2=str_replace("'","",$return_data);
			$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);

			$temp = SendSocketPost($return_host,$return_script,$return_data);
			
			if( trim( $temp ) != "ok" ) {
				//error (메일 발송)
				if(strlen(AdminMail)>0) {
					@mail(AdminMail,"[PG] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
				}
                $bSucc = false;
			} else {
				//pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
			}
		}

        if (  $req_tx == "pay" )
        {
            if( $res_cd == "0000" )
            {
                if ( $bSucc === false ) // $bSucc == "false"
                {
                    $c_PayPlus->mf_clear();

                    $tran_cd = "00200000";

                    $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                    $c_PayPlus->mf_set_modx_data( "mod_type", "STSC"                       );  // 원거래 변경 요청 종류
                    $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
                    $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유
                    /*
                    $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "", 
                                  $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx, 
                                  $cust_ip, $g_conf_log_level, 0, $g_conf_mode );
                    */
                    $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                      $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                      $cust_ip, $g_conf_log_level, 0, 0 ); // 응답 전문 처리
                    $res_cd  = $c_PayPlus->m_res_cd;
                    $res_msg = $c_PayPlus->m_res_msg;
                    $msg     = $res_msg." ( db error) ";
                    //$PAY_FLAG = '9999';
                    $fc = fopen("./log/kcp_cancel_result_".date("Ymd").".txt","a+");
                    fwrite($fc,"date : ".date("Y-m-d H:i:s")."\r\n");
                    fwrite($fc,"ordercode : ".$ordr_idxx."\r\n");
                    fwrite($fc,"tran_cd : ".$tran_cd."\r\n");
                    fwrite($fc,"bank_issu : ".$bank_issu."\r\n");
                    fwrite($fc,"req_tx : ".$req_tx."\r\n");
                    fwrite($fc,"bSucc : ".$bSucc."\r\n");
                    fwrite($fc,"trans_code : ".$tno."\r\n");
                    fwrite($fc,"res_cd : ".$res_cd."\r\n");
                    fwrite($fc,"res_msg : ".$res_msg."\r\n");
                    fwrite($fc,"isreload : ".$isreload."\r\n");
                    fwrite($fc,"use_pay_method : ".$use_pay_method."\r\n");
                    fwrite($fc,"결제코드 : ".$paycode."\r\n");
                    fwrite($fc,"장바구니idx : ".$basketidxs."\r\n");
                    fwrite($fc,"----------------------------------\r\n");
                    fclose( $fc );
                    chmod("./log/kcp_cancel_result_".date("Ymd").".txt",0777);

                    /*
                    $return_data  = "ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG;
                    $return_data .= "&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$msg;
                    $return_data2 = str_replace("'","",$return_data);
                    $returndataSql = "UPDATE SET return_data = '".$return_data2."' WHERE ordercode = '".$ordr_idxx."' ";
                    pmysql_query( $returndataSql, get_db_conn() );
                    */
                    $sql = "UPDATE ".$tblname." SET ok = 'C', msg = msg||' >> 결과 처리 오류 - 자동 취소' ";
                    $sql.= "WHERE ordercode ='".$ordr_idxx."' AND trans_code='".$tno."' ";
                    pmysql_query( $sql, get_db_conn() );
                }
            }
        } // End of [res_cd = "0000"]
/*
        // 원래 모바일에서 결제 후 아래 페이지로 이동
		$return_result_url = "../payresult.php?ordercode=".$ordr_idxx;
		$return_fail_url = "../basket.php";
*/
        // 결제 후 다시 pc화면으로 이동
        $mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
        if( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && $param_opt_1 == '/m/order.php' ){
            $return_result_url = "/front/payresult.php?ordercode=".$ordr_idxx.'&mobile_path='.$param_opt_1;
            $return_fail_url = "/m/basket.php";
        } else {
            $return_result_url = "/front/payresult.php?ordercode=".$ordr_idxx.'&mobile_path='.$param_opt_1;
            $return_fail_url = "/front/basket.php";
        }
		//$return_result_url = "/m/payresult.php?ordercode=".$ordr_idxx;
		//$return_fail_url = "/m/basket.php";
		$savetemp.= " result_url : ".$return_result_url."\n";
		$savetemp.= " return_fail_url : ".$return_fail_url."\n";
		/*
		$f = fopen("111111111.txt","a+");
		fwrite($f, $return_fail_url."||||||||||||||||||".$return_result_url."\r\n\r\n");
		fwrite($f, $req_tx."||||||||||||||||||".$res_cd."\r\n\r\n");
		fwrite($f, $res_msg."||||||||||||||||||".$use_pay_method."\r\n\r\n");
		fclose($f);
		chmod("111111111.txt",0777);
		*/
		/*
		if($res && $req_tx == "pay"){
			go($return_result_url, "parent");
		}else if($req_tx == "pay" && !$res) {
			msg("결제에 실패 했습니다.");
			go($return_fail_url, "parent");
		}else if ( $req_tx == "mod" ){
			echo("<script>opener.location.reload();self.close();</script>");
			exit;
		}*/

/*
		$f = fopen("./kcpTest_".date("Y-m-d").".txt","a+");
		fwrite($f,"=====================================================================\r\n");
		fwrite($f,"req_tx : ".$req_tx."\r\n");
		fwrite($f,"res_cd : ".$res_cd."\r\n");
		fwrite($f,"bank_issu : ".$bank_issu."\r\n");
		fwrite($f,"bSucc : ".$bSucc."\r\n");
		fwrite($f,"temp : ".$temp."\r\n");
		fwrite($f,"return_host : ".$return_host."\r\n");
		fwrite($f,"return_script : ".$return_script."\r\n");
		fwrite($f,"return_data : ".$return_data."\r\n");
		fwrite($f,"ordr_idxx : ".$ordr_idxx."\r\n");
		fwrite($f,"=====================================================================\r\n");
		fclose($f);
		chmod("./kcpTest_".date("Y-m-d").".txt",0777);
*/
		$savetemp.= " temp : ".trim( $temp )."\n";
		$file = DirPath.DataDir."backup/mobile_paygate_".date("Y")."_".date("m")."_".date("d").".txt";
		if(!is_file($file)){
			$f = fopen($file,"a+");
			fclose($f);
			chmod($file,0777);
		}
		file_put_contents( $file, $savetemp, FILE_APPEND );

		if( trim( $temp ) != "ok" ) {
			msg("결제를 취소 하셨습니다.");
            /*
			list($tempkey)=pmysql_fetch("SELECT tempkey FROM tblorderproducttemp WHERE ordercode='".$ordr_idxx."'");
			if($tempkey){
				$itemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE tempkey='".$tempkey."'";
				pmysql_query($itemQuery);
			}
            */
			//go($return_fail_url, "opener.parent");
            echo "<script>";
            if( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && $param_opt_1 == '/m/order.php' ){
                echo "  window.location.replace('".$return_fail_url."'); ";
            } else {
                echo "  opener.parent.location.replace('".$return_fail_url."'); ";
                echo "  window.close(); ";
            }
            echo "</script>";
            exit;
		}else{
			//go($return_result_url, "opener.parent");
            echo "<script>";
            //echo "  window.location.replace('".$return_result_url."'); ";
            //echo "  window.close(); ";
            if( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && $param_opt_1 == '/m/order.php' ){
                echo "  window.location.replace('".$return_result_url."'); ";
            } else {
                echo "  opener.parent.location.replace('".$return_result_url."'); ";
                echo "  window.close(); ";
            }
            echo "</script>";
			exit;
		}
	}
